<template>
  <div class="container">
    <h4 class="fw-bold mb-5">Create News</h4>
    <div class="mb-3 justify-content-between p-3 content">
      <form @submit.prevent="handleSubmit" class="mt-3">
        <div class="mb-3">
          <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
          <select id="category" v-model="selectedCategory" class="form-select" required>
            <option value="" disabled>Choose item</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        <div class="mb-3">
          <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
          <input type="text" id="title" v-model="title" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
          <input type="text" id="slug" v-model="slug" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="image" class="form-label">Image URL</label>
          <input type="text" id="image" v-model="imageUrl" class="form-control" placeholder="Enter image URL" />
          <div v-if="imageUrl" class="mb-3">
            <img :src="getImageUrl(imageUrl)" alt="Image Preview" class="img-preview" />
          </div>
        </div>
        <div class="mb-3">
          <label for="content" class="form-label">Content</label>
          <CKEditor :content="content" @updateContent="handleContent" />
        </div>
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
        <div class="d-flex justify-content-center gap-2">
          <button type="button" class="btn btn-secondary" @click="handleCancel">Cancel</button>
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import router from "@/router";
import { ref, inject, watch } from "vue";
import CKEditor from "@/components/CKEditorComponent.vue";
import axios from 'axios';



const showDialog = inject("showDialog");
const confirm = inject("confirm");
const resetConfirm = inject("resetConfirm");


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

const handleContent = (editorData) => {
  content.value = editorData;
};

const handleCancel = () => {
  router.push("/admin/list-news");
};

const handleSubmit = async () => {
  showDialog("Are you sure to add this item?", "Add");

  watch(confirm, async () => {
    if (confirm.value) {
      const newItem = {
        category_id: selectedCategory.value,
        image: imageUrl.value,
        title: title.value,
        slug: slug.value,
        content: content.value,
        status: status.value, // Giữ lại trường status
      };

      try {
        if (status.value === "0") {
          // Gọi API tạo nháp
          await axios.post('http://localhost:8000/api/posts/draft', newItem);
        } else {
          // Gọi API tạo bài viết bình thường
          await axios.post('http://localhost:8000/api/posts', newItem);
        }

        resetConfirm();
        router.push("/admin/list-news");
        alert('Bài viết đã được thêm thành công!');
      } catch (error) {
        console.error('Error adding news item:', error);
        alert('Có lỗi xảy ra khi thêm bài viết.');
      }
    }
  });
};
const getImageUrl = (imagePath) => {
    // Kiểm tra xem đường dẫn có bắt đầu bằng '/' hay chưa, nếu chưa thì thêm vào
    if (!imagePath.startsWith('/')) {
      imagePath = '/' + imagePath;
    }
    return `http://localhost:8080${imagePath}`;
};

</script>


