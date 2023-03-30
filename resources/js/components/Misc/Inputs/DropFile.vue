<template>
      <div
        id="dropzone"
        class="dropzone-container"
        @dragover="dragover"
        @dragleave="dragleave"
        @drop="drop"
      >
      <label class="dropzone-label" for="dropzone">{{label}}</label>
        <input
          type="file"
          multiple
          :name="name"
          :id="name"
          :batch="batch"
          class="hidden-input"
          @change="onChange(name)"
          ref="file"
          :accept="[fileType == 'image' ? '.jpg,.jpeg,.png' : '.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv']"
        />
  
        <label :for="name" class="file-label">
          <div v-if="isDragging">Release to drop files here.</div>
          <div v-else>Drop files here or <u>click here</u> to upload.</div>
        </label>
  
        <div class="preview-container mt-4 row g-1" >
          <div v-for="file in files" :key="file.name" class="preview-card col-md-4">
            <div class="card-holder">
              <img class="preview-img" :src="[checkFileType(file.name) == 'pdf' ? '/images/pdf.jpg' : checkFileType(file.name) == 'txt' ? '/images/txt.jpg'
               : checkFileType(file.name) == 'xlsx' || checkFileType(file.name) == 'xls' || checkFileType(file.name) == 'csv' ? '/images/excel.jpg' : checkFileType(file.name) == 'docx' || checkFileType(file.name) == 'doc' ? '/images/word.jpg' : generateThumbnail(file)]" />
              <p :title="file.name">
                {{ makeName(file.name) }}
                {{ Math.round(file.size / 1000) + "kb" }}
              </p>
            </div>
              <button class="remove-file" type="button" @click="remove(files.indexOf(file))" title="Remove file">
              <i class="bi bi-x-lg"></i>
              </button>
          </div>
        </div>
      </div>
  </template>
  
  <script>
  import { errorMessage, successMessage } from '@/utilities.js'
  export default {
    props:{
        label:{
            type: String,
            required: true,
            default: 'Label Name'
        },
        name:{
            type: String,
            default: 'file'
        },
        ref:{
            type: String,
            default: 'file'
        },
        fileType:{
            type: String,
            default: 'file'
        },
        batch:{
            type: String,
            default: '' 
        }
    },
    data() {
      return {
        isDragging: false,
        files: [],
      };
    },
    methods: {
      onChange() {
        
        for (const item of this.$refs.file.files) {
           if(this.$props.fileType == 'image'){
             if(this.checkFileType(item.name) == 'jpg' || this.checkFileType(item.name) == 'png' || this.checkFileType(item.name) == 'jpeg'){
                 if(item.size / 1000 > 3000){
                    errorMessage('Opps!', item.name +' can not be added due to 3mb maximum file size.', 'top-right')
                 }
                 this.uploadToServer(item)
                //this.files.push(item);
             }else{
                errorMessage('Opps!', item.name +' can not be added due to file type restriction.', 'top-right')
             }
           }else{
            if(this.checkFileType(item.name) == 'xlsx' || this.checkFileType(item.name) == 'csv' || this.checkFileType(item.name) == 'xls'
             || this.checkFileType(item.name) == 'docx' || this.checkFileType(item.name) == 'txt' || this.checkFileType(item.name) == 'pdf'  || this.checkFileType(item.name) == 'doc'){
                if(item.size / 1000 > 3000){
                    errorMessage('Opps!', item.name +' can not be added due to 3mb maximum file size.', 'top-right')
                 }else{
                  this.uploadToServer(item)
                 // this.files.push(item);
                 }
                 
             }else{
                errorMessage('Opps!', item.name +' can not be added due to file type restriction.', 'top-right')
             }
          }
        }
        
        // this.files = [...this.$refs.file.files];
      },
      uploadToServer(file){
        this.files.push(file)
        const formData = new FormData();
        formData.append("selectedFiles", file);
        formData.append("batch_number", this.$props.batch);
        axios({
            method: "POST",
            url: "/api/file-upload",
            data: formData,
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
      },
      generateThumbnail(file) {
        let fileSrc = URL.createObjectURL(file);
        setTimeout(() => {
          URL.revokeObjectURL(fileSrc);
        }, 1000);
        return fileSrc;
      },
      checkFileType(name){
        var fileExt = name.split(".")[name.split(".").length - 1]; 
        return fileExt;
      },
      makeName(name) {
        return (
          name.split(".")[0].substring(0, 5) +
          "..." +
          name.split(".")[name.split(".").length - 1]
        );
      },
      remove(i) {
        this.files.splice(i, 1);
      },
      dragover(e) {
        e.preventDefault();
        this.isDragging = true;
      },
      dragleave() {
        this.isDragging = false;
      },
      drop(e) {
        e.preventDefault();
        this.$refs.file.files = e.dataTransfer.files;
        if(this.$refs.file.files.length > 3){
          errorMessage('Opps!','3 files maximum upload only!', 'top-right')
        }else{
          this.onChange();
          this.isDragging = false;
        }
      },
    },
  };
  </script>
  
  <style>
  .dropzone-container {
    padding: 25px 10px;
    text-align: center;
    background: #f7fafc;
    border: 2px dashed #7F7F7FAA;
    border-radius: 15px;
    position: relative;
  }
  .dropzone-label{
    position: absolute;
    top: -11px;
    left: 7px;
    padding: 0px 10px;
    background-color: #fff;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 400;
  }
  .hidden-input {
    opacity: 0;
    overflow: hidden;
    position: absolute;
    width: 1px;
    height: 1px;
  }
  .file-label {
    font-size: 16px;
    display: block;
    cursor: pointer;
  }
  .preview-container {
    margin-top: 2rem;
  }
  .preview-card {
    position:relative;
  }
  .card-holder{
    border: 1px solid #a2a2a2;
    padding: 10px;
    border-radius: 10px;
  }
  .card-holder p{
    margin-bottom: 0;
    font-size: 11px;
  }
  .remove-file{
    position: absolute;
    top: 6px;
    right: 10px;
    border: none;
    background-color: #fff;
    width: 28px;
    font-size: 14px;
  }
  .preview-img {
    width: 170px;
    height: 130px;
    border-radius: 5px;
    border: 1px solid #a2a2a2;
    background-color: #a2a2a2;
  }
  </style>