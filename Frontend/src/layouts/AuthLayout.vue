<template>
    <div class="account">
      <div class="container-fluid">
        <div class="row w-100 vh-100">
          <div
            class="col-md-7 col-lg-6 d-flex align-items-center justify-content-center login-form"
          >
          <router-view></router-view>
          </div>
          <div class="d-none d-md-block col-md-5 col-lg-6 image-container">
            <img
              src="../assets/auth/image1.png"
              alt="Leaf Image"
            />
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <style scoped>
  .image-container img {
    border-radius: 45px 0 0 45px;
    width: 50vw;
    height: 100vh;
    object-fit: cover;
  }
  </style>
  