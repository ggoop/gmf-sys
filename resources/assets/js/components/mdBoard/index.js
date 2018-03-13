import mdBoards from './MdBoards.vue';
import mdBoard from './MdBoard.vue';

export default function install(Vue) {
  Vue.component('md-boards', mdBoards);
  Vue.component('md-board', mdBoard);
}
