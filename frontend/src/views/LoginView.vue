<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { login } = useAuth()

const form = ref({
  email: '',
  password: '',
})
const error = ref('')

const submit = async () => {
  error.value = ''
  try {
    await login(form.value)
    router.push({ name: 'dashboard' })
  } catch (err) {
    error.value = 'Invalid email or password.'
  }
}
</script>

<template>
  <section class="auth">
    <div class="card">
      <h1>Login</h1>
      <form class="form" @submit.prevent="submit">
        <label>
          Email
          <input v-model="form.email" type="email" required />
        </label>
        <label>
          Password
          <input v-model="form.password" type="password" required />
        </label>
        <button class="btn" type="submit">Login</button>
        <p v-if="error" class="notice">{{ error }}</p>
      </form>
    </div>
  </section>
</template>
