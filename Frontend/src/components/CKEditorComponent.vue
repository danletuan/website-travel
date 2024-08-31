<template>
    <div id="app">
      <ckeditor
        :editor="editor"
        v-model="editorData"
        :config="editorConfig"
      ></ckeditor>
    </div>
  </template>
  
  <script setup>
  import {
    ClassicEditor,
    Bold,
    Essentials,
    Italic,
    Paragraph,
    Undo,
  } from "ckeditor5";
  import CKEditor from "@ckeditor/ckeditor5-vue";
  import "ckeditor5/ckeditor5.css";
  import { ref, defineProps, defineEmits, watch } from "vue";
  
  const props = defineProps({
    content: String,
  });
  
  const ckeditor = CKEditor.component;
  const editor = ClassicEditor;
  const editorData = ref(props.content);
  const editorConfig = {
    plugins: [Bold, Essentials, Italic, Paragraph, Undo],
    toolbar: ["undo", "redo", "|", "bold", "italic"],
    placeholder: "Starting typing here...",
  };
  
  const emit = defineEmits(["updateContent"]);
  watch(editorData, () => {
    emit("updateContent", editorData.value);
  });
  </script>
  