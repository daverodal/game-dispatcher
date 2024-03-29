
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');

window.Vue = require('vue');
import VueResource from 'vue-resource';
Vue.use(VueResource);
import router from './router'

import Vue from 'vue';
import VueFlashMessage from 'vue-flash-message';
Vue.use(VueFlashMessage);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.component('area-map-editor', require('./area-map-editor'));
Vue.component('Map', require('./views/Map'));
Vue.component('Movable', require('./views/Movable'));
Vue.component('MovableBorderBox', require('./views/MovableBorderBox'));
import WargameVueComponents, {SyncController} from "@markarian/wargame-vue-components";
Vue.use(WargameVueComponents)
import  store  from './store';
const app = new Vue({
    el: '#container',
    store: store,
    router,
});
window.app = app;