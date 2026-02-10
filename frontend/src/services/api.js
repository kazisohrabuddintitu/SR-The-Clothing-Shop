import axios from 'axios'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const rootUrl = apiBaseUrl.replace(/\/api$/, '')

export const api = axios.create({
  baseURL: apiBaseUrl,
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
})

const csrfClient = axios.create({
  baseURL: rootUrl,
  withCredentials: true,
})

api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const getCookie = (name) => {
  const match = document.cookie
    .split('; ')
    .find((row) => row.startsWith(`${name}=`))

  return match ? match.split('=').slice(1).join('=') : null
}

api.interceptors.request.use((config) => {
  const token = getCookie('XSRF-TOKEN')
  if (token) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token)
  }
  return config
})

export const ensureCsrfCookie = async () => {
  await csrfClient.get('/sanctum/csrf-cookie')
  const token = getCookie('XSRF-TOKEN')
  if (token) {
    api.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(token)
  }
}
