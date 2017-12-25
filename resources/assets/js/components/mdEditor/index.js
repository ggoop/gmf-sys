// import MdEditor from './MdEditor.vue';

export default function install(Vue) {
  Vue.component('MdEditor', () => import('./MdEditor.vue'));
  Vue.component('MdEditorTinymce', () => import('./MdEditorTinymce.vue'));
  // Vue.component(MdEditor.name, MdEditor);
}
