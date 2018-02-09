import mdBoards from './mdBoards.vue';
import mdBoard from './mdBoard.vue';

export default function install(Vue) {
  Vue.component('md-boards', mdBoards);
  Vue.component('md-board', mdBoard);
}
