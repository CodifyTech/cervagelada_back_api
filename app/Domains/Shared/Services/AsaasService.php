<?php

namespace App\Domains\Shared\Services;

use App\Domains\Shared\Utils\API;

class AsaasService
{
    private API $api;

    public function __construct(API $api)
    {
        $this->api = $api;
        $this->api->setBaseUrl(env('ASAAS_HOST'));
        $this->api->setHeader('access_token', env('ASAAS_API_KEY'));
    }

    //region Cobrança
    public function criarCobranca(array $data)
    {
        return $this->api->post('/payments', $data);
    }

    public function obterQRCode(string $paymentId)
    {
        return $this->api->get("/payments/$paymentId/pixQrCode");
    }

    public function estornarCobranca(string $id)
    {
        return $this->api->post("/payments/$id/refund");
    }

    public function excluirCobranca(string $id)
    {
        return $this->api->delete("/payments/$id");
    }
    //endregion

    //region Cliente

    public function criarCliente(array $data)
    {
        return $this->api->post('/customers', $data);
    }

    public function obterCliente(string $id)
    {
        return $this->api->get("/customers/$id");
    }

    public function removeCliente(array $data)
    {
        return $this->api->delete('/customers', [
            'id' => $data['id'],
        ]);
    }

    //endregion

    //region SubConta

    public function criarSubConta(array $data)
    {
        return $this->api->post('/accounts', $data);
    }

    public function listarSubContas(array $data)
    {
        return $this->api->get('/accounts', $data);
    }

    public function recuperarSubConta(string $id)
    {
        return $this->api->get("/accounts/$id");
    }

    /**
     * Verificar status de sub conta
     *
     * @return array $data
     *               ```
     *               [
     *               "id": "000000-000000-000000",
     *               "commercialInfo": "APPROVED",
     *               "bankAccountInfo": "PENDING",
     *               "documentation": "PENDING",
     *               "general": "PENDING"
     *               ]
     *               ```
     */
    public function statusSubConta(string $apiKeySubConta)
    {
        $this->api->setHeader('access_token', $apiKeySubConta);

        return $this->api->get('/myAccount/status');
    }
    //endregion

    //region Transferências
    /**
     * Transferir para conta de outra Instituição ou chave Pix
     * post
     *
     * @param  array  $data  Dados da transferência incluindo:
     *                       ```
     *                       - value (int): Valor a ser transferido.
     *                       - bankAccount (array): Dados da conta bancária para a transferência.
     *                       - operationType (string): Modalidade da transferência (PIX, TED, etc.).
     *                       - pixAddressKey (string): Chave Pix para transferência (se aplicável).
     *                       - pixAddressKeyType (string): Tipo da chave Pix (CPF, CNPJ, email, telefone, EVP).
     *                       - description (string): Descrição da transferência (opcional para PIX).
     *                       - scheduleDate (date): Data agendada para transferência (opcional).
     *                       - externalReference (string): Identificador da transferência no sistema (opcional).
     *                       ```
     * @return array|null Resposta da API após tentar efetuar a transferência.
     */
    public function transferir(array $data)
    {
        return $this->api->post('/transfers', $data);
    }

    /**
     * Listar transferências
     *
     * @param  array  $data  Dados para listar as transferências
     *                       ```
     *                       - dateCreatedLe[ge] (string): Filtrar pela data de criação inicial
     *                       - dateCreatedLe[le] (string): Filtrar pela data de criação final
     *                       - transferDate[ge] (string): Filtrar pela data inicial de efetivação de transferência
     *                       - transferDate[le] (string): Filtrar pela data final de efetivação de transferência
     *                       - type (string): Filtrar por tipo da transferência
     *                       ```
     * @return array|null
     */
    public function listarTransferencias(array $data)
    {
        return $this->api->get('/transfers', $data);
    }

    /**
     * Cancelar uma transferência
     *
     * @param  string  $id  Identificador único da transferência no Asaas
     * @return array|null
     */
    public function cancelarTransferencia(string $id)
    {
        return $this->api->delete("/transfers/$id/cancel");
    }
    //endregion

    //region Cartão de Crédito
    /**
     * Tokenização de cartão de crédito
     *
     * @return array|null
     */
    public function criarTokenCartaoCredito(array $data = [])
    {
        return $this->api->post('/creditCard/tokenizeCreditCard', $data);
    }
    //endregion
}
