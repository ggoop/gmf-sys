const routes = [{
  path: '/auth',
  name: 'auth',
  component:() => import('../Pages/Auth/App'),
  children: [{
      path: 'login',
      name: 'auth.login',
      component:() => import('../Pages/Auth/Login')
    },
    {
      path: 'register',
      name: 'auth.register',
      component:() => import('../Pages/Auth/Register')
    },
    {
      path: 'password/find-sms/:id?',
      name: 'auth.password.find.sms',
      component:() => import('../Pages/Auth/PasswordFindSms')
    },
    {
      path: 'password/find-mail/:id?',
      name: 'auth.password.find.mail',
      component:() => import('../Pages/Auth/PasswordFindMail')
    },
    {
      path: 'password/find-word/:id?',
      name: 'auth.password.find.word',
      component:() => import('../Pages/Auth/PasswordFindWord')
    },
    {
      path: 'password/find/:id?',
      name: 'auth.password.find',
      component:() => import('../Pages/Auth/PasswordFind')
    },
    {
      path: 'password/:id?',
      name: 'auth.password',
      component:() => import('../Pages/Auth/Password')
    },
    {
      path: 'reset/:id/:token?',
      name: 'auth.reset',
      component:() => import('../Pages/Auth/Reset')
    },
    {
      path: 'verify-mail',
      name: 'auth.verify.mail',
      component:() => import('../Pages/Auth/VerifyMail'),
      meta: { requiresAuth: true },
    },
    {
      path: 'chooser',
      name: 'auth.chooser',
      component:() => import('../Pages/Auth/Chooser')
    },
    {
      path: 'identifier',
      name: 'auth.identifier',
      component:() => import('../Pages/Auth/Identifier')
    },
    {
      path: 'remove',
      name: 'auth.remove',
      component:() => import('../Pages/Auth/Remove')
    },
    {
      path: 'logout',
      name: 'auth.logout',
      component:() => import('../Pages/Auth/Logout'),
      meta: { requiresAuth: true },
    },
    {
      path: 'account/join',
      name: 'auth.account.join',
      component:() => import('../Pages/Auth/AccountJoin'),
      meta: { requiresAuth: true },
    },
    {
      path: 'account/dashboard',
      name: 'auth.account.dashboard',
      component:() => import('../Pages/Auth/AccountDashboard'),
      meta: { requiresAuth: true },
    },
  ]
}];
export default routes;