// import mdGrid from './mdGrid.vue';
// import mdGridColumn from './mdGridColumn.vue';
// import mdPagination from './mdPagination.vue';

export default function install(Vue) {
  // Vue.component('md-grid', mdGrid);
  // Vue.component('md-grid-column', mdGridColumn);
  // Vue.component('md-pagination', mdPagination);
  Vue.component('MdGrid', () => import('./mdGrid.vue'));
  Vue.component('MdGridColumn', () => import('./mdGridColumn.vue'));
  Vue.component('MdPagination', () => import('./mdPagination.vue'));
}