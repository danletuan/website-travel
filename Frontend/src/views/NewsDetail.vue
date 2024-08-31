<template>
    <div>
      <div class="title-new">
        <div class="title-content">
          <h1 class="text-start text-nowrap overflow-hidden text-truncate">{{ news.title }}</h1>
          <div class="title-new-date">{{ formattedDate }}</div>
        </div>
      </div>
  
      <div class="content-new mt-5">
        <div class="container">
          <div class="row">
            <div class="col-8">
              <div v-html="news.content"></div>
            </div>
            <div class="col-4">
                    <div class="other-destinations">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Other Destinations</h4>
                            <a href="#" class="see-all">See all</a>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Wakatobi Beach Is A Paradise For Coral Reefs In Indonesia</h5>
                                <div class="card-text">Yogyakarta, Indonesia</div>
                                <a href="#" class="btn btn-success mt-5">View More</a>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Wakatobi Beach Is A Paradise For Coral Reefs In Indonesia</h5>
                                <div class="card-text">Yogyakarta, Indonesia</div>
                                <a href="#" class="btn btn-success mt-5">View More</a>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Wakatobi Beach Is A Paradise For Coral Reefs In Indonesia</h5>
                                <div class="card-text">Yogyakarta, Indonesia</div>
                                <a href="#" class="btn btn-success mt-5">View More</a>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Wakatobi Beach Is A Paradise For Coral Reefs In Indonesia</h5>
                                <div class="card-text">Yogyakarta, Indonesia</div>
                                <a href="#" class="btn btn-success mt-5">View More</a>
                            </div>
                        </div>

                    </div>
                </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import axios from 'axios';
  import { useRoute } from 'vue-router';
  
  // Lấy route params
  const route = useRoute();
  
  // State
  const news = ref({});
  const otherDestinations = ref([]);
  const apiError = ref('');
  
  // Tính toán
  const formattedDate = computed(() => {
    return new Date(news.value.created_at).toLocaleDateString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    });
  });
  
  // Fetch dữ liệu
  const fetchNews = async (slug) => {
    try {
      if (slug) {
        const encodedSlug = encodeURIComponent(slug);
        const response = await axios.get(`http://localhost:8000/api/posts/slug/${encodedSlug}`);
        news.value = response.data[0];
        otherDestinations.value = 'haha'
      } else {
        apiError.value = 'Invalid slug';
      }
    } catch (error) {
      console.error('Error fetching news:', error);
      apiError.value = 'Error fetching news';
    }
  };
  
  // Life cycle hook
  onMounted(() => {
    const slug = route.params.slug;
    fetchNews(slug);
  });
  </script>
  
  <style scoped>
body {
    font-family: Poppins;
}

.title-new {
    background-image: url('../../public/assets/home/news-detail/background.png');
    background-size: cover;
    background-position: center;
    height: 250px;
    display: flex;
    align-items: flex-end;
    padding: 0px 0px 30px 90px;
}

.title-content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.title-new h1 {
    font-size: 33.18px;
    font-weight: 700;
    line-height: 39.82px;
    color: white;
    margin: 0;
    width: 100%; 
}

.title-new-date {
    font-size: 18px;
    font-weight: 400;
    line-height: 32.4px;
    color: #FFFFFF;
}

.content-new {
    margin-top: 20px;
}
.content-new p {
    font-size: 16px;
    font-weight: 400;
    line-height: 28.8px;
    color: #000000;
}


.other-destinations {
    background-color: #f8f9fa;
    padding: 15px;
}

.see-all {
    color: #43B97F;
    text-decoration: none;
}

.card{
    background-image: url('../../public/assets/home/news-detail/image4.png');
    width: 380px; 
    height: 255px; 
    border-radius: 10px;
}

.card-title {
    font-size: 19.2px; 
    font-weight: 700; 
    line-height: 34.56px; 
    text-align: left;
    color: #FFFFFF;
}

.card-text {
    font-size: 12px; 
    font-weight: 700; 
    line-height: 21.6px; 
    text-align: left; 
    color: #FFFFFF;
}

.btn-success {
    background-color: #43B97F;
    border: none;
}

.btn-success:hover {
    background-color: #43B97F;
}


@media (max-width: 992px) {
    .title-new {
        padding: 0px 30px 30px 30px; 
    }

    .title-new h1 {
        font-size: 2rem; 
    }

    .title-new-date {
        font-size: 0.875rem; 
    }

    .content-new p {
        font-size: 0.875rem;
    }
}

@media (max-width: 768px) {
    .title-new {
        height: 200px; 
    }

    .title-new h1 {
        font-size: 1.5rem; 
    }

    .title-new-date {
        font-size: 0.75rem; 
    }

    .content-new p {
        font-size: 0.875rem;
    }
}
  </style>
  