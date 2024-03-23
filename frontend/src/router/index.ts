import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/layout/HomeView.vue'
import ForumView from '@/views/ForumView.vue'
import ThreadView from '@/views/topics/ThreadView.vue'
import LoginPage from '@/views/auth/LoginView.vue'
import { useAuthStore } from '@/stores/auth.store'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: HomeView,
      //!TODO SEO stuff...
    },
    {
      path: '/login',
      name: 'Login',
      component: LoginPage
    },
    {
      path: '/discussions',
      name: 'Discussions',
      component: ForumView
    },
    {
      path: '/discussions/thread/:id',
      name: 'Thread',
      component: ThreadView
    },
    {
      path: '/hot-topics',
      name: 'Hot Topics',
      component: HomeView
    }
  ]
})

router.beforeEach(async (to, from, next) => {

  const publicPages = ['/login'];
  const authRequired = !publicPages.includes(to.path);
  const auth = useAuthStore();
  
  if (authRequired && (!auth.user || !auth.user.token)) {
    auth.returnUrl = to.fullPath;
    next('/login');
  } else {
    next();
  }
});



export default router;