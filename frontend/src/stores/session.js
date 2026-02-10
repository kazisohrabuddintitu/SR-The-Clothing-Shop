import { computed, ref } from 'vue'

const storedUser = localStorage.getItem('sr_user')
const user = ref(storedUser ? JSON.parse(storedUser) : null)

const setUser = (value) => {
  user.value = value
  if (value) {
    localStorage.setItem('sr_user', JSON.stringify(value))
  } else {
    localStorage.removeItem('sr_user')
  }
}

const clearUser = () => setUser(null)

const isAdmin = computed(() => Boolean(user.value?.is_admin))

export const useSession = () => ({
  user,
  setUser,
  clearUser,
  isAdmin,
})
