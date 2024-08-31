<template>
  <div class="p-5 d-flex flex-column align-items-center dialog-window">
    <p class="mb-4">{{ title }}</p>
    <div class="d-flex w-100 justify-content-around">
      <button class="px-3 py-1 unconfirm-button" @click="emitConfirm(false)">
        Cancel
      </button>
      <button class="px-3 py-1 confirm-button" @click="emitConfirm(true)">
        {{ action }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from "vue";

const props = defineProps({
  title: String,
  action: String,
});

const title = ref(props.title);
const action = ref(props.action);

const emit = defineEmits(["updateConfirm"]);
const emitConfirm = (confirm) => {
  emit("updateConfirm", confirm);
};
</script>

<style>
.dialog-window {
  background-color: white;
  border-radius: 8px;
  min-width: fit-content;
}

.unconfirm-button {
  color: white;
  background-color: #667085;
  border: 1px solid #667085;
  border-radius: 8px;
}

.confirm-button {
  color: white;
  background-color: #ed4c5c;
  border: 1px solid #ed4c5c;
  border-radius: 8px;
}
</style>
