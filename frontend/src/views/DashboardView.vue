<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../services/api'
import { useSession } from '../stores/session'

const { user } = useSession()
const orders = ref([])
const cart = ref(null)
const loading = ref(false)
const error = ref('')

const loadData = async () => {
  loading.value = true
  error.value = ''
  try {
    const [ordersRes, cartRes] = await Promise.all([
      api.get('/orders'),
      api.get('/cart'),
    ])

    orders.value = ordersRes.data.orders
    cart.value = cartRes.data.cart
  } catch (err) {
    const status = err?.response?.status
    if (status === 401) {
      error.value = 'Please log in again to view your orders.'
    } else {
      error.value = 'Unable to load dashboard data.'
    }
  } finally {
    loading.value = false
  }
}

onMounted(loadData)
</script>

<template>
  <section class="stack">
    <div class="page-header">
      <h1>Dashboard</h1>
    </div>

    <p v-if="loading" class="muted">Loading...</p>
    <p v-else-if="error" class="notice">{{ error }}</p>

    <div class="card">
      <h2>Welcome back</h2>
      <p class="muted">{{ user?.email }}</p>
    </div>

    <div class="card">
      <h2>Cart summary</h2>
      <p v-if="!cart || cart.items.length === 0" class="muted">No items in cart.</p>
      <ul v-else>
        <li v-for="item in cart.items" :key="item.id">
          {{ item.product.name }} x {{ item.quantity }}
        </li>
      </ul>
    </div>

    <div class="card">
      <h2>Your orders</h2>
      <p v-if="orders.length === 0" class="muted">No orders yet.</p>
      <div v-else class="stack">
        <div v-for="order in orders" :key="order.id" class="order-card">
          <div class="order-card__header">
            <strong>Order #{{ order.id }}</strong>
            <span class="status">{{ order.status }}</span>
          </div>
          <ul>
            <li v-for="item in order.items" :key="item.id">
              {{ item.product.name }} ({{ item.size }}) x {{ item.quantity }}
            </li>
          </ul>
          <p class="muted">Total: ${{ Number(order.total).toFixed(2) }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
