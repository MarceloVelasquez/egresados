import Vue from 'vue';
import Router from 'vue-router';
import Login from './views/Login.vue';

Vue.use(Router);

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/personal',
      component: () => import('./views/Personal/Inicio.vue')
    }
  ]
});