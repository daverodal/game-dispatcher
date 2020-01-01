import Vue from 'vue'
import VueRouter from 'vue-router'
import TestMe from '../components/test-me'
import Map from '../views/Map.vue'
import Maps from "../views/Maps";

Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        redirect: '/maps'
    },
    {
    path: '/maps',
    name: 'maps',
    component: Maps
  },
    {
        path: '/maps/:id',
        name: 'map',
        component: Map
    },


]

const router = new VueRouter({
  routes
})

export default router
