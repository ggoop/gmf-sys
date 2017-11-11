import mdFile from './mdFile.vue';
import mdFileUpload from './mdFileUpload.vue';
import mdFileImport from './mdFileImport.vue';
import mdFileTheme from './mdFile.theme';

export default function install(Vue) {
  Vue.component('md-file', mdFile);
  Vue.component('md-file-upload', mdFileUpload);
  Vue.component('md-file-import', mdFileImport);
  Vue.material.styles.push(mdFileTheme);

}