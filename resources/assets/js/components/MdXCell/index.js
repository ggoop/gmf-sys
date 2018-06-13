import material from 'gmf/material'

import MdXCell from './MdXCell.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXCell.name, MdXCell)
}