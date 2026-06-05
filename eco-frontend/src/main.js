import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './style.css' // <--- AGREGA ESTA LÍNEA DE ESTA MANERA EXACTA

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')