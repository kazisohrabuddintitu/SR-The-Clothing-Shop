import { ref } from 'vue'
import { api, ensureCsrfCookie } from '../services/api'
import { useSession } from '../stores/session'

const loading = ref(false)
const initialized = ref(false)

export const useAuth = () => {
  const { user, setUser, clearUser, isAdmin } = useSession()

  const fetchMe = async () => {
    try {
      loading.value = true
      const { data } = await api.get('/auth/me')
      setUser(data.user)
    } catch (error) {
      clearUser()
    } finally {
      loading.value = false
      initialized.value = true
    }
  }

  const login = async (payload) => {
    await ensureCsrfCookie()
    await api.post('/auth/login', payload)
    await ensureCsrfCookie()
    await fetchMe()
  }

  const register = async (payload) => {
    await ensureCsrfCookie()
    await api.post('/auth/register', payload)
    await ensureCsrfCookie()
    await fetchMe()
  }

  const logout = async () => {
    try {
      await ensureCsrfCookie()
      await api.post('/auth/logout')
    } finally {
      clearUser()
    }
  }

  return {
    user,
    isAdmin,
    loading,
    initialized,
    fetchMe,
    login,
    register,
    logout,
  }
}
