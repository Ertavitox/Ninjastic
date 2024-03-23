import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/layout/HomeView.vue'
import ForumView from '@/views/ForumView.vue'
import ThreadView from '@/views/topics/ThreadView.vue'
import LoginPage from '@/components/auth/LoginPage.vue'
import { useAuthStore } from '@/components/auth/authStore'
import Logout from '@/components/auth/Logout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/login',
      name: 'login',
      component: LoginPage
    },
    {
      path: '/logout',
      name: 'logout',
      component: Logout
    },
    {
      path: '/discussions',
      name: 'discussions',
      component: ForumView
    },
    {
      path: '/discussions/thread/:id',
      name: 'thread',
      component: ThreadView
    },
    {
      path: '/hot-topics',
      name: 'hottopics',
      component: HomeView
    }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  // Check if the route requires authentication
  if (to.meta.requiresAuth) {
    // If the user is not authenticated, redirect to the login page
    if (!authStore.isAuthenticated) {
      next('/login');
    } else {
      next(); // Proceed to the route
    }
  } else {
    next(); // Allow access to routes that do not require authentication
  }

});

export default router;