export default {
  path: '/resources/messages',
  meta: { requiresAuth: true },
  name: 'messages',
  component: () => import('../components/message/Layout.vue'),
  redirect: { name: 'MessageList' },
  children: [
    {
      name: 'MessageList',
      path: '',
      component: () => import('../views/message/List.vue')
    },
    {
      name: 'MessageCreate',
      path: 'new',
      component: () => import('../views/message/Create.vue')
    },
    {
      name: 'MessageUpdate',
      path: ':id/edit',
      component: () => import('../views/message/Update.vue')
    },
    {
      name: 'MessageShow',
      path: ':id',
      component: () => import('../views/message/Show.vue')
    }
  ]
};
