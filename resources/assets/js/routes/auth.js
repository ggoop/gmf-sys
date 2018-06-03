const routes = [{
  path: '/auth',
  name: 'auth',
  component:() => import('../pages/Auth/App'),
  children: [{
      path: 'login',
      name: 'auth.login',
      component:() => import('../pages/Auth/Login')
    },
    {
      path: 'register',
      name: 'auth.register',
      component:() => import('../pages/Auth/Register')
    },
    {
      path: 'password/find-sms/:id?',
      name: 'auth.password.find.sms',
      component:() => import('../pages/Auth/PasswordFindSms')
    },
    {
      path: 'password/find-mail/:id?',
      name: 'auth.password.find.mail',
      component:() => import('../pages/Auth/PasswordFindMail')
    },
    {
      path: 'password/find-word/:id?',
      name: 'auth.password.find.word',
      component:() => import('../pages/Auth/PasswordFindWord')
    },
    {
      path: 'password/find/:id?',
      name: 'auth.password.find',
      component:() => import('../pages/Auth/PasswordFind')
    },
    {
      path: 'password/:id?',
      name: 'auth.password',
      component:() => import('../pages/Auth/Password')
    },
    {
      path: 'reset/:id/:token?',
      name: 'auth.reset',
      component:() => import('../pages/Auth/Reset')
    },
    {
      path: 'verify-mail',
      name: 'auth.verify.mail',
      component:() => import('../pages/Auth/VerifyMail'),
      meta: { requiresAuth: true },
    },
    {
      path: 'chooser',
      name: 'auth.chooser',
      component:() => import('../pages/Auth/Chooser')
    },
    {
      path: 'identifier',
      name: 'auth.identifier',
      component:() => import('../pages/Auth/Identifier')
    },
    {
      path: 'remove',
      name: 'auth.remove',
      component:() => import('../pages/Auth/Remove')
    },
    {
      path: 'logout',
      name: 'auth.logout',
      component:() => import('../pages/Auth/Logout'),
      meta: { requiresAuth: true },
    },
    {
      path: 'account/join',
      name: 'auth.account.join',
      component:() => import('../pages/Auth/AccountJoin'),
      meta: { requiresAuth: true },
    },
    {
      path: 'account/dashboard',
      name: 'auth.account.dashboard',
      component:() => import('../pages/Auth/AccountDashboard'),
      meta: { requiresAuth: true },
    },
  ]
}];
export default routes;