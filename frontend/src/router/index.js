import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProductView from '../views/ProductView.vue'
import CartView from '../views/CartView.vue'
import CheckoutView from '../views/CheckoutView.vue'
import ContactView from '../views/ContactView.vue'
import DashboardView from '../views/DashboardView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import AdminView from '../views/AdminView.vue'
import { useSession } from '../stores/session'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/products/:id', name: 'product', component: ProductView },
    { path: '/cart', name: 'cart', component: CartView },
    { path: '/checkout', name: 'checkout', component: CheckoutView, meta: { requiresAuth: true } },
    { path: '/contact', name: 'contact', component: ContactView },
    { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
    { path: '/admin', name: 'admin', component: AdminView, meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/login', name: 'login', component: LoginView },
    { path: '/register', name: 'register', component: RegisterView },
  ],
})

router.beforeEach((to) => {
  const { user } = useSession()

  if (to.meta.requiresAuth && !user.value) {
    return { name: 'login' }
  }

  if (to.meta.requiresAdmin && !user.value?.is_admin) {
    return { name: 'home' }
  }

  return true
})

export default router
