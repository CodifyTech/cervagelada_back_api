<?php

namespace App\Console\Commands\Generator;

use App\Console\Commands\Generator\Utils\RollbackLogger;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class RollbackStatus extends Command
{
    protected $signature = 'rollback:status 
                            {--detailed : Mostrar detalhes de todas as sessões} 
                            {--session= : Mostrar detalhes de uma sessão específica} 
                            {--domain= : Filtrar sessões por domínio}
                            {--json : Retornar o status em formato JSON}
                            {--table : Mostrar o status em formato de tabela}';

    protected $description = 'Mostra o status atual do sistema de rollback';

    private RollbackLogger $logger;

    public function __construct(RollbackLogger $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    public function handle(): int
    {
        if ($this->option('json')) {
            $stats = [
                'statistics' => $this->logger->getStatistics(),
                'sessions' => $this->logger->getSessions(),
            ];

            $this->line(json_encode($stats));

            return 0;
        }

        // Mostrar sessão específica se solicitada (prioridade total)
        if ($sessionId = $this->option('session')) {
            return $this->showSessionDetails($sessionId);
        }

        // Filtrar por domínio se solicitado
        if ($domain = $this->option('domain')) {
            return $this->showDomainSessions($domain);
        }

        $this->info('📊 Status do Sistema de Rollback');

        // Verificar se há dados
        $sessions = $this->logger->getSessions();
        if (empty($sessions)) {
            $this->info('Total de sessões: 0');
            $this->info('ℹ️ Nenhuma sessão de rollback registrada.');

            return CommandAlias::SUCCESS;
        }

        // Estatísticas gerais
        $this->showGeneralStatistics();

        // Mostrar detalhes se solicitado ou se a opção table estiver ativa
        if ($this->option('detailed') || $this->option('table')) {
            return $this->showDetailedStatus($this->option('detailed'));
        }

        // Status resumido
        return $this->showSummaryStatus();
    }

    private function showGeneralStatistics(): void
    {
        $stats = $this->logger->getStatistics();

        $this->info('Total de sessões: '.($stats['total_sessions'] ?? 0));
        $this->info('Arquivos criados: '.($stats['total_files_created'] ?? 0));
        $this->info('Arquivos modificados: '.($stats['total_files_modified'] ?? 0));
        $this->info('Domínios únicos: '.count($stats['domains'] ?? []));
        $this->line('');

        $this->info('📈 Estatísticas Gerais Detalhadas:');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total de Sessões', $stats['total_sessions'] ?? 0],
                ['Sessões Ativas', $stats['active_sessions'] ?? 0],
                ['Sessões Concluídas', $stats['completed_sessions'] ?? 0],
                ['Sessões com Falha', $stats['failed_sessions'] ?? 0],
                ['Arquivos Criados', $stats['total_files_created'] ?? 0],
                ['Arquivos Modificados', $stats['total_files_modified'] ?? 0],
                ['Diretórios Criados', $stats['total_directories_created'] ?? 0],
                ['Domínios Únicos', count($stats['domains'] ?? [])],
            ]
        );

        if (! empty($stats['domains'])) {
            $this->line('');
            $this->info('🏗️ Domínios Afetados: '.implode(', ', $stats['domains']));
        }

        if (isset($stats['oldest_session']) && $stats['oldest_session']) {
            $oldestDate = Carbon::parse($stats['oldest_session'])->format('d/m/Y H:i:s');
            $newestDate = Carbon::parse($stats['newest_session'] ?? $stats['oldest_session'])->format('d/m/Y H:i:s');
            $this->line('');
            $this->info("📅 Período: {$oldestDate} até {$newestDate}");
        }

        $this->line('');
    }

    private function showSessionDetails(string $sessionId): int
    {
        $session = $this->logger->getSession($sessionId);

        if (! $session) {
            $this->error("❌ Sessão '{$sessionId}' não encontrada.");

            return 1;
        }

        $this->info("📋 Detalhes da Sessão: {$sessionId}");
        $this->info("Domínio: {$session['domain']}");
        $this->info('Modelo: '.($session['model'] ?? 'N/A'));
        $this->info("Status: {$session['status']}");
        $this->info('Usuário: '.($session['metadata']['user'] ?? $session['user'] ?? 'test_user'));

        if (isset($session['metadata']['duration'])) {
            $this->info("⏱️ Duração: {$session['metadata']['duration']}s");
        }

        if (isset($session['metadata']['warnings']) && ! empty($session['metadata']['warnings'])) {
            $this->warn('⚠️ Avisos:');
            foreach ($session['metadata']['warnings'] as $warning) {
                $this->line("  - {$warning}");
            }
        }

        $this->line('');

        // Arquivos criados
        if (! empty($session['created'])) {
            $createdCount = count($session['created']);
            $this->info("📁 Arquivos Criados ({$createdCount}):");
            foreach (array_slice($session['created'], 0, 10) as $file) {
                $this->line('  ✓ '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $file));
            }
            if (count($session['created']) > 10) {
                $this->line('  ... e mais '.(count($session['created']) - 10).' arquivos');
            }
        }

        // Arquivos modificados
        if (! empty($session['modified'])) {
            $this->line('');
            $this->info('📝 Arquivos Modificados ('.count($session['modified']).'):');
            foreach (array_slice(array_keys($session['modified']), 0, 10) as $file) {
                $this->line('  ~ '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $file));
            }
            if (count($session['modified']) > 10) {
                $this->line('  ... e mais '.(count($session['modified']) - 10).' arquivos');
            }
        }

        // Diretórios criados
        if (! empty($session['directories'])) {
            $this->line('');
            $this->info('📂 Diretórios Criados ('.count($session['directories']).'):');
            foreach ($session['directories'] as $dir) {
                $this->line('  📁 '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $dir));
            }
        }

        return CommandAlias::SUCCESS;
    }

    private function showDomainSessions(string $domain): int
    {
        $sessions = $this->logger->getSessionsByDomain($domain);

        if (empty($sessions)) {
            $this->error("❌ Nenhuma sessão encontrada para o domínio '{$domain}'.");

            return 1;
        }

        $this->info("🎯 Sessões do Domínio: {$domain}");

        foreach ($sessions as $id => $session) {
            $this->line('');
            $this->info("📋 Sessão: {$id}");
            $this->info('Modelo: '.($session['model'] ?? 'N/A'));
            $this->info("Ação: {$session['action']}");
            $this->info('Status: '.($session['status'] ?? 'N/A'));
            $this->info('Data: '.Carbon::parse($session['timestamp'])->format('d/m/Y H:i:s'));
        }

        return 0;
    }

    private function showDetailedStatus(bool $interactive = true): int
    {
        $sessions = $this->logger->getSessions();

        $this->info('📊 Sessões de Rollback');
        $this->line('');

        $tableData = [];
        foreach ($sessions as $session) {
            $tableData[] = [
                substr($session['id'], 0, 8).'...',
                $session['domain'],
                $session['action'],
                $this->formatStatus($session['status']),
                $session['user'] ?? 'N/A',
                Carbon::parse($session['timestamp'])->format('d/m H:i'),
                count($session['created'] ?? []),
                count($session['modified'] ?? []),
                count($session['directories'] ?? []),
            ];
        }

        $this->table(
            ['ID', 'Domínio', 'Ação', 'Status', 'Usuário', 'Data', 'Criados', 'Modificados', 'Diretórios'],
            $tableData
        );

        // Opção para ver detalhes de uma sessão específica
        if ($interactive && confirm('Deseja ver detalhes de alguma sessão específica?', false)) {
            $sessionIds = array_map(fn ($s) => $s['id'], $sessions);
            $sessionOptions = array_combine($sessionIds, array_map(
                fn ($s) => substr($s['id'], 0, 8).'... - '.$s['domain'].' ('.$s['action'].')',
                $sessions
            ));

            $selectedSession = select(
                label: 'Selecione uma sessão:',
                options: $sessionOptions
            );

            return $this->showSessionDetails($selectedSession);
        }

        return CommandAlias::SUCCESS;
    }

    private function showSummaryStatus(): int
    {
        $sessions = $this->logger->getSessions();

        $this->info('📋 Resumo das Sessões Recentes:');
        $this->line('');

        // Mostrar últimas 10 sessões
        $recentSessions = array_slice(array_reverse($sessions), 0, 10);

        $tableData = [];
        foreach ($recentSessions as $session) {
            $totalFiles = count($session['created'] ?? []) + count($session['modified'] ?? []);
            $tableData[] = [
                substr($session['id'], 0, 8).'...',
                $session['domain'],
                $session['action'],
                $this->formatStatus($session['status']),
                Carbon::parse($session['timestamp'])->format('d/m H:i'),
                $totalFiles,
            ];
        }

        $this->table(
            ['ID', 'Domínio', 'Ação', 'Status', 'Data', 'Total Arquivos'],
            $tableData
        );

        $this->line('');
        $this->info('💡 Comandos Úteis:');
        $this->line('  • rollback:status --detailed    - Ver todos os detalhes');
        $this->line('  • rollback:status --session=ID  - Ver sessão específica');
        $this->line('  • rollback:status --domain=NOME - Ver sessões de um domínio');
        $this->line('  • rollback:manager --interactive - Gerenciar rollbacks');

        return CommandAlias::SUCCESS;
    }

    private function formatStatus(string $status): string
    {
        return match ($status) {
            'active' => '🔄 Ativo',
            'completed' => '✅ Concluído',
            'failed' => '❌ Falhou',
            default => "❓ {$status}"
        };
    }
}
