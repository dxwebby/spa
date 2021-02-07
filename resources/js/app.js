
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
require('jquery-easing');

Vue.config.productionTip = false
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

 

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


/////////////////////////////////////
// Event BUS to communicated siblings such as myexpenese.vue and todopayment.vue
window.eventBus = new Vue();
/////////////////////////////////////

// Components
//Vue.component('subscription', require('./components/Subscription.vue').default);
Vue.component('activeComponent', require('./components/activeComponent.vue').default);
Vue.component('editform', require('./components/editform.vue').default);
Vue.component('todelete', require('./components/todelete.vue').default);
Vue.component('changepayment', require('./components/changepayment.vue').default);
// Vue.component('unlistedactions', require('./components/unlistedactions.vue').default);

// Siblings Communication
/**
 * Note: home.vue is their parent
 */
Vue.component('brother', require('./components/brother.vue').default);
Vue.component('sister', require('./components/sister.vue').default);
/////////////////////////////////////
// VUE INIT
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter)

const routes = [

  /**
   * If default page is /dashboard, just redirect it with default path /
   */
  { 
    path: '/',
    name: 'dashboard',
    component: require('./components/home.vue').default,
    props: true,
    //redirect: '/dashboard'
  },
  {
    path: '/paymentorder',
    name: 'paymentorder',
    component: require('./components/paymentorder.vue').default,
    props: true,
    //redirect: '/dashboard'
  },
  {
  path: '/allexpenses',
  name: 'allexpenses',
  component: require('./components/allexpenses.vue').default,
  props: true,  
  },
  {
    path: '/unlisted',
    name: 'unlisted',
    component: require('./components/unlisted.vue').default,
    props: true,  
  },
  {
    path: '/unlisted_expenses',
    name: 'unlisted_expenses',
    component: require('./components/unlistedexpenses.vue').default,
    props: true,  
  },
  {
    path: '/_myexpenses', 
    name: 'myexpenses',
    component: require('./components/myexpenses.vue').default,    
    props: true,
  },   
  {
    path: '/_edit', 
    // pass data thru router-link
    name: 'edit',
    props: true,
    component: require('./components/editexpenses.vue').default
  },
  {
    path: '/payments',
    name: 'payments',
    props: true,
    component: require('./components/payments.vue').default
  },
  {
    path: '/addbill',
    name: 'addbill',
    props: true,
    component: require('./components/addbill.vue').default
  },
  {
    path: '/test',
    name: 'test',
    props: true,
    component: require('./components/test.vue').default
  },
  
]
 
const router = new VueRouter({
  routes // short for `routes: routes`
})

const app = new Vue({
  router
}).$mount('#app');
/////////////////////////////////////

// Error
//Vue.config.errorHandler = err => {
  // console.log('Exception: ', err)
//}
/////////////////////////////////////

// Installing modules
// require('../../node_modules/bootstrap-select');

/////////////////////////////////////

// Custom JS
require('./custom.js')

/////////////////////////////////////
