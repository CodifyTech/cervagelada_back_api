<?php

use App\Domains\Auth\Models\User;
use App\Domains\ItemPedido\Models\ItemPedido;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Produto\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function relatorioCriarLoja(array $overrides = []): Loja
{
    return Loja::create(array_merge([
        'nome_fantasia' => 'Loja Teste',
        'tipo_loja' => 'cervejaria',
        'latitude' => -23.5505,
        'longitude' => -46.6333,
        'raio_entrega_km' => 10,
        'tempo_entrega_min' => 30,
        'tempo_entrega_max' => 60,
        'aceite_automatico' => true,
        'pedido_minimo' => 50.00,
        'taxa_comissao' => 15.00,
        'cep' => '01310100',
        'logradouro' => 'Av. Paulista',
        'numero' => '1',
        'bairro' => 'Bela Vista',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'ativo' => true,
    ], $overrides));
}

function relatorioCriarPedido(Loja $loja, User $user, array $overrides = []): Pedido
{
    return Pedido::create(array_merge([
        'user_id' => $user->id,
        'loja_id' => $loja->id,
        'subtotal' => 50.00,
        'taxa_entrega' => 0,
        'total' => 50.00,
        'status' => 'entregue',
    ], $overrides));
}

function relatorioCriarProduto(array $overrides = []): Produto
{
    return Produto::create(array_merge([
        'nome' => 'IPA Artesanal',
        'descricao' => 'Cerveja IPA',
        'marca' => 'Cervejaria Teste',
    ], $overrides));
}

function relatorioCriarItemPedido(Pedido $pedido, Produto $produto, array $overrides = []): ItemPedido
{
    return ItemPedido::create(array_merge([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto->id,
        'quantidade_solicitada' => 10,
        'quantidade_final' => 10,
        'preco_unitario' => 5.00,
        'preco_total' => 50.00,
        'ajuste_preco' => 0,
    ], $overrides));
}

function relatorioCriarUsuario(array $overrides = []): User
{
    return User::create(array_merge([
        'name' => 'Usuario Teste',
        'email' => 'usuario'.uniqid().'@example.com',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
    ], $overrides));
}

beforeEach(function () {
    $this->user = relatorioCriarUsuario(['email' => 'principal'.uniqid().'@example.com']);
    $this->token = auth('api')->login($this->user);
});

// ---------------------------------------------------------------------
// GET relatorios/produtos-mais-vendidos
// ---------------------------------------------------------------------

it('exige autenticacao para produtos mais vendidos', function () {
    auth('api')->logout();
    $this->getJson('/api/relatorios/produtos-mais-vendidos')->assertStatus(401);
});

it('retorna contrato completo de produtos mais vendidos com dados', function () {
    $loja = relatorioCriarLoja();
    $produto = relatorioCriarProduto();
    $pedido = relatorioCriarPedido($loja, $this->user);
    relatorioCriarItemPedido($pedido, $produto, [
        'quantidade_final' => 10,
        'preco_total' => 50.00,
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/produtos-mais-vendidos');

    $response->assertStatus(200);

    $item = $response->json('data.0');

    expect($item)->toHaveKeys([
        'produto', 'loja', 'quantidade_vendida', 'receita_total', 'ticket_medio',
    ]);
    expect($item['produto'])->toBe('IPA Artesanal');
    expect((float) $item['quantidade_vendida'])->toBe(10.0);
    expect((float) $item['receita_total'])->toBe(50.0);
    expect($item['ticket_medio'])->not->toBeNull();
    expect((float) $item['ticket_medio'])->toBe(5.0);
});

it('nunca retorna ticket_medio null ou NaN quando quantidade vendida e zero', function () {
    // Produto sem nenhum item_pedido vinculado não aparece no relatório
    // (join exige item_pedidos), então simulamos quantidade zero via item
    // com quantidade_final 0 explicitamente.
    $loja = relatorioCriarLoja();
    $produto = relatorioCriarProduto(['nome' => 'Produto Sem Venda']);
    $pedido = relatorioCriarPedido($loja, $this->user);
    relatorioCriarItemPedido($pedido, $produto, [
        'quantidade_final' => 0,
        'preco_total' => 0,
        'preco_unitario' => 0,
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/produtos-mais-vendidos');

    $response->assertStatus(200);

    $item = collect($response->json('data'))
        ->firstWhere('produto', 'Produto Sem Venda');

    expect($item)->not->toBeNull();
    expect($item['ticket_medio'])->not->toBeNull();
    expect(is_numeric($item['ticket_medio']))->toBeTrue();
    expect((float) $item['ticket_medio'])->toBe(0.0);
});

it('retorna lista vazia de produtos mais vendidos quando nao ha pedidos', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/produtos-mais-vendidos');

    $response->assertStatus(200)
        ->assertJsonPath('data', [])
        ->assertJsonPath('total', 0);
});

it('filtra produtos mais vendidos por intervalo de datas', function () {
    $loja = relatorioCriarLoja();
    $produtoDentro = relatorioCriarProduto(['nome' => 'Produto Dentro do Periodo']);
    $produtoFora = relatorioCriarProduto(['nome' => 'Produto Fora do Periodo']);

    $pedidoDentro = relatorioCriarPedido($loja, $this->user, [
        'created_at' => now()->subDays(2),
    ]);
    $pedidoDentro->created_at = now()->subDays(2);
    $pedidoDentro->save();
    relatorioCriarItemPedido($pedidoDentro, $produtoDentro);

    $pedidoFora = relatorioCriarPedido($loja, $this->user);
    $pedidoFora->created_at = now()->subDays(30);
    $pedidoFora->save();
    relatorioCriarItemPedido($pedidoFora, $produtoFora);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/produtos-mais-vendidos?'.http_build_query([
            'de' => now()->subDays(5)->toDateString(),
            'ate' => now()->toDateString(),
        ]));

    $response->assertStatus(200);

    $produtos = collect($response->json('data'))->pluck('produto');

    expect($produtos)->toContain('Produto Dentro do Periodo');
    expect($produtos)->not->toContain('Produto Fora do Periodo');
});

// ---------------------------------------------------------------------
// GET relatorios/sellers
// ---------------------------------------------------------------------

it('exige autenticacao para sellers', function () {
    auth('api')->logout();
    $this->getJson('/api/relatorios/sellers')->assertStatus(401);
});

it('retorna contrato completo de sellers com dados', function () {
    $loja = relatorioCriarLoja(['nome_fantasia' => 'Cervejaria X']);
    $responsavel = relatorioCriarUsuario(['loja_id' => $loja->id]);
    relatorioCriarPedido($loja, $this->user, ['total' => 100.00]);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/sellers');

    $response->assertStatus(200);

    $item = collect($response->json('data'))->firstWhere('loja', 'Cervejaria X');

    expect($item)->not->toBeNull();
    expect($item)->toHaveKeys([
        'loja', 'cnpj', 'responsavel', 'cidade', 'total_pedidos', 'receita', 'status', 'criado_em',
    ]);
    expect($item['receita'])->not->toBeNull();
    expect(is_numeric($item['receita']))->toBeTrue();
    expect((float) $item['receita'])->toBe(100.0);
    expect($item['status'])->toBeIn(['ativo', 'inativo']);
});

it('retorna o cnpj real da loja em sellers quando cadastrado', function () {
    relatorioCriarLoja(['nome_fantasia' => 'Loja Com Cnpj', 'cnpj' => '12.345.678/0001-99']);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/sellers');

    $response->assertStatus(200);

    $item = collect($response->json('data'))->firstWhere('loja', 'Loja Com Cnpj');

    expect($item)->not->toBeNull();
    expect($item['cnpj'])->toBe('12.345.678/0001-99');
});

it('retorna cnpj null em sellers quando loja nao possui cnpj cadastrado', function () {
    relatorioCriarLoja(['nome_fantasia' => 'Loja Sem Cnpj', 'cnpj' => null]);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/sellers');

    $response->assertStatus(200);

    $item = collect($response->json('data'))->firstWhere('loja', 'Loja Sem Cnpj');

    expect($item)->not->toBeNull();
    expect(array_key_exists('cnpj', $item))->toBeTrue();
    expect($item['cnpj'])->toBeNull();
});

it('retorna lista vazia de sellers quando nao ha lojas', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/sellers');

    $response->assertStatus(200)
        ->assertJsonPath('data', [])
        ->assertJsonPath('total', 0);
});

it('nao duplica linha nem infla receita quando loja tem multiplos usuarios vinculados', function () {
    $loja = relatorioCriarLoja(['nome_fantasia' => 'Loja Multi Usuario']);

    relatorioCriarUsuario(['loja_id' => $loja->id]);
    relatorioCriarUsuario(['loja_id' => $loja->id]);
    relatorioCriarUsuario(['loja_id' => $loja->id]);

    relatorioCriarPedido($loja, $this->user, ['total' => 100.00]);
    relatorioCriarPedido($loja, $this->user, ['total' => 200.00]);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/sellers');

    $response->assertStatus(200);

    $linhas = collect($response->json('data'))->where('loja', 'Loja Multi Usuario');

    expect($linhas)->toHaveCount(1);

    $item = $linhas->first();
    expect((int) $item['total_pedidos'])->toBe(2);
    expect((float) $item['receita'])->toBe(300.0);
});

it('filtra sellers por cidade e regiao', function () {
    relatorioCriarLoja(['nome_fantasia' => 'Loja SP', 'cidade' => 'São Paulo', 'estado' => 'SP']);
    relatorioCriarLoja(['nome_fantasia' => 'Loja RJ', 'cidade' => 'Rio de Janeiro', 'estado' => 'RJ']);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/sellers?regiao=SP');

    $response->assertStatus(200);
    $lojas = collect($response->json('data'))->pluck('loja');

    expect($lojas)->toContain('Loja SP');
    expect($lojas)->not->toContain('Loja RJ');
});

// ---------------------------------------------------------------------
// GET relatorios/financeiro
// ---------------------------------------------------------------------

it('exige autenticacao para financeiro', function () {
    auth('api')->logout();
    $this->getJson('/api/relatorios/financeiro')->assertStatus(401);
});

it('retorna contrato completo de financeiro com dados', function () {
    $loja = relatorioCriarLoja(['nome_fantasia' => 'Loja Financeiro', 'taxa_comissao' => 15.00]);
    relatorioCriarPedido($loja, $this->user, ['total' => 100.00]);
    relatorioCriarPedido($loja, $this->user, ['total' => 200.00]);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/financeiro');

    $response->assertStatus(200);

    $item = collect($response->json('data'))->firstWhere('loja', 'Loja Financeiro');

    expect($item)->not->toBeNull();
    expect($item)->toHaveKeys([
        'loja', 'receita_bruta', 'taxa_plataforma', 'receita_liquida', 'pedidos', 'periodo',
    ]);

    foreach (['receita_bruta', 'taxa_plataforma', 'receita_liquida', 'pedidos'] as $campo) {
        expect($item[$campo])->not->toBeNull();
        expect(is_numeric($item[$campo]))->toBeTrue();
    }

    $receitaBrutaEsperada = 300.0;
    $taxaEsperada = round($receitaBrutaEsperada * (15.00 / 100), 2);

    expect((float) $item['receita_bruta'])->toBe($receitaBrutaEsperada);
    expect((float) $item['taxa_plataforma'])->toBe($taxaEsperada);
    expect((float) $item['receita_liquida'])->toBe(round($receitaBrutaEsperada - $taxaEsperada, 2));
    expect((int) $item['pedidos'])->toBe(2);
    expect($item['periodo'])->not->toBeNull();
    expect($item['periodo'])->toBeString();
});

it('calcula taxa_plataforma usando a taxa_comissao por loja', function () {
    $loja = relatorioCriarLoja(['nome_fantasia' => 'Loja Comissao Alta', 'taxa_comissao' => 15.00]);
    relatorioCriarPedido($loja, $this->user, ['total' => 400.00]);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/financeiro');

    $response->assertStatus(200);

    $item = collect($response->json('data'))->firstWhere('loja', 'Loja Comissao Alta');

    $taxaEsperada = round(400.0 * (15.00 / 100), 2);

    expect($item)->not->toBeNull();
    expect((float) $item['taxa_plataforma'])->toBe($taxaEsperada);
    expect((float) $item['receita_liquida'])->toBe(round(400.0 - $taxaEsperada, 2));
});

it('usa a taxa de plataforma do config como fallback quando taxa_comissao da loja e zero', function () {
    $loja = relatorioCriarLoja(['nome_fantasia' => 'Loja Sem Comissao', 'taxa_comissao' => 0]);
    relatorioCriarPedido($loja, $this->user, ['total' => 300.00]);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/financeiro');

    $response->assertStatus(200);

    $item = collect($response->json('data'))->firstWhere('loja', 'Loja Sem Comissao');

    $taxaPlataforma = config('relatorios.taxa_plataforma');
    $taxaEsperada = round(300.0 * $taxaPlataforma, 2);

    expect($item)->not->toBeNull();
    expect((float) $item['taxa_plataforma'])->toBe($taxaEsperada);
    expect((float) $item['receita_liquida'])->toBe(round(300.0 - $taxaEsperada, 2));
});

it('retorna receita bruta zero sem null ou NaN para loja sem pedidos', function () {
    relatorioCriarLoja(['nome_fantasia' => 'Loja Sem Pedidos']);

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/financeiro');

    $response->assertStatus(200);

    $item = collect($response->json('data'))->firstWhere('loja', 'Loja Sem Pedidos');

    expect($item)->not->toBeNull();
    expect($item['receita_bruta'])->not->toBeNull();
    expect((float) $item['receita_bruta'])->toBe(0.0);
    expect((float) $item['taxa_plataforma'])->toBe(0.0);
    expect((float) $item['receita_liquida'])->toBe(0.0);
    expect((int) $item['pedidos'])->toBe(0);
    expect($item['periodo'])->not->toBeNull();
});

it('filtra financeiro por intervalo de datas e reflete no periodo exibido', function () {
    $loja = relatorioCriarLoja(['nome_fantasia' => 'Loja Periodo']);

    $pedidoDentro = relatorioCriarPedido($loja, $this->user, ['total' => 100.00]);
    $pedidoDentro->created_at = now()->subDays(2);
    $pedidoDentro->save();

    $pedidoFora = relatorioCriarPedido($loja, $this->user, ['total' => 500.00]);
    $pedidoFora->created_at = now()->subDays(30);
    $pedidoFora->save();

    $de = now()->subDays(5)->toDateString();
    $ate = now()->toDateString();

    $response = $this->withToken($this->token)
        ->getJson('/api/relatorios/financeiro?'.http_build_query(['de' => $de, 'ate' => $ate]));

    $response->assertStatus(200);

    $item = collect($response->json('data'))->firstWhere('loja', 'Loja Periodo');

    expect($item)->not->toBeNull();
    expect((float) $item['receita_bruta'])->toBe(100.0);
    expect((int) $item['pedidos'])->toBe(1);
    expect($item['periodo'])->not->toBeNull();
    expect($item['periodo'])->not->toBe('Todos os períodos');
});
