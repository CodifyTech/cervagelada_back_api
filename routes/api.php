<?php

// Public routes (no admin auth required)
require __DIR__ . '/public.php';

require __DIR__ . '/auth.php';

require __DIR__ . '/roles.php';

require __DIR__ . '/permissions.php';

require __DIR__ . '/domains/users.php';

// Endereco Domain Routes
require __DIR__ . '/domains/endereco.php';

// Loja Domain Routes
require __DIR__ . '/domains/loja.php';

// Produto Domain Routes
require __DIR__ . '/domains/produto.php';

// Noticias Domain Routes
require __DIR__ . '/domains/noticias.php';

// Promocao Domain Routes
require __DIR__ . '/domains/promocao.php';

// Pedido Domain Routes
require __DIR__ . '/domains/pedido.php';

// ItemPedido Domain Routes
require __DIR__ . '/domains/item-pedido.php';

// Avaliacao Domain Routes
require __DIR__ . '/domains/avaliacao.php';

// TransacoesFinanceiras Domain Routes
require __DIR__ . '/domains/transacoes-financeiras.php';

// Dashboard Domain Routes
require __DIR__ . '/domains/dashboard.php';
