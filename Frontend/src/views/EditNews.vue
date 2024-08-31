<template>
  <div class="container">
    <h4 class="fw-bold mb-5">Edit News</h4>
    <div class="mb-3 justify-content-between p-3 content">
      <form @submit.prevent="handleSubmit" class="mt-3">
        <!-- Category Selection -->
        <div class="mb-3">
          <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
          <select id="category" v-model="selectedCategory" class="form-select" required>
            <option value="" disabled>Choose item</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        <!-- Title Input -->
        <div class="mb-3">
          <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
          <input type="text" id="title" v-model="title" class="form-control" required />
        </div>
        <!-- Slug Input -->
        <div class="mb-3">
          <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
          <input type="text" id="slug" v-model="slug" class="form-control" required />
        </div>
        <!-- Image URL Input -->
        <div class="mb-3">
          <label for="image" class="form-label">Image URL</label>
          <input type="text" id="image" v-model="imageUrl" class="form-control" placeholder="Enter image URL" />
          <div v-if="imageUrl" class="mb-3">
            <img :src="getImageUrl(imageUrl)" alt="Image Preview" class="img-preview" />
          </div>
        </div>
        <!-- Content Editor -->
        <div class="mb-3">
          <label for="content" class="form-label">Content</label>
          <CKEditor :content="content" @updateContent="handleContent" />
        </div>
        <!-- Status Selection -->
        <div class="mb-3">
          <label class="form-label">Status:</label>
          <div class="form-check form-check-inline">
            <input 
              type="radio" 
              id="published" 
              value="1" 
              v-model="status" 
              class="form-check-input" 
            />
            <label for="published" class="form-check-label">Published</label>
          </div>
          <div class="form-check form-check-inline">
            <input 
              type="radio" 
              id="unpublished" 
              value="2" 
              v-model="status" 
              class="form-check-input" 
            />
            <label for="unpublished" class="form-check-label">Unpublished</label>
          </div>
          <div class="form-check form-check-inline">
            <input 
              type="radio" 
              id="draft" 
              value="0" 
              v-model="status" 
              class="form-check-input" 
            />
            <label for="draft" class="form-check-label">Draft</label>
          </div>
        </div>
        <!-- Buttons -->
        <div class="d-flex justify-content-center gap-2">
          <button type="button" class="btn btn-secondary" @click="handleCancel">Cancel</button>
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted,  } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import CKEditor from '@/components/CKEditorComponent.vue';

// Reactive data properties
const categories = ref([
  { id: 1, name: 'Adventure Travel' },
  { id: 2, name: 'Beach Explore' },
  { id: 3, name: 'World' },
  { id: 4, name: 'Family Holidays' },
  { id: 5, name: 'Art and culture' }
]);
const selectedCategory = ref("");
const title = ref("");
const slug = ref("");
const imageUrl = ref("");
const content = ref("");
const status = ref("1"); // Default to published
const apiError = ref("");
const successMessage = ref("");
const success = ref(false);

// Get route parameters and router instance
const route = useRoute();
const router = useRouter();

// Fetch the news item by ID
const fetchNewsItem = async (id) => {
  try {
    const response = await axios.get(`http://localhost:8000/api/posts/${id}`);
    const item = response.data;
    console.log(item)

    // Populate form with fetched data
    selectedCategory.value = item.category_id;
    title.value = item.title;
    slug.value = item.slug;
    imageUrl.value = item.image;
    content.value = item.content;
    status.value = item.status.toString();
  } catch (error) {
    apiError.value = 'Có lỗi xảy ra khi tải dữ liệu bài viết.';
    console.error('Error fetching news item:', error);
  }
};

// Handle form submission
const handleSubmit = async () => {
  const id = route.params.id; // Get the ID from route params
  if (!id) {
    apiError.value = 'Invalid ID';
    return;
  }

  try {
    // Determine which endpoint to call based on the status
    const endpoint = status.value === "0" 
      ? `http://localhost:8000/api/posts/draft/${id}` 
      : `http://localhost:8000/api/posts/${id}`;

    await axios.put(endpoint, {
      category_id: selectedCategory.value,
      image: imageUrl.value,
      title: title.value,
      slug: slug.value,
      content: content.value,
      status: status.value
    });

    successMessage.value = 'Bài viết đã được cập nhật thành công!';
    success.value = true;
    alert('Bài viết đã được cập nhật thành công!');
    router.push('/admin/list-news');
  } catch (error) {
    apiError.value = 'Có lỗi xảy ra khi cập nhật bài viết.';
    alert('Có lỗi xảy ra khi cập nhật bài viết.');
    console.error('Error updating news item:', error);
  }
};

// Handle cancel button click
const handleCancel = () => {
  router.push('/admin/list-news');
};

// Initialize component
onMounted(() => {
  const id = route.params.id;
  if (id) {
    fetchNewsItem(id);
  } else {
    apiError.value = 'Invalid ID';
  }
});

const getImageUrl = (imagePath) => {
    // Kiểm tra xem đường dẫn có bắt đầu bằng '/' hay chưa, nếu chưa thì thêm vào
    if (!imagePath.startsWith('/')) {
      imagePath = '/' + imagePath;
    }
    return `http://localhost:8080${imagePath}`;
};

</script>

<style scoped>
.img-preview {
  max-width: 100%;
  height: auto;
}
</style>
