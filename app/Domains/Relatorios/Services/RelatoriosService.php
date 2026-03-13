<?php

namespace App\Domains\Relatorios\Services;

use App\Domains\Relatorios\Models\RelatoriosModel;
use App\Domains\Shared\Services\BaseService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelatoriosService extends BaseService
{
    public function __construct(private readonly RelatoriosModel $model)
    {
        $this->setModel($this->model);
    }

    public function getPedidos(array $filtros): array
    {
        return $this->model->getPedidos($filtros);
    }

    public function getProdutosMaisVendidos(array $filtros): array
    {
        return $this->model->getProdutosMaisVendidos($filtros);
    }

    public function getSellers(array $filtros): array
    {
        return $this->model->getSellers($filtros);
    }

    public function getFinanceiro(array $filtros): array
    {
        return $this->model->getFinanceiro($filtros);
    }

    /**
     * Generate a streamed CSV response for the given report type.
     */
    public function exportarCsv(string $tipo, array $filtros): StreamedResponse
    {
        $headers = $this->getCsvHeaders($tipo);
        $filename = "relatorio-{$tipo}-".now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($tipo, $filtros, $headers) {
            $output = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers, ';');

            $stream = match ($tipo) {
                'pedidos' => $this->model->streamPedidosCsv($filtros),
                'produtos' => $this->model->streamProdutosCsv($filtros),
                'financeiro' => $this->model->streamFinanceiroCsv($filtros),
                default => throw new \InvalidArgumentException("Tipo de relatório inválido: {$tipo}"),
            };

            foreach ($stream as $row) {
                fputcsv($output, (array) $row, ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function getCsvHeaders(string $tipo): array
    {
        return match ($tipo) {
            'pedidos' => ['ID', 'Cliente', 'Loja', 'Status', 'Total (R$)', 'Pagamento', 'Cidade', 'Data'],
            'produtos' => ['Produto', 'Cervejaria', 'Total Vendido', 'Receita Total (R$)'],
            'financeiro' => ['Data', 'Total Pedidos', 'Receita (R$)', 'Cancelamentos', 'Ticket Médio (R$)'],
            default => [],
        };
    }
}
