<template>
  <div class="container">
    <h1 class="display-4 mb-3">Register</h1>
    <p class="text-muted mb-4">Enter your information</p>
    <form @submit.prevent="handleSubmit">
      <div class="form-group mb-3">
        <label for="name">Name</label>
        <input
          type="name"
          class="form-control"
          id="name"
          name="name"
          v-model="state.name"
          placeholder="Enter your name"
        />
        <div v-if="nameErrors.length" class="text-danger mt-1">
          <div v-for="error in nameErrors" :key="error">{{ error }}</div>
        </div>
      </div>

      <div class="form-group mb-3">
        <label for="email">Email address</label>
        <input 
          type="email" 
          class="form-control" 
          id="email" 
          name="email" 
          v-model="state.email" 
          placeholder="Enter your email"
        >
        <div v-if="emailErrors.length" class="text-danger mt-1">
          <div v-for="error in emailErrors" :key="error">{{ error }}</div>
        </div>
      </div>
      <div class="form-group mb-3 position-relative">
        <label for="password">Password</label>
        <input 
          type="password" 
          class="form-control" 
          id="password" 
          name="password" 
          v-model="state.password" 
          placeholder="Enter your password"
        >
        <img src="../assets/auth/eye.png" class="eye-icon">
        <div v-if="passwordErrors.length" class="text-danger mt-1">
          <div v-for="error in passwordErrors" :key="error">{{ error }}</div>
        </div>
      </div>

      <div class="form-group mb-3 position-relative">
        <label for="confirmPassword">Confirm Password</label>
        <input
          type="password"
          class="form-control"
          id="confirmPassword"
          v-model="state.confirmPassword"
          placeholder="Confirm your password"
        />
        <img src="../assets/auth/eye.png" class="eye-icon" alt="Show Password" />
        <div v-if="confirmPasswordErrors.length" class="text-danger mt-1">
          <div v-for="error in confirmPasswordErrors" :key="error">{{ error }}</div>
        </div>
      </div>
      <!-- Hiển thị thông báo lỗi chung từ API -->
      <div v-if="apiError" class="text-danger mt-2">{{ apiError }}</div>

    
      <button type="submit" class="btn btn-success btn-block w-100">Register</button>
    </form>
  </div>
</template>

<script>
import { reactive, computed, ref } from 'vue';
import useVuelidate from '@vuelidate/core';
import { required, email, sameAs, maxLength } from '@vuelidate/validators';
import router from "@/router";
import { useAuthStore } from "@/stores/auth";

export default {
  name: 'RegisterPage',

  setup() {
    const state = reactive({
      name: '',
      email: '',
      password: '',
      confirmPassword: ''
    });

    const authStore = useAuthStore();
    const apiError = ref(''); // Biến lưu trữ lỗi từ API

    const passwordValue = computed(() => state.password);

    const rules = {
      name: { required },
      email: { required, email, maxLength: maxLength(255) },
      password: { required, maxLength: maxLength(255) },
      confirmPassword: { required, sameAsPassword: sameAs(passwordValue) }
    };





    const v$ = useVuelidate(rules, state);

    const handleSubmit = async () => {
      v$.value.$touch();
      await v$.value.$validate();
      if (!v$.value.$invalid) {
        apiError.value = ''; // Reset lỗi trước khi gửi yêu cầu
        const success = await authStore.register(state.name, state.email, state.password, state.confirmPassword);
        if (success) {
          console.log("Đăng ký thành công");
          router.push("/");
        } else {
          apiError.value = "Invalid email or password. Please try again."; // Cập nhật lỗi từ API
        }
      } else {
        console.log("Form failed validation");
        console.log(v$.value);
        console.log("Validation Results: ", v$.value.$params);

      }
    };

    const nameErrors = computed(() => {
      const errors = [];
      if (!v$.value.name.required) errors.push('Name is required.');
      return errors;
    });

    const emailErrors = computed(() => {
      const errors = [];
      if (!v$.value.email.required) errors.push('Email is required.');
      if (!v$.value.email.email) errors.push('Email is not valid.');
      if (state.email.length > 255) errors.push('Email must be less than 255 characters.');
      return errors;
    });

    const passwordErrors = computed(() => {
      const errors = [];
      if (!v$.value.password.required) errors.push('Password is required.');
      if (state.password.length > 255) errors.push('Password must be less than 255 characters.');
      return errors;
    });

    const confirmPasswordErrors = computed(() => {
      const errors = [];
  
      if (!v$.value.confirmPassword.required) errors.push('Confirmation password is required.');
      if (!v$.value.confirmPassword.sameAsPassword) errors.push('Passwords do not match.');
    
      return errors;
    });

    return {
      state,
      v$,
      handleSubmit,
      nameErrors,
      emailErrors,
      passwordErrors,
      confirmPasswordErrors,
      apiError, // Trả về biến để hiển thị trong template
      authStore,
    };
  }
};
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
  
  input[type="email"], input[type="password"], input[type="name"] {
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
  
  .forgot-password {
    align-self: flex-end;
    color: #0C2A92;
    text-decoration: none;
    margin-bottom: 20px;
    text-align: end; 
  }
  
  .forgot-password:hover {
    text-decoration: underline;
  }
  </style>
  