const unchangedSubjects = ['auth', 'all'] // These subjects are not related to any model
const userSubjects = ['permission', 'user', 'roles', 'contato', 'endereco', 'loja', 'produto', 'noticias', 'promocao', 'pedido', 'item-pedido', 'avaliacao', 'transacoes-financeiras']

export const subjects = [...unchangedSubjects, ...userSubjects]
export const actions = ['create', 'list', 'read', 'edit', 'delete', 'manage', 'block']
