<template>
  <div class="md-editor">
    <textarea :id="id">{{ content }}</textarea>
  </div>
</template>
<script>
import MdUuid from 'core/utils/MdUuid';
import setting from './setting';

import LoadScript from 'gmf/core/utils/LoadScript';
export default {
  name: 'MdEditor',
  props: {
    id: {
      type: String,
      default: () => 'md-editor-input-' + MdUuid()
    },
    value: { default: '' },
    mdMode: {
      type: String,
      //simple,full
      default: 'simple'
    },
    placeholder: String,
    mdOptions: Object
  },
  data() {
    return {
      content: '',
      editor: null,
      cTinyMce: null,
      checkerTimeout: null,
      isTyping: false
    };
  },
  created() {
    this.content = this.value;
    LoadScript('/assets/vendor/gmf-sys/tinymce/tinymce.js').then((s) => {
      this.initEdit();
    }).catch(e => {
      this.$toast('tinymce加载失败!');
    });
  },
  mounted() {
    
  },
  beforeDestroy() {
    this.editor && this.editor.destroy();
    this.editor && this.editor.remove();
  },
  watch: {
    value: function(newValue) {
      if (!this.isTyping) {
        if (this.editor !== null)
          this.editor.setContent(newValue);
        else
          this.content = newValue;
      }
    }
  },
  methods: {
    initEdit() {
      let options = {
        selector: '#' + this.id,
        init_instance_callback: (editor) => {
          this.editor = editor;
          if (!editor) {
            this.$toast('编辑器初始化失败');
          }
          editor.on('KeyUp', (e) => {
            this.submitNewContent();
          });
          editor.on('input change undo redo execCommand', (e) => {
            if (this.editor.getContent() !== this.value) {
              this.submitNewContent();
            }
          });
          editor.on('init', (e) => {
            editor.setContent(this.content);
            this.$emit('input', this.content);
          });
          editor.iframeElement.contentDocument &&
            editor.iframeElement.contentDocument.firstElementChild &&
            editor.iframeElement.contentDocument.firstElementChild.classList.add("md-theme-default");
        },
        images_upload_handler: (blobInfo, success, failure) => {
          this.images_upload_handler(blobInfo, success, failure);
        }
      };
      tinymce && tinymce.init(this._.assignIn(setting[this.mdMode], options, this.mdOptions));
    },
    submitNewContent() {
      this.isTyping = true;
      if (this.checkerTimeout !== null)
        clearTimeout(this.checkerTimeout);
      this.checkerTimeout = setTimeout(() => {
        this.isTyping = false;
      }, 300);
      this.$emit('input', this.editor.getContent());
    },
    images_upload_handler(blobInfo, success, failure) {
      const formData = new FormData();
      formData.append('files', blobInfo.blob(), blobInfo.filename());
      let config = {
        headers: { 'Content-Type': 'multipart/form-data' }
      };
      this.$http.post('sys/files', formData, config).then(response => {
        if (response.data.data) {
          response.data.data.forEach(item => {
            success(item.url);
          });
        } else {
          success();
        }

      }).catch(err => {
        this.$toast(err);
        failure('update image Error  ! ');
      });
    },
  }
}

</script>
<style lang="scss">
.md-editor {
  min-width: 100%;
  max-width: 100%;
}

</style>
