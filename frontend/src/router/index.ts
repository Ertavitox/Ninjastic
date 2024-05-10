import { useHead } from 'unhead'
import { createRouter, createWebHistory } from 'vue-router'
import ForumView from '@/views/ForumView.vue'
import CommentView from '@/views/topics/CommentView.vue'
import HotestView from '@/views/HotestView.vue'
import LoginPage from '@/views/auth/LoginView.vue'
import MyProfile from '@/views/auth/MyProfile.vue'
import RegisterPage from '@/views/auth/RegisterView.vue'
import { useAuthStore } from '@/stores/auth.store'
import ViewProfile from '@/views/ViewProfile.vue'

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
      component: LoginPage,
      meta: {  title: 'Login', description: 'Ninjastic Community is a place to be.' }
    },
    {
      path: '/my-profile',
      name: 'My Profile',
      meta: { requiresAuth: true, title: 'My Profile', description: 'Ninjastic Community is a place to be.' },
      component: MyProfile
    },
    {
      path: '/profile/:id',
      name: 'Profile',
      component: ViewProfile,
      meta: { requiresAuth: true, title: 'Profile', description: 'Ninjastic Community is a place to be.' }
      
    },
    {
      path: '/register',
      name: 'Register',
      component: RegisterPage,
      meta: {  title: 'Registration', description: 'Ninjastic Community is a place to be.' }
    },
    {
      path: '/discussions/thread/:id',
      name: 'Thread',
      meta: { requiresAuth: true, title: 'Discussion', description: 'Ninjastic Community is a place to be.' },
      component: CommentView
    },
    {
      path: '/hot-topics',
      name: 'Hot Topics',
      component: HotestView,
      meta: { requiresAuth: true, title: 'Hot Topics', description: 'Ninjastic Community is a place to be.' }

    }
  ]
})



router.beforeEach((to, from, next) => {
  const auth = useAuthStore();
  if (to.meta.requiresAuth && !auth.checkAuth()) {
    auth.returnUrl = to.fullPath
    next('/login')
  } else {
    next()
  }
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
  
})

export default router
