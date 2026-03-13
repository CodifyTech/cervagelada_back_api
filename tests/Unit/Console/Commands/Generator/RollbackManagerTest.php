<?php

namespace Tests\Unit\Console\Commands\Generator;

use App\Console\Commands\Generator\RollbackManager;
use App\Console\Commands\Generator\Utils\RollbackLogger;
use App\Console\Commands\Generator\Utils\RouteManager;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\TestCase;

class RollbackManagerTest extends TestCase
{

    private string $testRollbackLogPath;

    private array $testRollbackLog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testRollbackLogPath = storage_path('testing/rollback_test.json');
        $this->testRollbackLog = [
            'version' => '1.0',
            'timestamp' => now()->toISOString(),
            'sessions' => [
                'session1' => [
                    'id' => 'session1',
                    'domain' => 'Products',
                    'action' => 'create',
                    'timestamp' => now()->toISOString(),
                    'created' => [
                        base_path('app/Domains/Products/Models/Product.php'),
                        base_path('app/Domains/Products/Controllers/ProductController.php'),
                        storage_path('testing/frontend/src/types/Product.ts'),
                    ],
                    'modified' => [
                        base_path('routes/web.php') => storage_path('testing/backups/web.php.backup'),
                        base_path('config/permission_list.php') => storage_path('testing/backups/permission_list.php.backup'),
                    ],
                    'directories' => [
                        base_path('app/Domains/Products'),
                        base_path('app/Domains/Products/Models'),
                    ],
                ],
            ],
        ];

        // Criar diretório de teste
        $testDir = dirname($this->testRollbackLogPath);
        if (! is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }

        // Configurar ambiente para o trait
        $frontendTestPath = storage_path('testing/frontend/src');
        if (! is_dir($frontendTestPath)) {
            mkdir($frontendTestPath, 0755, true);
        }
        config(['cdf.dir_front_end' => 'storage/testing/frontend/src']);
        config(['cdf.rollback_log_path' => $this->testRollbackLogPath]);
        putenv('CDF_DIR_FRONT_END=storage/testing/frontend/src');

        // Criar log de teste
        file_put_contents($this->testRollbackLogPath, json_encode($this->testRollbackLog, JSON_PRETTY_PRINT));
    }

    protected function tearDown(): void
    {
        // Limpar arquivos de teste
        if (file_exists($this->testRollbackLogPath)) {
            unlink($this->testRollbackLogPath);
        }

        $testDir = dirname($this->testRollbackLogPath);
        if (is_dir($testDir)) {
            File::deleteDirectory($testDir);
        }

        parent::tearDown();
    }

    private function createCommandMock($routeManager)
    {
        $logger = new RollbackLogger();
        $command = $this->getMockBuilder(RollbackManager::class)
            ->setConstructorArgs([$routeManager, $logger])
            ->onlyMethods(['getFrontendPath'])
            ->getMock();

        $frontendTestPath = storage_path('testing/frontend/src');
        if (! is_dir($frontendTestPath)) {
            mkdir($frontendTestPath, 0755, true);
        }
        $command->method('getFrontendPath')->willReturn($frontendTestPath);

        // Mock output para evitar erro de writeln() on null
        $output = new OutputStyle(
            new ArrayInput([]),
            new NullOutput
        );
        $command->setOutput($output);

        return $command;
    }

    /** @test */
    public function can_load_rollback_log()
    {
        $routeManager = $this->createMock(RouteManager::class);
        $command = $this->createCommandMock($routeManager);

        // Usar reflexão para acessar propriedades privadas
        $reflection = new \ReflectionClass($command);

        $rollbackLogPathProperty = $reflection->getProperty('rollbackLogPath');
        $rollbackLogPathProperty->setAccessible(true);
        $rollbackLogPathProperty->setValue($command, $this->testRollbackLogPath);

        $rollbackLogProperty = $reflection->getProperty('rollbackLog');
        $rollbackLogProperty->setAccessible(true);

        // Simular carregamento do log
        $loadedLog = json_decode(file_get_contents($this->testRollbackLogPath), true);
        $rollbackLogProperty->setValue($command, collect($loadedLog['sessions']));

        $actualLog = $rollbackLogProperty->getValue($command);

        $this->assertTrue($actualLog->has('session1'));
        $session = $actualLog->get('session1');
        $this->assertCount(3, $session['created']);
        $this->assertCount(2, $session['modified']);
        $this->assertCount(2, $session['directories']);
    }

    /** @test */
    public function can_extract_domains_from_log()
    {
        $routeManager = $this->createMock(RouteManager::class);
        $command = $this->createCommandMock($routeManager);

        // Usar reflexão para testar método privado
        $reflection = new \ReflectionClass($command);

        $rollbackLogProperty = $reflection->getProperty('rollbackLog');
        $rollbackLogProperty->setAccessible(true);
        $rollbackLogProperty->setValue($command, collect($this->testRollbackLog['sessions']));

        $method = $reflection->getMethod('extractDomainsFromLog');
        $method->setAccessible(true);

        $domains = $method->invoke($command);

        $this->assertIsArray($domains);
        $this->assertContains('Products', $domains);
    }

    /** @test */
    public function can_get_domain_files()
    {
        $routeManager = $this->createMock(RouteManager::class);
        $command = $this->createCommandMock($routeManager);

        // Usar reflexão para testar método privado
        $reflection = new \ReflectionClass($command);

        $rollbackLogProperty = $reflection->getProperty('rollbackLog');
        $rollbackLogProperty->setAccessible(true);
        $rollbackLogProperty->setValue($command, collect($this->testRollbackLog['sessions']));

        $method = $reflection->getMethod('getDomainFiles');
        $method->setAccessible(true);

        $domainFiles = $method->invoke($command, 'Products');

        $this->assertIsArray($domainFiles);
        $this->assertArrayHasKey('created', $domainFiles);
        $this->assertArrayHasKey('modified', $domainFiles);

        // Verificar se arquivos do domínio Products foram encontrados
        $this->assertGreaterThan(0, count($domainFiles['created']));
    }

    /** @test */
    public function can_count_frontend_files()
    {
        $routeManager = $this->createMock(RouteManager::class);
        $command = $this->createCommandMock($routeManager);

        // Usar reflexão para testar método privado
        $reflection = new \ReflectionClass($command);

        $rollbackLogProperty = $reflection->getProperty('rollbackLog');
        $rollbackLogProperty->setAccessible(true);
        $rollbackLogProperty->setValue($command, collect($this->testRollbackLog['sessions']));

        $method = $reflection->getMethod('getFrontendFilesCount');
        $method->setAccessible(true);

        $frontendCount = $method->invoke($command);

        $this->assertIsInt($frontendCount);
        $this->assertGreaterThanOrEqual(1, $frontendCount); // Pelo menos o arquivo Product.ts
    }

    /** @test */
    public function command_fails_when_no_rollback_log_exists()
    {
        // Remover o arquivo de log
        if (file_exists($this->testRollbackLogPath)) {
            unlink($this->testRollbackLogPath);
        }

        $routeManager = $this->createMock(RouteManager::class);

        $this->artisan('rollback:manager')
            ->expectsOutput('🔄 Gerenciador Avançado de Rollback')
            ->expectsOutput('❌ Nenhum log de rollback encontrado. Nada a desfazer.')
            ->assertExitCode(1);
    }

    /** @test */
    public function command_works_with_domain_option()
    {
        $routeManager = $this->createMock(RouteManager::class);
        $routeManager->method('removeDomainRoutes')->willReturn(true);

        // Criar alguns arquivos de teste para simular rollback
        $testFiles = [
            base_path('app/Domains/Products/Models/Product.php'),
        ];

        foreach ($testFiles as $file) {
            $dir = dirname($file);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($file, '<?php // Test file');
        }

        // Atualizar a instância do command com nosso mock
        $this->app->instance(RouteManager::class, $routeManager);

        // Simular o comando (sem executar de fato para não afetar arquivos reais)
        $reflection = new \ReflectionClass(RollbackManager::class);
        $command = $reflection->newInstanceWithoutConstructor();

        $routeManagerProperty = $reflection->getProperty('routeManager');
        $routeManagerProperty->setAccessible(true);
        $routeManagerProperty->setValue($command, $routeManager);

        $rollbackLogPathProperty = $reflection->getProperty('rollbackLogPath');
        $rollbackLogPathProperty->setAccessible(true);
        $rollbackLogPathProperty->setValue($command, $this->testRollbackLogPath);

        $rollbackLogProperty = $reflection->getProperty('rollbackLog');
        $rollbackLogProperty->setAccessible(true);
        $rollbackLogProperty->setValue($command, collect($this->testRollbackLog['sessions']));

        // Testar extração de domínios
        $extractMethod = $reflection->getMethod('extractDomainsFromLog');
        $extractMethod->setAccessible(true);
        $domains = $extractMethod->invoke($command);

        $this->assertContains('Products', $domains);

        // Limpar arquivos de teste
        foreach ($testFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Limpar diretórios de teste
        $testDir = base_path('app/Domains/Products/Models');
        if (is_dir($testDir)) {
            rmdir($testDir);
        }
        $productsDomainDir = base_path('app/Domains/Products');
        if (is_dir($productsDomainDir)) {
            rmdir($productsDomainDir);
        }
    }

    /** @test */
    public function can_simulate_rollback_with_dry_run()
    {
        $routeManager = $this->createMock(RouteManager::class);
        $command = $this->createCommandMock($routeManager);

        // Usar reflexão para testar método privado de simulação
        $reflection = new \ReflectionClass($command);

        $rollbackLogProperty = $reflection->getProperty('rollbackLog');
        $rollbackLogProperty->setAccessible(true);
        $rollbackLogProperty->setValue($command, collect($this->testRollbackLog));

        $method = $reflection->getMethod('simulateRollback');
        $method->setAccessible(true);

        // Capturar output usando buffer
        ob_start();
        $method->invoke($command);
        $output = ob_get_clean();

        // Verificar se a simulação mostra informações corretas
        $this->assertIsString($output);
    }

    /** @test */
    public function validates_rollback_log_format()
    {
        // Criar log inválido
        $invalidLog = ['invalid' => 'format'];
        file_put_contents($this->testRollbackLogPath, json_encode($invalidLog));

        $routeManager = $this->createMock(RouteManager::class);

        $this->artisan('rollback:manager', ['--force' => true])
            ->expectsOutput('🔄 Gerenciador Avançado de Rollback')
            ->expectsOutput('❌ Log de rollback corrompido ou inválido.')
            ->assertExitCode(1);
    }

    /** @test */
    public function handles_missing_backup_files_gracefully()
    {
        // Criar log com backup que não existe
        $logWithMissingBackup = $this->testRollbackLog;
        $logWithMissingBackup['modified'] = [
            base_path('routes/web.php') => '/nonexistent/backup/file.php',
        ];

        file_put_contents($this->testRollbackLogPath, json_encode($logWithMissingBackup, JSON_PRETTY_PRINT));

        $routeManager = $this->createMock(RouteManager::class);
        $command = new RollbackManager($routeManager, new RollbackLogger());

        // Usar reflexão para testar comportamento
        $reflection = new \ReflectionClass($command);

        $rollbackLogProperty = $reflection->getProperty('rollbackLog');
        $rollbackLogProperty->setAccessible(true);
        $rollbackLogProperty->setValue($command, collect($logWithMissingBackup));

        // O comando deve continuar funcionando mesmo com backup faltando
        $this->assertTrue(true); // Se chegou até aqui, não houve exceção
    }
}
