<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { register } = useAuth()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const error = ref('')

const submit = async () => {
  error.value = ''
  try {
    await register(form.value)
    router.push({ name: 'dashboard' })
  } catch (err) {
    error.value = 'Unable to register. Check your details.'
  }
}
</script>

<template>
  <section class="auth">
    <div class="card">
      <h1>Create account</h1>
      <form class="form" @submit.prevent="submit">
        <label>
          Name
          <input v-model="form.name" required />
        </label>
        <label>
          Email
          <input v-model="form.email" type="email" required />
        </label>
        <label>
          Password
          <input v-model="form.password" type="password" required />
        </label>
        <label>
          Confirm password
          <input v-model="form.password_confirmation" type="password" required />
        </label>
        <button class="btn" type="submit">Register</button>
        <p v-if="error" class="notice">{{ error }}</p>
      </form>
    </div>
  </section>
</template>
