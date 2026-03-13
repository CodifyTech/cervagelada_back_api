<?php

namespace App\Console\Commands;

use App\Domains\Produto\Models\Produto;
use App\Domains\Produto\Seeders\SkuCatalogSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSkusCommand extends Command
{
    protected $signature = 'app:import-skus {file? : Path to JSON or CSV file with SKUs}';

    protected $description = 'Import base SKU catalog from a JSON or CSV file';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (! $file) {
            $this->info('No file specified. Running built-in seeder with sample SKUs...');

            return $this->importBuiltIn();
        }

        if (! file_exists($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        return match ($extension) {
            'json' => $this->importJson($file),
            'csv' => $this->importCsv($file),
            default => $this->failWithCode("Unsupported file format: {$extension}. Use JSON or CSV."),
        };
    }

    private function importJson(string $file): int
    {
        $content = file_get_contents($file);
        $items = json_decode($content, true);

        if (! is_array($items)) {
            $this->error('Invalid JSON structure. Expected an array of objects.');

            return self::FAILURE;
        }

        return $this->importItems($items);
    }

    private function importCsv(string $file): int
    {
        $handle = fopen($file, 'r');
        if (! $handle) {
            $this->error("Cannot open file: {$file}");

            return self::FAILURE;
        }

        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);
            $this->error('CSV file is empty or has no headers.');

            return self::FAILURE;
        }

        $headers = array_map('trim', $headers);
        $items = [];

        while (($row = fgetcsv($handle)) !== false) {
            $items[] = array_combine($headers, array_map('trim', $row));
        }

        fclose($handle);

        return $this->importItems($items);
    }

    private function importItems(array $items): int
    {
        $created = 0;
        $skipped = 0;
        $fields = ['nome', 'marca', 'ean', 'volume_ml', 'teor_alcoolico', 'fabricante', 'url_imagem', 'descricao', 'sku', 'atributos'];

        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $ean = $item['ean'] ?? null;

                if (! $ean || ! ($item['nome'] ?? null)) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                $exists = Produto::where('ean', $ean)->exists();
                if ($exists) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                $data = [];
                foreach ($fields as $field) {
                    if (isset($item[$field]) && $item[$field] !== '') {
                        $data[$field] = $item[$field];
                    }
                }

                $data['status_aprovacao'] = 'aprovado';
                Produto::create($data);
                $created++;
                $bar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine();
            $this->error("Import failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Import complete: {$created} created, {$skipped} skipped (duplicate EAN or missing data).");

        return self::SUCCESS;
    }

    private function importBuiltIn(): int
    {
        $seeder = new SkuCatalogSeeder;
        $seeder->setCommand($this);
        $seeder->run();

        return self::SUCCESS;
    }

    private function failWithCode(string $message): int
    {
        $this->error($message);

        return self::FAILURE;
    }
}
