/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from "vue";
window.Vue = Vue;

/*import Vuex from 'vuex';
window.Vuex = Vuex;
Vue.use(Vuex);

import store from './../cart/store';*/


Vue.component('pages-filter-view', require('./components/Filter/PagesFilter').default);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const pages_filter = new Vue({

}).$mount('#pages-filter-app');

