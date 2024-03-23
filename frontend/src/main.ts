import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { createMetaManager } from 'vue-meta'


const app = createApp(App)
app.use(createPinia())
const metaManager = createMetaManager()
app.use(router)
app.use(metaManager)
app.mount('#app')
