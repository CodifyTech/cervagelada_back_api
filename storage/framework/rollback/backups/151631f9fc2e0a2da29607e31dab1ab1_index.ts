export default [
  {
    title: 'Home',
    to: { name: 'root' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    title: 'Usuários',
    icon: { icon: 'tabler-users-group' },
    to: 'users',
    action: 'list',
    subject: 'users',
  },
  {
    title: 'Controle de Acesso',
    icon: { icon: 'tabler-smart-home' },
    children: [
      { title: 'Perfis', to: 'acesso-perfis' },
      { title: 'Permissões', to: 'acesso-permissoes' },
    ],
  },
  {
    title: 'Endereços',
    icon: { icon: 'tabler-home-2' },
    to: 'endereco',
    action: 'list',
    subject: 'endereco',
  },
  {
    title: 'Lojas',
    icon: { icon: 'tabler-building-store' },
    to: 'loja',
    action: 'list',
    subject: 'loja',
  },
  {
    title: 'Produto',
    icon: { icon: 'tabler-shopping-cart' },
    to: 'produto',
    action: 'list',
    subject: 'produto',
  },
{
                    title: 'Loja',
                    icon: { icon: 'tabler-template' },
                    to: 'loja',
                    action: 'list',
                    subject: 'loja',
                },
{
                    title: 'Noticias',
                    icon: { icon: 'tabler-template' },
                    to: 'noticias',
                    action: 'list',
                    subject: 'noticias',
                },
]