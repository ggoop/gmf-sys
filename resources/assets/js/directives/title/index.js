export default function install(Vue) {
    Vue.directive('title', {
        inserted: function(el, binding) {
            document.title = binding.value || el.dataset.title;
            Vue.prototype.$documentTitle=document.title;
        }
    });
}
