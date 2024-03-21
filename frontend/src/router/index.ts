import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/layout/HomeView.vue'
import ForumView from '../views/ForumView.vue'
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/discussions',
      name: 'discussions',
      component: ForumView
    },
    {
      path: '/hot-topics',
      name: 'hottopics',
      component: HomeView
    },
  ]
})

export default router
