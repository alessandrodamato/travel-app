import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import TTMap from '../views/TTMap.vue'
import CreateTrip from '../views/CreateTrip.vue'
import Error404 from '../views/Error404.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/map',
      name: 'ttmap',
      component: TTMap
    },
    {
      path: '/create',
      name: 'createTrip',
      component: CreateTrip
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'error404',
      component: Error404
    },
  ]
})

export default router
