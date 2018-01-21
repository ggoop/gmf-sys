<template>
  <div class="md-file md-file-import layout layout-row layout-align-start-start layout-wrap">
    <md-menu md-size="auto">
      <div class="md-button md-theme-default" md-menu-trigger>
        <slot>
          <md-button>导入</md-button>
        </slot>
      </div>
      <md-menu-content>
        <md-list class="md-file-import-list">
          <md-list-item @click="openPicker">
            <md-icon>add_to_photos</md-icon>
            <span class="md-list-item-text">导入数据</span>
          </md-list-item>
          <md-list-item v-if="template" @click="onDownTemplateFile">
            <md-icon>cloud_download</md-icon>
            <span class="md-list-item-text">下载模板</span>
          </md-list-item>
        </md-list>
      </md-menu-content>
    </md-menu>
    <input type="file" :id="id" :disabled="disabled" :multiple="multiple" :accept="accept" @change="onFileSelected" ref="fileInput" />
  </div>
</template>
<script>
import common from 'gmf/core/utils/common';
export default {
  name: 'MdFileImport',
  props: {
    id: String,
    disabled: Boolean,
    accept: {
      type: String,
      default: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    },
    multiple: Boolean,
    maxSize: {
      type: Number,
      //100 * 1024表示100kb
      default: 100 * 1024
    },
    template: String,
    mdAction: {
      type: String,
      default: 'sys/datas/import'
    },
    mdEntity: String
  },
  data() {
    return {
      files: []
    };
  },
  watch: {

  },
  computed: {},
  methods: {
    openPicker() {
      if (!this.disabled) {
        this.resetFile();
        this.$refs.fileInput.click();
      }
    },
    onDownTemplateFile() {
      window.open(this.template);
    },
    resetFile() {
      this.$refs.fileInput.value = '';
    },
    onFileSelected($event) {
      const files = $event.target.files || $event.dataTransfer.files;
      if (files) {
        for (var i = 0; i < files.length; i++) {
          let file = files[i];
          let fileInfo = { file: file, title: file.name, size: file.size, type: file.type };

          fileInfo.ext = fileInfo.title.substr(fileInfo.title.lastIndexOf(".") + 1);
          this.uploadFile(fileInfo);
        }
      }
    },
    uploadFile(file) {
      const formData = new FormData();
      formData.append('files', file.file, file.title);
      formData.append('entity', this.mdEntity);
      formData.append('name', 'files');
      let config = {
        headers: { 'Content-Type': 'multipart/form-data' }
      };
      this.$http.post(this.mdAction, formData, config).then(response => {
        this.$emit('import',response.data.data);
        this.$toast('导入成功!');
      }).catch(err => {
        this.$toast(err);
      });
    },
  },
  mounted() {},
  beforeDestroy() {}
};
</script>
<style lang="scss">
.md-file-import-list.md-list {
  padding: 0;
}
</style>