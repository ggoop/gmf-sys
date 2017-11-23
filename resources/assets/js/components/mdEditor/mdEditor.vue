<template>
  <div class="md-editor-wrapper layout layout-column layout-fill">
    <div ref="quillContainer" :id="inputId"></div>
    <input v-if="useCustomImageHandler" @change="emitImageInfo($event)" ref="fileInput" id="file-upload" type="file" style="display:none;">
  </div>
</template>
<script>
import Quill from 'quill'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import uniqueId from 'core/utils/uniqueId';
var defaultToolbar = [
  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
  [{ 'size': ['small', false, 'large', 'huge'] }],
  ['bold', 'italic', 'underline', 'strike'],
  [{ 'color': [] }, { 'background': [] }],
  [{ 'script': 'sub' }, { 'script': 'super' }, 'blockquote', 'code-block'],
  [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }, { 'align': [] }],
  ['link', 'image', 'video', 'formula'],
  ['clean']
];

export default {
  name: 'MdEditor',
  props: {
    value: String,
    mdInputId: String,
    placeholder: String,
    disabled: Boolean,
    editorToolbar: Array,
    useCustomImageHandler: {
      type: Boolean,
      default: false
    },
    height:Number
  },

  data() {
    return {
      inputId: this.mdInputId || 'quill-' + uniqueId(),
      quill: null,
      editor: null,
      toolbar: this.editorToolbar ? this.editorToolbar : defaultToolbar,
    }
  },

  mounted() {
    this.initializeEditor()
    this.handleUpdatedEditor()
  },

  watch: {
    value(val) {
      if (val != this.editor.innerHTML && !this.quill.hasFocus()) {
        this.editor.innerHTML = val
      }
    },
    disabled(status) {
      this.quill.enable(!status);
    }
  },

  methods: {
    initializeEditor() {
      this.setQuillElement()
      this.setEditorElement()
      this.checkForInitialContent()
    },

    setQuillElement() {
      this.quill = new Quill(this.$refs.quillContainer, {
        modules: {
          toolbar: this.toolbar
        },
        placeholder: this.placeholder ? this.placeholder : '',
        theme: 'snow',
        readOnly: this.disabled ? this.disabled : false,
      })
      this.checkForCustomImageHandler()
    },

    setEditorElement() {
      this.editor = document.querySelector(`#${this.inputId} .ql-editor`);
      if(this.height&&this.height>10){
        this.editor.style.minHeight=this.height+'px';
      }
    },

    checkForInitialContent() {
      this.editor.innerHTML = this.value || ''
    },

    checkForCustomImageHandler() {
      this.useCustomImageHandler === true ? this.setupCustomImageHandler() : ''
    },

    setupCustomImageHandler() {
      let toolbar = this.quill.getModule('toolbar');
      toolbar.addHandler('image', this.customImageHandler);
    },

    handleUpdatedEditor() {
      this.quill.on('text-change', () => {
        this.$emit('input', this.editor.innerHTML)
      })
    },

    customImageHandler(image, callback) {
      this.$refs.fileInput.click();
    },

    emitImageInfo($event) {
      let file = $event.target.files[0]
      let Editor = this.quill
      let range = Editor.getSelection();
      let cursorLocation = range.index
      this.$emit('imageAdded', file, Editor, cursorLocation)
    }
  }
}
</script>
<style lang="scss">
  @import "~components/MdAnimation/variables";
 .md-editor-wrapper {
    margin:0px;
  }
  
</style>
