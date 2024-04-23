import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import Vue3Toasity, { type ToastContainerOptions } from 'vue3-toastify';
import router from './router'
import { createHead } from '@unhead/vue'

const app = createApp(App)
const head = createHead()

app.use(createPinia())
app.use(router)
app.use(head)
app.use(Vue3Toasity,  {
    autoClose: 5000,
  }) as ToastContainerOptions,
app.mount('#app')