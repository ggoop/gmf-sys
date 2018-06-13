
import material from 'gmf/material'

import MdXCellSwipe from './MdXCellSwipe.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXCellSwipe.name, MdXCellSwipe)
}