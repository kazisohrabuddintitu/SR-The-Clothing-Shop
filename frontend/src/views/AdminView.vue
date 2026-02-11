<script setup>
import { onMounted, ref } from 'vue'
import { api, ensureCsrfCookie } from '../services/api'

const products = ref([])
const orders = ref([])
const ordersLoading = ref(false)
const ordersError = ref('')
const filterFrom = ref('')
const filterTo = ref('')
const filterOrderId = ref('')
const contactForm = ref({
  email: '',
  phone: '',
  address: '',
  hours: '',
})
const contactStatus = ref('')
const imageFile = ref(null)
const form = ref({
  id: null,
  name: '',
  description: '',
  price: '',
  stock: 0,
  image_url: '',
  category: '',
  sizes: {
    S: 0,
    M: 0,
    L: 0,
    XL: 0,
  },
})

const statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']

const loadProducts = async () => {
  const { data } = await api.get('/products')
  products.value = data.products
}

const loadOrders = async () => {
  ordersLoading.value = true
  ordersError.value = ''
  try {
    const params = {
      from: filterFrom.value || undefined,
      to: filterTo.value || undefined,
      order_id: filterOrderId.value || undefined,
    }
    const { data } = await api.get('/admin/orders', { params })
    orders.value = data.orders || []
  } catch (error) {
    const status = error?.response?.status
    if (status === 401 || status === 403) {
      ordersError.value = 'Admin access required. Please log in as admin.'
    } else {
      ordersError.value = 'Unable to load admin orders.'
    }
  } finally {
    ordersLoading.value = false
  }
}

const formatDate = (date) => date.toISOString().slice(0, 10)

const resetFilters = () => {
  const now = new Date()
  const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1)
  filterFrom.value = formatDate(startOfMonth)
  filterTo.value = formatDate(now)
  filterOrderId.value = ''
  loadOrders()
}

const loadContactInfo = async () => {
  const { data } = await api.get('/contact-info')
  contactForm.value = {
    email: data.contact.email || '',
    phone: data.contact.phone || '',
    address: data.contact.address || '',
    hours: data.contact.hours || '',
  }
}

const saveContactInfo = async () => {
  contactStatus.value = ''
  try {
    await ensureCsrfCookie()
    const { data } = await api.put('/admin/contact-info', contactForm.value)
    contactForm.value = {
      email: data.contact.email || '',
      phone: data.contact.phone || '',
      address: data.contact.address || '',
      hours: data.contact.hours || '',
    }
    contactStatus.value = 'Contact information updated.'
  } catch (error) {
    contactStatus.value = 'Unable to save contact info. Please refresh and try again.'
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    name: '',
    description: '',
    price: '',
    stock: 0,
    image_url: '',
    category: '',
    sizes: {
      S: 0,
      M: 0,
      L: 0,
      XL: 0,
    },
  }
  imageFile.value = null
}

const editProduct = async (product) => {
  const { data } = await api.get(`/products/${product.id}`)
  const sizes = data.product.sizes || []

  const sizeMap = sizes.reduce((acc, size) => {
    acc[size.size] = size.stock
    return acc
  }, {})

  form.value = {
    id: data.product.id,
    name: data.product.name,
    description: data.product.description,
    price: data.product.price,
    stock: data.product.stock,
    image_url: data.product.image_url || '',
    category: data.product.category || '',
    sizes: {
      S: sizeMap.S ?? 0,
      M: sizeMap.M ?? 0,
      L: sizeMap.L ?? 0,
      XL: sizeMap.XL ?? 0,
    },
  }
}

const uploadImage = async () => {
  if (!imageFile.value) return null
  const payload = new FormData()
  payload.append('image', imageFile.value)
  const { data } = await api.post('/admin/uploads', payload, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return data.url
}

const saveProduct = async () => {
  const uploadedUrl = await uploadImage()
  const sizes = Object.entries(form.value.sizes).map(([size, stock]) => ({
    size,
    stock: Number(stock),
  }))

  const payload = {
    name: form.value.name,
    description: form.value.description,
    price: Number(form.value.price),
    stock: Number(form.value.stock),
    image_url: uploadedUrl || form.value.image_url || null,
    category: form.value.category || null,
    sizes,
  }

  if (form.value.id) {
    await api.put(`/products/${form.value.id}`, payload)
  } else {
    await api.post('/products', payload)
  }

  await loadProducts()
  resetForm()
}

const deleteProduct = async (productId) => {
  await api.delete(`/products/${productId}`)
  await loadProducts()
}

const updateOrderStatus = async (order, status) => {
  await api.patch(`/admin/orders/${order.id}/status`, { status })
  await loadOrders()
}

const printOrder = (order) => {
  const items = order.items
    .map(
      (item) =>
        `<li>${item.product?.name || 'Product'} (${item.size}) × ${item.quantity} - $${Number(item.price).toFixed(2)}</li>`
    )
    .join('')

  const addressLine2 = order.address_line2 ? `, ${order.address_line2}` : ''
  const state = order.state ? `, ${order.state}` : ''

  const html = `
    <html>
      <head>
        <title>Order #${order.id} - Print</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 24px; }
          h1 { margin: 0; font-size: 22px; }
          .muted { color: #555; }
          .brand { display: flex; justify-content: space-between; align-items: center; }
          .brand-title { font-size: 20px; font-weight: 700; letter-spacing: 0.04em; }
          .brand-subtitle { font-size: 12px; color: #666; margin-top: 4px; }
          .divider { border-top: 2px solid #111; margin: 16px 0; }
          .meta { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
          .section { margin-top: 16px; }
          .section-title { font-size: 14px; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
          .totals { font-weight: 700; font-size: 16px; }
          ul { padding-left: 18px; }
          @media print {
            body { padding: 0; }
          }
        </style>
      </head>
      <body>
        <div class="brand">
          <div>
            <div class="brand-title">SR- The Clothing Brand</div>
            <div class="brand-subtitle">Packing Slip</div>
          </div>
          <div>
            <div class="muted">Order #${order.id}</div>
            <div class="muted">Status: ${order.status}</div>
          </div>
        </div>
        <div class="divider"></div>
        <div class="meta">
          <div>
            <div class="section-title">Ship To</div>
            <p>
              ${order.address_line1}${addressLine2}<br />
              ${order.city}${state} ${order.postal_code}<br />
              ${order.country}
            </p>
          </div>
          <div>
            <div class="section-title">Customer</div>
            <p>${order.user?.email || ''}</p>
            <div class="section-title">Total</div>
            <p class="totals">$${Number(order.total).toFixed(2)}</p>
          </div>
        </div>
        <div class="section">
          <div class="section-title">Items</div>
          <ul>${items}</ul>
        </div>
      </body>
    </html>
  `

  const printWindow = window.open('', '_blank', 'width=800,height=600')
  if (!printWindow) return
  printWindow.document.write(html)
  printWindow.document.close()
  printWindow.focus()
  printWindow.print()
  printWindow.close()
}

const onFileChange = (event) => {
  const [file] = event.target.files
  imageFile.value = file || null
}

onMounted(() => {
  loadProducts()
  loadContactInfo()
  resetFilters()
})
</script>

<template>
  <section class="stack">
    <div class="page-header">
      <h1>Admin dashboard</h1>
    </div>

    <div class="card">
      <h2>{{ form.id ? 'Edit product' : 'Add product' }}</h2>
      <form class="form" @submit.prevent="saveProduct">
        <label>
          Name
          <input v-model="form.name" required />
        </label>
        <label>
          Description
          <textarea v-model="form.description" rows="3"></textarea>
        </label>
        <div class="form-grid">
          <label>
            Price
            <input v-model="form.price" type="number" min="0" step="0.01" required />
          </label>
          <label>
            Stock
            <input v-model="form.stock" type="number" min="0" required />
          </label>
        </div>
        <label>
          Image URL
          <input v-model="form.image_url" type="url" />
        </label>
        <label>
          Upload image
          <input type="file" accept="image/*" @change="onFileChange" />
        </label>
        <label>
          Category
          <input v-model="form.category" />
        </label>
        <div class="form-grid">
          <label>
            Size S stock
            <input v-model.number="form.sizes.S" type="number" min="0" />
          </label>
          <label>
            Size M stock
            <input v-model.number="form.sizes.M" type="number" min="0" />
          </label>
          <label>
            Size L stock
            <input v-model.number="form.sizes.L" type="number" min="0" />
          </label>
          <label>
            Size XL stock
            <input v-model.number="form.sizes.XL" type="number" min="0" />
          </label>
        </div>
        <div class="form-actions">
          <button class="btn" type="submit">
            {{ form.id ? 'Update' : 'Create' }}
          </button>
          <button class="btn btn--ghost" type="button" @click="resetForm">Clear</button>
        </div>
      </form>
    </div>

    <div class="card">
      <h2>Products</h2>
      <div v-if="products.length === 0" class="muted">No products yet.</div>
      <div v-else class="stack">
        <div v-for="product in products" :key="product.id" class="row">
          <div>
            <strong>{{ product.name }}</strong>
            <div class="muted">${{ Number(product.price).toFixed(2) }} · {{ product.stock }} in stock</div>
          </div>
          <div class="row-actions">
            <button class="btn btn--ghost" type="button" @click="editProduct(product)">Edit</button>
            <button class="btn btn--danger" type="button" @click="deleteProduct(product.id)">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <h2>Contact settings</h2>
      <form class="form" @submit.prevent="saveContactInfo">
        <label>
          Contact email
          <input v-model="contactForm.email" type="email" />
        </label>
        <label>
          Phone
          <input v-model="contactForm.phone" />
        </label>
        <label>
          Address
          <input v-model="contactForm.address" />
        </label>
        <label>
          Hours
          <input v-model="contactForm.hours" placeholder="e.g. Mon-Fri 9am-6pm" />
        </label>
        <button class="btn" type="submit">Save contact info</button>
        <p v-if="contactStatus" class="notice">{{ contactStatus }}</p>
      </form>
    </div>

    <div class="card">
      <h2>Orders</h2>
      <div class="filters">
        <label>
          From
          <input v-model="filterFrom" type="date" />
        </label>
        <label>
          To
          <input v-model="filterTo" type="date" />
        </label>
        <label>
          Order #
          <input v-model="filterOrderId" type="number" min="1" placeholder="e.g. 102" />
        </label>
        <div class="filter-actions">
          <button class="btn btn--ghost" type="button" @click="loadOrders">Apply</button>
          <button class="btn btn--ghost" type="button" @click="resetFilters">This month</button>
        </div>
      </div>
      <div v-if="ordersLoading" class="muted">Loading orders...</div>
      <div v-else-if="ordersError" class="notice">{{ ordersError }}</div>
      <div v-else-if="orders.length === 0" class="muted">No orders yet.</div>
      <div v-else class="stack">
        <div v-for="order in orders" :key="order.id" class="order-card">
          <div class="order-card__header">
            <strong>Order #{{ order.id }}</strong>
            <span class="muted">{{ order.user ? order.user.email : '' }}</span>
          </div>
          <p class="muted">Status: {{ order.status }}</p>
          <p class="muted">Total: ${{ Number(order.total).toFixed(2) }}</p>
          <div class="order-details">
            <strong>Items</strong>
            <ul>
              <li v-for="item in order.items" :key="item.id">
                {{ item.product ? item.product.name : 'Product' }} ({{ item.size }}) × {{ item.quantity }}
                - ${{ Number(item.price).toFixed(2) }}
              </li>
            </ul>
            <strong>Shipping address</strong>
            <p class="muted">
              {{ order.address_line1 }}<span v-if="order.address_line2">, {{ order.address_line2 }}</span>
              <br />
              {{ order.city }}<span v-if="order.state">, {{ order.state }}</span> {{ order.postal_code }}
              <br />
              {{ order.country }}
            </p>
          </div>
          <div class="order-actions">
            <select :value="order.status" @change="updateOrderStatus(order, $event.target.value)">
              <option v-for="status in statuses" :key="status" :value="status">
                {{ status }}
              </option>
            </select>
            <button class="btn btn--ghost" type="button" @click="printOrder(order)">
              Print
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
