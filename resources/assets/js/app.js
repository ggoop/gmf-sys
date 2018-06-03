import Vue from 'vue';

import Start from './Start';
if (!Vue.gmfStart) {
  Vue.gmfStart = new Start()
}
const st = Vue.gmfStart;
export default st;
