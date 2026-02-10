<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../services/api'
import { useSession } from '../stores/session'

const products = ref([])
const message = ref('')
const loading = ref(false)
const router = useRouter()
const { user } = useSession()

const loadProducts = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/products')
    products.value = data.products
  } finally {
    loading.value = false
  }
}

const addToCart = async (productId) => {
  if (!user.value) {
    router.push({ name: 'login' })
    return
  }

  router.push({ name: 'product', params: { id: productId } })
}

onMounted(loadProducts)
</script>

<template>
  <section>
    <div class="page-header">
      <div>
        <h1>Shop SR</h1>
        <p class="muted">Curated essentials for everyday wear.</p>
      </div>
      <span v-if="loading" class="muted">Loading...</span>
    </div>

    <p v-if="message" class="notice">{{ message }}</p>

    <div class="grid">
      <article v-for="product in products" :key="product.id" class="card">
        <img v-if="product.image_url" :src="product.image_url" :alt="product.name" />
        <div class="card__body">
          <h3>{{ product.name }}</h3>
          <p class="muted">{{ product.category || 'Essentials' }}</p>
          <p class="price">${{ Number(product.price).toFixed(2) }}</p>
          <div class="card__actions">
            <RouterLink :to="`/products/${product.id}`" class="btn btn--ghost">View</RouterLink>
            <button class="btn" type="button" @click="addToCart(product.id)">Select size</button>
          </div>
        </div>
      </article>
    </div>
  </section>
</template>
