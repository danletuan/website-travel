<template>
    <div class="admin-layout">
      <div class="container-fluid">
        <div class="row w-100 vh-100">
          <div class="d-none d-lg-block col-lg-2 tab">
            <h1 class="text-center align-content-center fw-bold admin">Admin</h1>
            <nav class="nav flex-column">
              <router-link class="nav-link" :class="{ 'active-link': $route.path === '/tours' }" to="/tours">
                <img src="../assets/admin/icon1.png" alt="Tours Icon" class="me-2 icon">
                Tours
              </router-link>
              <router-link class="nav-link" :class="{ 'active-link': $route.path === '/admin/list-news' }" to="/admin/list-news">
                <img src="../assets/admin/icon1.png" alt="News Icon" class="me-2 icon">
                News
              </router-link>
              <router-link class="nav-link" :class="{ 'active-link': $route.path === '/bookings' }" to="/bookings">
                <img src="../assets/admin/icon1.png" alt="Bookings Icon" class="me-2 icon">
                Bookings
              </router-link>
            </nav>
          </div>
  
          <div class="col-lg-10 content">
            <div class="d-flex justify-content-between justify-content-lg-end align-items-center px-3 px-lg-5 account-header">
              <div class="d-flex align-items-center">
                <p class="d-none d-lg-block me-2 name">{{ name }}</p>
                <img :src="avatar" alt="avatar" />
              </div>
            </div>
            <div class="px-3 px-lg-5 py-4 py-lg-5">
              <router-view></router-view>
            </div>
          </div>
        </div>
      </div>
      <div
        class="vh-100 vw-100 d-flex justify-content-center align-items-center dialog"
        v-if="isDialog"
      >
        <ConfirmDialog
          :title="titleDialog"
          :action="actionDialog"
          @updateConfirm="handleConfirm"
        />
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, provide } from 'vue';
  import ConfirmDialog from '@/components/ConfirmDialog.vue';
  
  // Reactive references
  const name = ref('John');
  const avatar = require('../assets/admin/avatar.png');
  const isDialog = ref(false);
  const titleDialog = ref('');
  const actionDialog = ref('');
  

  const confirm = ref(false);
  provide('confirm', confirm);
  const showDialog = (title, action) => {
    isDialog.value = true;
    titleDialog.value = title;
    actionDialog.value = action;
  };

  provide('showDialog', showDialog);

  provide('resetConfirm', () => {
  confirm.value = false;
  });
  


  const handleConfirm = (confirmReceived) => {
    isDialog.value = false;
    confirm.value = confirmReceived;
  };

  </script>
  
  <style scoped>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }
  
  .nav {
    margin-left: 30px;
  }
  
  .nav-link {
    display: flex;
    align-items: center;
    margin-left: 10px;
    color: #a4a6b3;
  }
  
  .nav-link .icon {
    margin-right: 10px;
  }
  
  .nav-link:hover {
    color: #ffffff;
  }
  
  .tab {
    background-color: #363740;
  }
  
  .admin {
    color: white;
  }
  
  .icon {
    object-fit: contain;
  }
  
  .content {
    background-color: #eaecf0;
  }
  
  .account-header {
    border-bottom: #cfcfcf solid;
    background-color: white;
    height: 100px;
  }
  
  .admin-layout,
  .admin {
    height: 100px;
  }
  
  .active-link {
    color: #ffffff;
  }
  
  .admin-layout {
    z-index: 1;
  }
  
  .dialog {
    z-index: 2;
    position: fixed;
    top: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.5);
  }
  </style>
  