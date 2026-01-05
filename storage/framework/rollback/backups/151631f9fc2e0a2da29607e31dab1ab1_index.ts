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
                    title: 'Endereco',
                    icon: { icon: 'tabler-template' },
                    to: 'endereco',
                    action: 'list',
                    subject: 'endereco',
                },
{
                    title: 'Loja',
                    icon: { icon: 'tabler-template' },
                    to: 'loja',
                    action: 'list',
                    subject: 'loja',
                },
]