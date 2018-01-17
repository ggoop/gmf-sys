<template>
  <div class="md-file md-file-upload layout layout-row layout-align-start-start layout-wrap">
    <div v-for="(file,ind) in files" :key="ind" class="md-files-item layout layout-align-center-center">
      <md-image v-if="isImage(file)&&file.url" :md-src="file.url"></md-image>
      <md-image v-else-if="isImage(file)&&file.data" :md-src="file.data"></md-image>
      <div v-else class="file">
        {{file.title}}
      </div>
      <div class="md-delete" v-if="!disabled">
        <md-button class="md-icon-button md-accent md-raised" @click.native="onItemDelete(ind)">
          <md-icon>delete</md-icon>
        </md-button>
      </div>
    </div>
    <div v-show="showPicker" @click="openPicker" class="md-picker layout layout-column layout-align-center-center flex">
      <md-icon v-if="!disabled">{{ mdIcon }}</md-icon>
      <p v-if="placeholder">{{placeholder}}</p>
    </div>
    <div v-if="loading>0" class="md-file-upload-progress">
      <md-progress-spinner md-mode="indeterminate"></md-progress-spinner>
    </div>
    <input type="file" :id="id" :name="name" :disabled="disabled" :multiple="multiple" :accept="accept" @change="onFileSelected" ref="fileInput">
  </div>
</template>
<script>
import compressImage from 'core/utils/compressImage';
import common from 'core/utils/common';
export default {
  name: 'MdFileUpload',
  props: {
    value: [String, Object, Array],
    mdIcon: {
      type: String,
      default: 'local_see'
    },
    id: String,
    name: String,
    disabled: Boolean,
    required: Boolean,
    placeholder: String,
    accept: {
      type: String,
      default: 'image/*'
    },
    multiple: Boolean,
    maxSize: {
      type: Number,
      //100 * 1024表示100kb
      default: 100 * 1024
    },
    mdUpload: Boolean
  },
  data() {
    return {
      loading: 0,
      files: []
    };
  },
  watch: {
    value(v) {
      if (common.isArray(v)) {
        this.files = v;
      } else {
        this.files = [];
        v && this.files.push(v);
      }
    }
  },
  computed: {
    showPicker() {
      if (this.multiple) return true;
      if (!this.files || this.files.length == 0) return true;
      return false;
    },
  },
  methods: {
    isImage(file) {
      return file && file.type && file.type.indexOf('image/') == 0;
    },
    openPicker() {
      if (!this.disabled && this.loading == 0) {
        this.resetFile();
        this.$refs.fileInput.click();
      }
    },
    onItemDelete(ind) {
      this.files.splice(ind, 1);
      this.setInputValue();
    },
    onFileToData(file) {
      if (!file) return;
      if (this.mdUpload) {
        this.uploadFile(file);
        return;
      }
      if (file.type && file.type.indexOf('image/') == 0) {
        compressImage(file.file, { maxSize: this.maxSize })
          .then((data) => {
            file.data = data.base64;
            this.files.push(file);
            this.setInputValue();
          }, (e) => {
            console.log(e);
          });
      } else {
        const reader = new window.FileReader();
        reader.onload = (e) => {
          file.data = e.target.result;
          this.files.push(file);
          this.setInputValue();
        }
        reader.readAsDataURL(file.file);
      }
    },
    uploadFile(file) {
      const formData = new FormData();
      formData.append('files', file.file, file.title);
      let config = {
        headers: { 'Content-Type': 'multipart/form-data' }
      };
      this.loading++;
      this.$http.post('sys/files', formData, config).then(response => {
        response.data.data.forEach(item => {
          this.files.push(item);
        });
        this.setInputValue();
        this.loading--;
      }).catch(err => {
        this.$toast(err);
        this.loading--;
      });
    },
    resetFile() {
      this.$refs.fileInput.value = '';
    },
    setInputValue() {
      if (this.multiple) {
        this.$emit('input', this.files);
      } else if (this.files.length > 0) {
        this.$emit('input', this.files[0]);
      } else {
        this.$emit('input', null);
      }
    },
    onFileSelected($event) {
      const files = $event.target.files || $event.dataTransfer.files;
      if (files) {
        for (var i = 0; i < files.length; i++) {
          let file = files[i];
          let fileInfo = { file: file, title: file.name, size: file.size, type: file.type };
          fileInfo.ext = file.name.substr(file.name.lastIndexOf(".") + 1);
          this.onFileToData(fileInfo);
        }
      }
    }
  },
  mounted() {
    if (common.isArray(this.value)) {
      this.files = this.value;
    } else {
      this.files = [];
      this.value && this.files.push(this.value);
    }
  },
  beforeDestroy() {

  }
};

</script>
<style lang="scss">
.md-file-upload {
  color: #808080;
  position: relative;
  z-index: 2;
  .md-file-upload-progress {
    position: absolute;
    top: 0px;
    left: 0px;
    right: 0px;
    bottom: 0px;
    z-index: 4;
  }
  .md-picker {
    cursor: pointer;
    padding: 8px;
    min-width: 200px;
    &:hover {
      background: #cce6e4;
    }
  }
  .md-files-item {
    position: relative;
    border: 1px dashed #009688;
    height: 200px;
    margin: 4px;
    padding: 4px;

    img,
    .md-image {
      width: auto;
      height: auto;
      max-width: 100%;
      max-height: 100%;
    }
    div.md-image {
      height: 190px;
    }
    .file {
      width: 120px;
      height: 200px;
      text-align: center;
      word-wrap: break-word;
    }
    .md-delete {
      position: absolute;
      display: none;
      top: 30%;
      left: 30%;
    }
    &:hover {
      .md-delete {
        display: inline-block;
      }
    }
  }
}

</style>
