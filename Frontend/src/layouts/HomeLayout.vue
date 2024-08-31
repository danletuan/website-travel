<template>
    <div>
      <!-- Header -->
      <header>
        <div class="container">
          <div class="row align-items-center">
            <div class="col-3 text-left">
              <router-link class="navbar-brand" to="/">travelaja</router-link>
            </div>
            <div class="col-7">
              <div class="d-flex justify-content-around">
                <div class="col-auto">
                  <router-link class="nav-link" :class="{ active: isActive('/home') }" to="/home">Home</router-link>
                </div>
                <div class="col-auto">
                  <router-link class="nav-link" :class="{ active: isActive('/discover') }" to="/discover">Discover</router-link>
                </div>
                <div class="col-auto">
                  <router-link class="nav-link" :class="{ active: isActive('/services') }" to="/services">Services</router-link>
                </div>
                <div class="col-auto">
                  <router-link class="nav-link" :class="{ active: isActive('/news-page') }" to="/news-page">News</router-link>
                </div>
                <div class="col-auto">
                  <router-link class="nav-link" :class="{ active: isActive('/about') }" to="/about">About Us</router-link>
                </div>
                <div class="col-auto">
                  <router-link class="nav-link" :class="{ active: isActive('/contact') }" to="/contact">Contact</router-link>
                </div>
              </div>
            </div>
            <div class="col-2 d-flex align-items-center justify-content-end">
              <img src="assets/home/news/VietNam.png" alt="icon" style="width: 24px; height: 24px; margin-right: 8px;">
              <span class="me-2">ID</span>
            </div>
          </div>
        </div>
      </header>
  
      <!-- Main Content -->
      <main>
        <router-view></router-view>
      </main>
  
      <!-- Footer -->
      <footer class="footer">
        <div class="container group pt-5 pb-5">
          <div class="group row">
            <div class="logo col-4">
              <router-link to="/" class="fs-3">travelaja</router-link>
              <p class="mt-4">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit ut aliquam, purus sit amet luctus venenatis, lectus magna fringilla urna, porttitor rhoncus dolor purus non enim praesent elementum facilisis leo, vel
              </p>
            </div>
            <div class="links col-2">
              <h5 class="mb-4 mt-2 fw-bold">Links</h5>
              <ul class="list-unstyled">
                <li><router-link to="/discover">Discover</router-link></li>
                <li><router-link to="/special-deals">Special Deals</router-link></li>
                <li><router-link to="/services">Services</router-link></li>
                <li><router-link to="/community">Community</router-link></li>
                <li><router-link to="/about">About Us</router-link></li>
              </ul>
            </div>
            <div class="services col-3">
              <h5 class="mb-4 mt-2 fw-bold">Services</h5>
              <ul class="list-unstyled">
                <li><router-link to="/about">About Us</router-link></li>
                <li><router-link to="/blog">Blog & Articles</router-link></li>
                <li><router-link to="/terms">Term and Condition</router-link></li>
                <li><router-link to="/privacy">Privacy Policy</router-link></li>
                <li><router-link to="/contact">Contact Us</router-link></li>
              </ul>
            </div>
            <div class="contact col-3">
              <h5 class="mb-4 mt-2 fw-bold">Contact</h5>
              <ul class="list-unstyled">
                <li><p>Address: Jl.Codelaras No.205A</p></li>
                <li><p>Kediri, Pare AG17</p></li>
                <li><p>Phone: 123 456 7890</p></li>
                <li><p>Email: myagungperdana@gmail.com</p></li>
                <li><p>Maps: Kediri, East java</p></li>
              </ul>
            </div>
          </div>
          <div class="social">
            <h4>Ikuti Kami</h4>
            <img class="img" src="assets/home/news/Frame 9.svg" />
          </div>
        </div>
      </footer>
    </div>
  </template>
  
  <script setup>
  import { useRoute } from 'vue-router';
  
  const route = useRoute();
  
  const isActive = (path) => route.path === path;
  </script>
  
<style scoped>
 body {
    font-family: Poppins;
}

.navbar-brand {
    font-size: 33.18px;
    font-weight: 400;
    line-height: 39.82px;
    color: #295943;
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
.footer {
    background-color: #295943;
    color: white;
}

.footer a {
    color: white;
    text-decoration: none;
}

.footer li {
    margin-bottom: 0.5rem;
}

</style>
  