import mdFile from './mdFile.vue';
import mdFileUpload from './mdFileUpload.vue';
import mdFileTheme from './mdFile.theme';

export default function install(Vue) {
  Vue.component('md-file', mdFile);
  Vue.component('md-file-upload', mdFileUpload);
  Vue.material.styles.push(mdFileTheme);

}