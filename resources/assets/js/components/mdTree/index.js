
import mdTree from './mdTree.vue';
import mdTreeNode from './MdTreeNode.vue';
import mdTreeView from './MdTreeView.vue';

export default function install(Vue) {
  Vue.component('mdTree', mdTree);
  Vue.component('mdTreeNode', mdTreeNode);
  Vue.component('mdTreeView', mdTreeView);
}
