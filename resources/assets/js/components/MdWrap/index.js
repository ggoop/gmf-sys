import material from 'gmf/material'

import MdWrap from './MdWrap.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdWrap.name, MdWrap)
}