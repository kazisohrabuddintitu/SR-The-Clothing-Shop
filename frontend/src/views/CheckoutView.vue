<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../services/api'

const router = useRouter()
const form = ref({
  address_line1: '',
  address_line2: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
})
const message = ref('')
const loading = ref(false)

const submit = async () => {
  loading.value = true
  try {
    await api.post('/orders', form.value)
    message.value = 'Order placed!'
    router.push({ name: 'dashboard' })
  } catch (error) {
    message.value = 'Unable to place order.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <section class="stack">
    <div class="page-header">
      <h1>Checkout</h1>
    </div>

    <form class="form" @submit.prevent="submit">
      <label>
        Address line 1
        <input v-model="form.address_line1" required />
      </label>
      <label>
        Address line 2
        <input v-model="form.address_line2" />
      </label>
      <div class="form-grid">
        <label>
          City
          <input v-model="form.city" required />
        </label>
        <label>
          State
          <input v-model="form.state" />
        </label>
      </div>
      <div class="form-grid">
        <label>
          Postal code
          <input v-model="form.postal_code" required />
        </label>
        <label>
          Country
          <input v-model="form.country" required />
        </label>
      </div>
      <button class="btn" type="submit" :disabled="loading">
        {{ loading ? 'Placing order...' : 'Place order' }}
      </button>
      <p v-if="message" class="notice">{{ message }}</p>
    </form>
  </section>
</template>
