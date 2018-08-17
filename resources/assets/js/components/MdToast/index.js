import Vue from 'vue';
import MdToast from './MdToast.vue';
let queue = [];

function createInstance() {
  if (!queue.length) {
    const tip = new(Vue.extend(MdToast))({
      el: document.createElement('div')
    });
    document.body.appendChild(tip.$el);
    queue.push(tip);
  }
  return queue[queue.length - 1];
};

function Toast(options = {}) {
  const tip = createInstance();
  tip.toast(options);
  return tip;
};
Toast.install = () => {
  Vue.use(MdToast);
};
Vue.prototype.$toast = Toast;
export default Toast;