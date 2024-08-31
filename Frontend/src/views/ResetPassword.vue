<template>
  <div class="container">
    <h1 class="display-4 mb-3" v-if="!success">Reset Password</h1>
    <!-- Form nhập mật khẩu mới -->
    <form @submit.prevent="handleSubmit" v-if="!success">
      <div class="form-group mb-3">
        <label for="password">New Password</label>
        <input type="password" v-model="password" class="form-control" id="password" placeholder="Enter new password">
      </div>
      <div class="form-group mb-3">
        <label for="password_confirmation">Confirm New Password</label>
        <input type="password" v-model="passwordConfirmation" class="form-control" id="password_confirmation" placeholder="Confirm new password">
      </div>
      <button type="submit" class="btn btn-success w-100">Reset Password</button>
    </form>

    <!-- Thông báo lỗi khi cập nhật thất bại -->
    <div v-if="apiError" class="text-danger mt-3">{{ apiError }}</div>

    <!-- Thông báo khi cập nhật thành công -->
    <div v-if="success" class="text-success mt-3">
      <p>{{ successMessage }}</p>
      <button @click="goToLogin" class="btn btn-primary">Go to Login</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';

// Hook để lấy route params
const route = useRoute();
const router = useRouter();

// Define reactive variables
const password = ref('');
const passwordConfirmation = ref('');
const apiError = ref('');
const successMessage = ref('');
const success = ref(false);

// Get token from query params
const token = ref(route.query.token || '');

// Handle form submission
const handleSubmit = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/reset-password', {
      token: token.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    });

    if (response.data.message) {
      successMessage.value = response.data.message;
      success.value = true; // Update state to show success message
    }
  } catch (error) {
    if (error.response && error.response.data.message) {
      apiError.value = error.response.data.message;
    } else {
      apiError.value = 'An error occurred. Please try again.';
    }
  }
};

// Redirect to login page
const goToLogin = () => {
  router.push('/'); 
};

// Optional: Fetch token if needed, or perform additional initialization
onMounted(() => {
  // Any initialization or additional logic can go here
});
</script>



  
  <style scoped>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
  }
  
  body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f7f7f7;
  }

  .container {
    width: 404px; 
    height: 390px;
    top: 230px;
    left: 165px;
  }
  
  .eye-icon {
    position: absolute;
    right: 10px;
    top: 70%; 
    transform: translateY(-50%);
    cursor: pointer;
    width: 20px;
    height: 20px;
  }
  
  .text-danger {
    color: red;
    font-size: 0.875em;
  }
  
  h1 {
    font-size: 2em;
    margin-bottom: 10px;
  }
  
  p {
    margin-bottom: 30px;
    color: #666;
  }
  
  label {
    margin-bottom: 5px;
    font-weight: bold;
  }
  
  input[type="password"] {
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1em;
  }
  
  input::placeholder {
    opacity: 0.3;
    color: #000;
  }
  
  button {
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    width: 100%; 
  }
  
  button:hover {
    background-color: #45a049;
  }
  </style>
  