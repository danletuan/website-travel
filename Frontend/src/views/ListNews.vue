<template>
  <div class="container">
    <h4 class="fw-bold mb-5">News Management</h4>
    <div class="d-flex mb-3 justify-content-between">
      <div class="d-flex">
        <button
          :class="{'filter-active': isFilterVisible}"
          class="me-2 px-3 py-1 d-flex align-items-center filter"
          @click="toggleFilter"
        >
          <img class="me-2" src="../assets/admin/icon2.png" alt="" />Filter
        </button>
        <router-link
          to="/admin/create-news"
          class="px-3 py-1 d-flex align-items-center add-news text-decoration-none"
        >
          <img class="me-2" src="../assets/admin/icon3.png" alt="" />Add News
        </router-link>
        <div v-if="selectedCount > 0" class="selected-count ms-3">
          {{ selectedCount }} row{{ selectedCount > 1 ? 's' : '' }} selected
        </div>
      </div>
      <div class="searching">
        <img class="searching-icon" src="../assets/admin/icon4.png" alt="" />
        <input 
          class="searching-input" 
          type="text" 
          placeholder="Search" 
          v-model="searchQuery"
          @keydown.enter="handleSearch"
        />
      </div>
    </div>
    <div v-if="isFilterVisible" class="filter-options w-100">
      <div>
        <label>Status:</label>
        <input class="ms-2 me-1" type="checkbox" v-model="filterStatus.published" /> Published
        <input class="ms-2 me-1" type="checkbox" v-model="filterStatus.unpublished" /> Unpublished
        <input class="ms-2 me-1" type="checkbox" v-model="filterStatus.draft" /> Draft
      </div>
      <div>
        <label>Created At:</label>
        <input type="date" v-model="filterDate" />
      </div>
      <button @click="applyFilter" class="apply-button ms-auto">Apply</button>
    </div>
    <div v-if="selectedItems.includes(true)" class="d-flex mb-3 align-items-center selected-actions">
      <button @click="deleteSelected" class="delete-button me-5">
        <img src="../assets/admin/delete.png" alt="" /> Delete
      </button>
      <button @click="changeStatusSelected(true)" class="publish-button me-5">
        <img src="../assets/admin/published.png" alt="" /> Published
      </button>
      <button @click="changeStatusSelected(false)" class="unpublish-button">
        <img src="../assets/admin/unpublished.png" alt="" /> Unpublished
      </button>
    </div>
    <ul class="news-list">
      <li class="d-flex justify-content-between align-items-center news-header">
        <input class="news-checkbox" type="checkbox" @click="selectAllItems" v-model="selectAll" />
        <div class="news-image">Image</div>
        <div class="news-title">Title</div>
        <div class="news-date">Created At</div>
        <div class="news-status">Status</div>
        <div class="news-action"></div>
      </li>
      <li v-for="(item, index) in displayedNews" :key="item.id" class="d-flex justify-content-between news-item">
      <input class="news-checkbox" type="checkbox" @click="selectItem(index)" v-model="selectedItems[index]" />
      <img :src="getImageUrl(item.image)" alt="news image" class="news-image" />
      <div class="news-title">{{ item.title }}</div>
      <div class="news-date">{{ item.created_at }}</div>
      <div class="news-status">
        <div
          :class="{
            'status-text text-center mb-1': true,
            'published': item.status === 1,
            'inactive': item.status === 2,
            'draft': item.status === 0
          }"
        >
          {{ item.status === 1 ? "Published" : item.status === 2 ? "Inactive" : "Draft" }}
        </div>
      </div>
      <div class="d-flex justify-content-center news-action">
        <button class="edit-button me-3" @click="editItem(item)">
          <img src="../assets/admin/pen.png" alt="Edit" />
        </button>
        <button class="delete-button" @click="deleteItem(item)">
          <img src="../assets/admin/trash.png" alt="Delete" />
        </button>
      </div>
    </li>
    </ul>
    <div class="d-flex justify-content-between align-items-center news-pages">
      <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
      <p>Page {{ currentPage }} of {{ totalPages }}</p>
      <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
    </div>
  </div>
</template>
  

<script setup>
import { ref, computed, inject, watch, onMounted } from 'vue';
import axios from 'axios';
import router from "@/router";

// Define reactive state
const listNews = ref([]);
const selectAll = ref(false);
const selectedItems = ref([]);
const pageSize = 5;
const currentPage = ref(1);
const isFilterVisible = ref(false);
const searchQuery = ref('');
const filterStatus = ref({
  published: true,
  unpublished: true,
  draft: true,
});
const filterDate = ref('');
const filterApplied = ref(false);

// Fetch news data from the API
const fetchNews = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/posts');
    listNews.value = response.data; // Assuming the data is in `data.data`
    selectedItems.value = Array(listNews.value.length).fill(false);
  } catch (error) {
    console.error("Error fetching news:", error);
  }
};

onMounted(fetchNews);

// Computed properties
const filteredNews = computed(() => {
  if (!Array.isArray(listNews.value)) {
    return [];
  }

  if (!filterApplied.value) {
    return listNews.value;
  }

  return listNews.value.filter((item) => {
    const statusMatch =
      (filterStatus.value.draft && item.status === 0) ||
      (filterStatus.value.published && item.status === 1) ||
      (filterStatus.value.unpublished && item.status === 2);
    const dateMatch = filterDate.value ? item.date === filterDate.value : true;
    const searchMatch = item.title.toLowerCase().includes(searchQuery.value.toLowerCase());
    return statusMatch && dateMatch && searchMatch;
  });
});



const totalPages = computed(() => {
  return Math.ceil(filteredNews.value.length / pageSize);
});

const displayedNews = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredNews.value.slice(start, start + pageSize);
});

const selectedCount = computed(() => {
  return selectedItems.value.filter(Boolean).length;
});

// Methods
const selectAllItems = (event) => {
  const isChecked = event.target.checked;
  selectedItems.value = Array(displayedNews.value.length).fill(isChecked);
};

const selectItem = (index) => {
  selectedItems.value[index] = !selectedItems.value[index];
  checkSelectAll();
};

const checkSelectAll = () => {
  selectAll.value = selectedItems.value.every((item) => item) || selectedItems.value.length > 0;
};

const changeStatusSelected = (newStatus) => {
  const actionText =
    newStatus === 1 ? 'Publish' :
    newStatus === 2 ? 'Unpublish' : 'Set as Draft';

  showDialog(`Are you sure to ${actionText} all selected items?`, actionText);

  watch(confirm, async () => {
    if (confirm.value) {
      listNews.value = listNews.value.map((item, index) => {
        if (selectedItems.value[index]) {
          return { ...item, status: newStatus };
        }
        return item;
      });
      selectedItems.value = Array(listNews.value.length).fill(false);
      selectAll.value = false;
    }
    resetConfirm();
  });
};


const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const toggleFilter = () => {
  isFilterVisible.value = !isFilterVisible.value;
};

const applyFilter = () => {
  filterApplied.value = true;
  currentPage.value = 1;
  isFilterVisible.value = false;
};

const handleSearch = () => {
  filterApplied.value = true;
  currentPage.value = 1;
};

// Delete an item
const itemToDelete = ref(null);
const showDialog = inject("showDialog");
const confirm = inject("confirm");
const resetConfirm = inject("resetConfirm");

// Delete a single news item using API
const deleteItem = (item) => {
  itemToDelete.value = item;
  showDialog("Are you sure to delete this information?", "Delete");

  watch(confirm, async () => {
    if (confirm.value && itemToDelete.value) {
      try {
        await axios.delete(`http://localhost:8000/api/posts/${itemToDelete.value.id}`);
        await fetchNews(); // Refresh the list after deletion
      } catch (error) {
        console.error("Error deleting news:", error);
      }
      itemToDelete.value = null;
    }
    resetConfirm();
  });
};

const editItem = (item) => {
  router.push(`/admin/edit-news/${item.id}`);
};

// Delete selected items using API
const deleteSelected = () => {
  showDialog("Are you sure to delete all items checked?", "Delete");

  watch(confirm, async () => {
    if (confirm.value) {
      try {
        const deletePromises = selectedItems.value
          .map((isSelected, index) => isSelected && axios.delete(`http://localhost:8000/api/posts/${displayedNews.value[index].id}`))
          .filter(Boolean);

        await Promise.all(deletePromises);
        await fetchNews(); // Refresh the list after deletion
        selectedItems.value = Array(listNews.value.length).fill(false);
        selectAll.value = false;
      } catch (error) {
        console.error("Error deleting selected news:", error);
      }
    }
    resetConfirm();
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


<style scoped>
* {
box-sizing: border-box;
margin: 0 ;
padding: 0 ;
}

.filter {
min-width: 107px;
height: 40px;
color: black;
background-color: white;
border: 1px solid #d0d5dd;
border-radius: 8px;
box-shadow: 0px 1px 2px 0px #1018280d;
}

.add-news {
min-width: 143px;
height: 40px;
color: white;
background-color: #000aff;
border: 1px solid #7280ff;
border-radius: 8px;
box-shadow: 0px 1px 2px 0px #1018280d;
}

.searching {
position: relative;
}

.searching-input {
height: 40px;
padding-left: 42px !important;
border: 1px solid #d0d5dd;
border-radius: 8px;
box-shadow: 0px 1px 2px 0px #1018280d;
}

.searching-icon {
position: absolute;
height: 16px;
top: 12px;
left: 16px;
}

.news-list {
min-width: 720px;
box-shadow: 0px 4px 8px -2px #1018280d;
}

.news-header {
padding: 16px 25px 16px 25px ;
border-radius: 8px 8px 0px 0px;
border: 1px solid #eaecf0;
color: #8a92a6;
background-color: #f9fafb;
}

.news-item {
color: #232d42;
padding: 20px 25px 20px 25px ;
background-color: white;
border: 1px solid #eaecf0;
}

.news-checkbox {
width: 20px;
height: 20px;
margin-right: 30px;
}

.news-image {
min-width: 80px;
max-height: 50px;
margin-right: 30px ;
}

.news-title {
width: 480px;
margin-right: 30px;
}

.news-date {
min-width: 95px;
margin-right: 30px;
}

.news-status {
min-width: 95px;
margin-right: 30px;
}

.status-text {
padding: 0px 15px 0px 15px;
border-radius: 12px;
color: white;
width: min-content;
}

.published {
background-color: #4caf50;
}

.inactive {
background-color: #ed4c5c;
}

.draft {
background-color: #667085;
}

.news-action {
min-width: 116px;
}

.news-action button {
background-color: white;
border: none;
}

.news-pages {
color: #344054;
background-color: white;
padding: 12px 24px 16px 24px;
border-radius: 0px 0px 8px 8px;
border: 1px solid #eaecf0;
}

.news-pages button {
padding: 8px 14px 8px 14px;
border-radius: 8px 0px 0px 0px;
border: 1px 0px 0px 0px;
opacity: 0px;

color: #344054;
background-color: white;
border: 1px solid #d0d5dd;
border-radius: 8px;
box-shadow: 0px 1px 2px 0px #1018280d;
}

.selected-options {
display: flex;
justify-content: space-between;
align-items: center;
padding: 12px 24px;
background-color: white;
border: 1px solid #eaecf0;
border-radius: 8px;
margin-top: 20px;
}

.selected-options button {
padding: 8px 16px;
border: 1px solid #d0d5dd;
border-radius: 8px;
background-color: #f9fafb;
cursor: pointer;
}

.selected-options button:hover {
background-color: #e0e0e0;
}

.selected-actions {
background-color: #f9fafb;
padding: 10px;
border-radius: 8px;
}

.delete-button,
.publish-button,
.unpublish-button {
background-color: transparent;
border: none;
cursor: pointer;
}
.delete-button {
color: #ED4C5C; 
}

.publish-button,
.unpublish-button
{
color: #232D42; 
}


.filter-active {
background-color: #ffc107;
}

.searching-input {
border: 1px solid #e0e0e0;
border-radius: 5px;
padding: 0.5rem;
}
.filter-options {
width: 1064px;  
height: 50px;    
gap: 16px;       
border-radius: 8px 0 0 0; 
background-color: #f9f9f9;
padding: 1rem;
border: 1px solid #e0e0e0;
margin-bottom: 1rem;
display: flex;
align-items: center;
}
.filter-options > div {
display: flex;
align-items: center;
margin-right: 1rem ;
}
.filter-options label {
margin-right: 0.5rem ;
}
.filter-options input[type="checkbox"] {
margin-right: 0.5rem ;
}
.filter-options input[type="date"] {
padding: 0.25rem;
margin-left: 1rem ;
}
.apply-button {
background-color: #43B97F;
border: 1px;
padding: 10px 16px 10px 16px;
border-radius: 8px;
cursor: pointer;
}

.selected-count {
font-size: 14px;
font-weight: 400;
line-height: 17.5px;
display: flex;
align-items: center; 
justify-content: center;
}

</style>
