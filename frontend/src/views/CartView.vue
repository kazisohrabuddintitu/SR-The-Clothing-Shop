<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../services/api'

const router = useRouter()
const cart = ref(null)
const message = ref('')

const loadCart = async () => {
  const { data } = await api.get('/cart')
  cart.value = data.cart
}

const updateItem = async (item) => {
  await api.patch(`/cart/items/${item.id}`, { quantity: item.quantity })
  message.value = 'Cart updated.'
}

const removeItem = async (item) => {
  await api.delete(`/cart/items/${item.id}`)
  await loadCart()
}

const total = computed(() => {
  if (!cart.value) return 0
  return cart.value.items.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
})

onMounted(loadCart)
</script>

<template>
  <section>
    <div class="page-header">
      <h1>Your cart</h1>
      <button class="btn btn--ghost" type="button" @click="router.push({ name: 'checkout' })">
        Checkout
      </button>
    </div>

    <p v-if="message" class="notice">{{ message }}</p>

    <div v-if="!cart || cart.items.length === 0" class="empty-state">
      Cart is empty.
    </div>

    <div v-else class="stack">
      <div v-for="item in cart.items" :key="item.id" class="cart-item">
        <div>
          <strong>{{ item.product.name }}</strong>
          <p class="muted">
            Size: {{ item.size }} · ${{ Number(item.product.price).toFixed(2) }}
          </p>
        </div>
        <div class="cart-item__actions">
          <input v-model.number="item.quantity" type="number" min="1" />
          <button class="btn btn--ghost" type="button" @click="updateItem(item)">Update</button>
          <button class="btn btn--danger" type="button" @click="removeItem(item)">Remove</button>
        </div>
      </div>
      <div class="total-row">
        <span>Total</span>
        <strong>${{ total.toFixed(2) }}</strong>
      </div>
    </div>
  </section>
</template>
