import { useHead } from 'unhead'
import { createRouter, createWebHistory } from 'vue-router'
import ForumView from '@/views/ForumView.vue'
import CommentView from '@/views/topics/CommentView.vue'
import HotView from '@/views/topics/HotView.vue'
import SearchView from '@/views/SearchView.vue'
import LoginPage from '@/views/auth/LoginView.vue'
import MyProfile from '@/views/auth/MyProfile.vue'
import RegisterPage from '@/views/auth/RegisterView.vue'
import { useAuthStore } from '@/stores/auth.store'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: ForumView,
      meta: { requiresAuth: true, title: 'Home', description: 'Ninjastic Community is a place to be.' }


    },
    {
      path: '/login',
      name: 'Login',
      component: LoginPage
    },
    {
      path: '/my-profile',
      name: 'My Profile',
      component: MyProfile,
      meta: { requiresAuth: true }
    },
    {
      path: '/register',
      name: 'Register',
      component: RegisterPage
    },
    {
      path: '/discussions/thread/:id',
      name: 'Thread',
      component: CommentView,
      meta: { requiresAuth: true }
    },
    {
      path: '/hot-topics',
      name: 'Hot Topics',
      component: HotView,
      meta: { requiresAuth: true }
    },
    {
      path: '/search',
      name: 'Search',
      component: SearchView,
      meta: { requiresAuth: true }
    }
  ]
})



router.beforeEach((to, from, next) => {
  if (typeof to.meta.title === 'string') {
    const pageTitle = `${to.meta.title} | ${import.meta.env.VITE_SITENAME}` ?? `${to.meta.title} ' | Unknown Site`; 
    useHead({
      title: pageTitle + ' Community',
      meta: [
        {
          name: 'description',
          content: typeof to.meta.description === 'function' ? to.meta.description() : to.meta.description || '', 
        },
      ],
    });
  }
  const auth = useAuthStore();
  if (to.meta.requiresAuth && !auth.checkAuth()) {
    console.log(auth.checkAuth())
    auth.returnUrl = to.fullPath
    next('/login')
  } else {
    next()
  }
})



export default router
