<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from './composables/useAuth'

const router = useRouter()
const { user, isAdmin, fetchMe, logout } = useAuth()
const navHidden = ref(false)
const navOpen = ref(false)
let lastScrollY = 0
let onScroll = null

onMounted(() => {
  fetchMe()

  lastScrollY = window.scrollY
  onScroll = () => {
    const currentScroll = window.scrollY
    const delta = currentScroll - lastScrollY
    if (Math.abs(delta) > 8) {
      navHidden.value = delta > 0 && currentScroll > 80
      lastScrollY = currentScroll
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true })
})

onBeforeUnmount(() => {
  if (onScroll) {
    window.removeEventListener('scroll', onScroll)
  }
})

const handleLogout = async () => {
  await logout()
  router.push({ name: 'home' })
  navOpen.value = false
}

const toggleNav = () => {
  navOpen.value = !navOpen.value
}

const closeNav = () => {
  navOpen.value = false
}
</script>

<template>
  <div class="app">
    <header class="nav" :class="{ 'nav--hidden': navHidden }">
      <div class="nav__brand">
        <RouterLink to="/" class="brand-link">
          <img src="/logo.png" alt="SR The Clothing Brand" class="brand-logo" />
          <span>SR- The Clothing Brand</span>
        </RouterLink>
      </div>
      <button class="nav__toggle" type="button" @click="toggleNav" aria-label="Toggle menu">
        ☰
      </button>
      <div class="nav__menu" :class="{ 'nav__menu--open': navOpen }" @click="closeNav">
        <nav class="nav__links">
          <RouterLink to="/">Shop</RouterLink>
          <RouterLink to="/cart">Cart</RouterLink>
          <RouterLink to="/contact">Contact</RouterLink>
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
      </div>
    </header>

    <main class="container">
      <RouterView />
    </main>
    <footer class="footer">
      <p>All rights reserved to SR.</p>
    </footer>
  </div>
</template>
