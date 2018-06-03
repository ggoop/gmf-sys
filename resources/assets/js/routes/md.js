const routes = [{
  path: '/md',
  name: 'md',
  component: () =>
    import ('../pages/Md/App'),
  children: [{
      path: 'home',
      name: 'md.home',
      component: () =>
        import ('../pages/Md/Home')
    },
    {
      path: 'search/:q?',
      name: 'md.search',
      component: () =>
        import ('../pages/Md/Search')
    },
    {
      path: 'show/:id',
      name: 'md.show',
      component: () =>
        import ('../pages/Md/Show')
    }
  ]
}];
export default routes;
