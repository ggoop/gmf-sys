<template>
  <div class="md-file md-file-upload layout layout-row layout-align-start-start layout-wrap">
    <div v-for="(file,ind) in files" :key="ind" class="md-files-item layout layout-align-center-center">
       <md-image v-if="file.image&&file.url" :md-src="file.url"></md-image>
       <md-image v-else-if="file.image&&file.base64" :md-src="file.base64"></md-image>
       <div v-else class="file">
         {{file.name}}
       </div>
       <div class="md-delete">
        <md-button class="md-icon-button md-accent md-raised" @click.native="onItemDelete(ind)">
          <md-icon>delete</md-icon>
        </md-button>
       </div>
    </div>   
    <div v-show="showPicker" @click="openPicker" class="md-picker layout layout-column layout-align-center-center flex">
      <md-icon>local_see</md-icon>
      <p v-if="placeholder">{{placeholder}}</p>
    </div>
    <input
      type="file"
      :id="id"
      :name="name"
      :disabled="disabled"
      :multiple="multiple"
      :accept="accept"
      @change="onFileSelected"
      ref="fileInput">
  </div>
</template>
<script>
  import compressImage from '../../core/utils/compressImage';
  import common from '../../core/utils/common';
  export default {
    name: 'md-file-upload',
    props: {
      value: [String,Object,Array],
      id: String,
      name: String,
      disabled: Boolean,
      required: Boolean,
      placeholder: String,
      accept: {
        type:String,
        default:'image/*'
      },
      multiple: Boolean,
      maxSize: {
        type: Number,
        //100 * 1024表示100kb
        default: 100 * 1024
      },
    },
    data() {
      return {
        files:[]
      };
    },
    watch: {
      value(v) {
        if (common.isArray(v)) {
          this.files=v;
        } else {
          this.files =[];
          v&&this.files.push(v);
        }
      }
    },
    computed: {
      showPicker() {
        if (this.multiple)return true;
        if (!this.files||this.files.length==0) return true;
        return false;
      },
    },
    methods: {
      openPicker() {
        if (!this.disabled) {
          this.resetFile();
          this.$refs.fileInput.click();
        }
      },
      onItemDelete(ind){
        this.files.splice(ind,1);
        this.setInputValue();
      },
      onFileToData(file){
        if(!file)return;
        if(file.type&&file.type.indexOf('image/')==0){
          compressImage(file.file,{maxSize:this.maxSize})
          .then((data)=>{
            file.base64=data.base64;
            file.image=true;
            this.files.push(file);
            this.setInputValue();
          },(e)=>{
            console.log(e);
          });
        }else{
          const reader = new window.FileReader();
          reader.onload = (e)=> {
            file.base64= e.target.result;
            this.files.push(file);
            this.setInputValue();
          }
          reader.readAsDataURL(file.file);
        }
      },
      resetFile() {
        this.$refs.fileInput.value = '';
      },
      setInputValue(){
        if(this.multiple){
          this.$emit('input',this.files);
        }else if(this.files.length>0){
          this.$emit('input',this.files[0]);
        }else{
          this.$emit('input',null);
        }
      },
      onFileSelected($event) {
        const files = $event.target.files || $event.dataTransfer.files;
        if (files) {
          for (var i = 0; i < files.length; i++) {
            let file=files[i];
            let fileInfo={file:file,name:file.name,size:file.size,type:file.type};            
            fileInfo.ext=file.name.substr(file.name.lastIndexOf(".")+1); 
            this.onFileToData(fileInfo);
          }          
        }
      }
    },
    mounted() {
      
    },
    beforeDestroy() {
      
    }
  };
</script>