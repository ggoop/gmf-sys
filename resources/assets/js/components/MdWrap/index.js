import material from 'vue-material/material'

import MdWrap from './MdWrap.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdWrap.name, MdWrap)
}