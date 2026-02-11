<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../services/api'
import { useSession } from '../stores/session'

const route = useRoute()
const router = useRouter()
const { user } = useSession()

const product = ref(null)
const similarProducts = ref([])
const reviews = ref([])
const selectedSize = ref('')
const quantity = ref(1)
const message = ref('')
const reviewError = ref('')
const reviewSuccess = ref('')
const reviewForm = ref({
  rating: 5,
  comment: '',
})

const loadProduct = async () => {
  const { data } = await api.get(`/products/${route.params.id}`)
  product.value = data.product
  similarProducts.value = data.similar_products || []
  reviews.value = data.product.reviews || []
  selectedSize.value = data.product.sizes?.find((size) => size.stock > 0)?.size || ''
}

const addToCart = async () => {
  if (!user.value) {
    router.push({ name: 'login' })
    return
  }

  if (!selectedSize.value) {
    message.value = 'Select a size before adding to cart.'
    return
  }

  await api.post('/cart/items', {
    product_id: product.value.id,
    size: selectedSize.value,
    quantity: quantity.value,
  })
  message.value = 'Added to cart.'
}

const submitReview = async () => {
  if (!user.value) {
    router.push({ name: 'login' })
    return
  }

  reviewError.value = ''
  reviewSuccess.value = ''

  try {
    const { data } = await api.post(`/products/${product.value.id}/reviews`, reviewForm.value)
    const existingIndex = reviews.value.findIndex((review) => review.user_id === data.review.user_id)
    if (existingIndex >= 0) {
      reviews.value.splice(existingIndex, 1, data.review)
    } else {
      reviews.value.unshift(data.review)
    }
    reviewForm.value.comment = ''
    reviewSuccess.value = 'Review submitted.'
  } catch (error) {
    const status = error?.response?.status
    if (status === 403) {
      reviewError.value = 'You can only review products you have purchased.'
    } else {
      reviewError.value = 'Unable to submit review.'
    }
  }
}

const sizesInStock = computed(() => product.value?.sizes || [])

onMounted(loadProduct)
</script>

<template>
  <div v-if="product" class="stack">
    <section class="product">
      <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="product__image" />
      <div class="product__details">
        <h1>{{ product.name }}</h1>
        <p class="muted">{{ product.category }}</p>
        <p class="price">${{ Number(product.price).toFixed(2) }}</p>
        <p>{{ product.description }}</p>

        <div class="size-picker">
          <strong>Sizes</strong>
          <div class="size-options">
            <button
              v-for="size in sizesInStock"
              :key="size.id"
              class="size-chip"
              :class="{ 'size-chip--active': selectedSize === size.size }"
              :disabled="size.stock === 0"
              type="button"
              @click="selectedSize = size.size"
            >
              {{ size.size }} <span class="muted">({{ size.stock }})</span>
            </button>
          </div>
        </div>

        <div class="form-row">
          <label>
            Qty
            <input v-model.number="quantity" type="number" min="1" />
          </label>
          <button class="btn" type="button" @click="addToCart">Add to cart</button>
        </div>
        <p v-if="message" class="notice">{{ message }}</p>
      </div>
    </section>

    <section class="stack">
      <div class="card">
        <h2>Reviews</h2>
        <div v-if="reviews.length === 0" class="muted">No reviews yet.</div>
        <ul v-else class="stack">
          <li v-for="review in reviews" :key="review.id" class="review-item">
            <strong>{{ review.user ? review.user.name : 'Customer' }}</strong>
            <span class="muted">Rating: {{ review.rating }}/5</span>
            <p>{{ review.comment || 'No comment provided.' }}</p>
          </li>
        </ul>
        <div class="review-form">
          <h3>Add your review</h3>
          <form class="form" @submit.prevent="submitReview">
            <label>
              Rating
              <select v-model.number="reviewForm.rating">
                <option v-for="score in [5,4,3,2,1]" :key="score" :value="score">
                  {{ score }}
                </option>
              </select>
            </label>
            <label>
              Comment
              <textarea v-model="reviewForm.comment" rows="3"></textarea>
            </label>
            <button class="btn" type="submit">Submit review</button>
            <p v-if="reviewSuccess" class="notice">{{ reviewSuccess }}</p>
            <p v-if="reviewError" class="notice">{{ reviewError }}</p>
          </form>
        </div>
      </div>

      <div class="card">
        <h2>Similar products</h2>
        <div v-if="similarProducts.length === 0" class="muted">No similar products found.</div>
        <div v-else class="grid">
          <article v-for="item in similarProducts" :key="item.id" class="card">
            <img v-if="item.image_url" :src="item.image_url" :alt="item.name" />
            <div class="card__body">
              <h3>{{ item.name }}</h3>
              <p class="muted">{{ item.category || 'Essentials' }}</p>
              <p class="price">${{ Number(item.price).toFixed(2) }}</p>
              <RouterLink :to="`/products/${item.id}`" class="btn btn--ghost">View</RouterLink>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>
