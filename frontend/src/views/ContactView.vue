<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../services/api'

const contact = ref({
  email: '',
  phone: '',
  address: '',
  hours: '',
})

const form = ref({
  name: '',
  email: '',
  message: '',
})

const status = ref('')
const error = ref('')

const loadContact = async () => {
  const { data } = await api.get('/contact-info')
  contact.value = data.contact
}

const submit = async () => {
  status.value = ''
  error.value = ''
  try {
    await api.post('/contact', form.value)
    status.value = 'Message sent. We will get back to you soon.'
    form.value.message = ''
  } catch (err) {
    error.value = 'Unable to send message.'
  }
}

onMounted(loadContact)
</script>

<template>
  <section class="stack">
    <div class="page-header">
      <h1>Contact</h1>
    </div>

    <div class="card">
      <h2>Contact information</h2>
      <div class="stack">
        <p v-if="contact.email"><strong>Email:</strong> {{ contact.email }}</p>
        <p v-if="contact.phone"><strong>Phone:</strong> {{ contact.phone }}</p>
        <p v-if="contact.address"><strong>Address:</strong> {{ contact.address }}</p>
        <p v-if="contact.hours"><strong>Hours:</strong> {{ contact.hours }}</p>
      </div>
    </div>

    <div class="card">
      <h2>Send a message</h2>
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
          Message
          <textarea v-model="form.message" rows="4" required></textarea>
        </label>
        <button class="btn" type="submit">Send message</button>
        <p v-if="status" class="notice">{{ status }}</p>
        <p v-if="error" class="notice">{{ error }}</p>
      </form>
    </div>
  </section>
</template>
