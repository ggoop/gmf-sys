<template>
  <div class="md-file md-file-upload layout layout-row layout-align-start-start layout-wrap">
    <div v-for="(file,ind) in files" :key="ind" class="md-files-item layout layout-align-center-center">
       <md-image v-if="isImage(file)&&file.url" :md-src="file.url"></md-image>
       <md-image v-else-if="isImage(file)&&file.data" :md-src="file.data"></md-image>
       <div v-else class="file">
         {{file.title}}
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
  import compressImage from 'core/utils/compressImage';
  import common from 'core/utils/common';
  export default {
    name: 'MdFileUpload',
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
      mdUpload:Boolean
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
      isImage(file){
        return file&&file.type&&file.type.indexOf('image/')==0;
      },
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
            file.data=data.base64;

            if(this.mdUpload){
              this.uploadFile(file);
            }else{
              this.files.push(file);
              this.setInputValue();
            }
            
          },(e)=>{
            console.log(e);
          });
        }else{
          const reader = new window.FileReader();
          reader.onload = (e)=> {
            file.data= e.target.result;
            if(this.mdUpload){
              this.uploadFile(file);
            }else{
              this.files.push(file);
              this.setInputValue();
            }
          }
          reader.readAsDataURL(file.file);
        }
      },
      uploadFile(file){      
        const options={};
        options.files=[file];
        this.$http.post('sys/files', options).then(response=>{
          response.data.data.forEach(item=>{
            this.files.push(item);
          });          
          this.setInputValue();
        }).catch(err=>{
          this.$toast(err);
        });      
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
            let fileInfo={file:file,title:file.name,size:file.size,type:file.type};            
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

<style lang="scss">
  .md-file-upload{
  background: #f7f8f9;
  color: #808080;
  .md-picker{
    cursor: pointer;
    padding: 8px;
    min-width: 200px;
    &:hover{
      background: #cce6e4;
    }
  }
  .md-files-item{
    position: relative;
    border: 1px dashed #009688;
    height: 200px;
    margin: 4px;
    padding:4px;

    img,.md-image{
      width: auto;  
      height: auto;  
      max-width: 100%;  
      max-height:100%; 
    }
    div.md-image{
      height:190px;
    }
    .file{
      width:120px;
      height: 200px;
      text-align: center;
      word-wrap: break-word;
    }
    .md-delete{
      position:absolute;
      display: none;
      top: 30%;
      left: 30%;
    }
    &:hover{
      .md-delete{
        display: inline-block;
      }
    }
  }
}

</style>