
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueResource from 'vue-resource';
Vue.use(VueResource);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ScenarioEditor.vue'));
Vue.component('modern-unit', require('./components/ModernUnit.vue'));
Vue.component('units-list', require('./components/UnitsList.vue'));
Vue.component('modern-unit-editor', require('./components/ModernUnitEditor.vue'));
Vue.component('modern-at-def-unit-editor', require('./components/ModernAtDefUnitEditor.vue'));

const app = new Vue({
    el: '#container'
});
