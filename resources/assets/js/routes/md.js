const routes = [{
  path: '/md',
  name: 'md',
  component: () =>
    import ('../Pages/Md/App'),
  children: [{
      path: 'home',
      name: 'md.home',
      component: () =>
        import ('../Pages/Md/Home')
    },
    {
      path: 'search/?q',
      name: 'md.search',
      component: () =>
        import ('../Pages/Md/Search')
    },
    {
      path: 'show/:id',
      name: 'md.show',
      component: () =>
        import ('../Pages/Md/Show')
    }
  ]
}];
export default routes;
