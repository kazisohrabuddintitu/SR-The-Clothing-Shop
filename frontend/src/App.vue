<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from './composables/useAuth'

const router = useRouter()
const { user, isAdmin, fetchMe, logout } = useAuth()

onMounted(() => {
  fetchMe()
})

const handleLogout = async () => {
  await logout()
  router.push({ name: 'home' })
}
</script>

<template>
  <div class="app">
    <header class="nav">
      <div class="nav__brand">
        <RouterLink to="/">SR- The Clothing Brand</RouterLink>
      </div>
      <nav class="nav__links">
        <RouterLink to="/">Shop</RouterLink>
        <RouterLink to="/cart">Cart</RouterLink>
        <RouterLink v-if="user" to="/dashboard">Dashboard</RouterLink>
        <RouterLink v-if="isAdmin" to="/admin">Admin</RouterLink>
      </nav>
      <div class="nav__auth">
        <span v-if="user" class="nav__user">Hi, {{ user.name }}</span>
        <RouterLink v-if="!user" to="/login">Login</RouterLink>
        <RouterLink v-if="!user" to="/register">Register</RouterLink>
        <button v-if="user" class="btn btn--ghost" type="button" @click="handleLogout">
          Logout
        </button>
      </div>
    </header>

    <main class="container">
      <RouterView />
    </main>
  </div>
</template>
