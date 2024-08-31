import { createApp } from 'vue'
import App from './App.vue'
import 'bootstrap/dist/css/bootstrap.css';
import router from './router';
import { createPinia } from 'pinia';
import { useAuthStore } from "@/stores/auth";


const app = createApp(App)
const pinia = createPinia()


app.use(pinia)
app.use(router)
app.mount('#app')

// Khôi phục trạng thái người dùng từ sessionStorage
const authStore = useAuthStore();
authStore.loadUserFromSessionStorage();

if (authStore.isAuthenticated) {
    authStore.loadUserFromServer();
}

