<template>
  <div>
    <!-- News Header -->
    <div class="our-news-header">
      <h1 class="text-start">Our News</h1>
    </div>

    <!-- Main Content -->
    <div class="container">
      <!-- Articles Header -->
      <header class="text-center my-4">
        <h1>Travelaja Articles</h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit ut aliquam</p>
      </header>

      <!-- Categories Navigation -->
      <nav class="nav justify-content-center mb-4">
        <a
          v-for="category in categories"
          :key="category.id"
          :class="['nav-link', selectedCategory === category.id ? 'active' : '']"
          @click.prevent="fetchNewsByCategory(category.id)"
          href="#"
        >
          {{ category.name }}
        </a>
      </nav>

      <!-- News Cards -->
      <div class="row">
        <div class="col-md-4 mb-4" v-for="item in news" :key="item.id">
          <div class="card" @click="newsdetail(item)">
            <img :src="item.image" class="card-img-top" alt="News Image">
            <div class="card-body">
              <h5 class="card-title">{{ item.title }}</h5>
              <p class="card-text">
                <small class="text-muted">{{ formatDate(item.created_at) }}</small>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';  // Import useRouter
import axios from 'axios';

// State variables
const categories = ref([]);
const news = ref([]);
const selectedCategory = ref(null);
const router = useRouter();  // Initialize router

// Fetch categories from the API
const fetchCategories = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/categories');
    categories.value = response.data;
    if (categories.value.length > 0) {
      // Automatically load the first category's news
      selectedCategory.value = categories.value[0].id;
      fetchNewsByCategory(selectedCategory.value);
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

// Fetch news by category ID
const fetchNewsByCategory = async (categoryId) => {
  try {
    selectedCategory.value = categoryId;
    const response = await axios.get(`http://localhost:8000/api/posts/category/${categoryId}`);
    news.value = response.data;
  } catch (error) {
    console.error('Error fetching news:', error);
  }
};

// Format date
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

// Redirect to news detail page
const newsdetail = (item) => {
  router.push(`/news-detail/${item.slug}`);
};

// Fetch categories on component mount
onMounted(() => {
  fetchCategories();
});
</script>

  
  <style scoped>
  

  .our-news-header {
    background-image: url('../../public/assets/home/news/Background.png');
    background-size: cover;
    background-position: center;
    height: 250px;
    display: flex;
    align-items: flex-end;
    padding: 0px 0px 30px 90px;
}

.our-news-header h1 {
    font-size: 33.18px;
    font-weight: 700;
    line-height: 39.82px;
    color: white;
    width: 188px;
    margin: 0;
}

.paragraph {
    font-size: 16px;
    font-weight: 400;
    line-height: 28.8px;
}

.form-label {
    font-size: 16px;
    font-weight: 400;
    line-height: 28.8px;
}

.card{
  cursor: pointer;
}

.card-link {
    text-decoration: none;
}
.nav-link {
    font-size: 16px;
    font-weight: 400;
    line-height: 28.8px;
    color: #3D3E48;
}

.nav-link.active {
    font-weight: 700;
    color: #43B97F;
}


  </style>
  