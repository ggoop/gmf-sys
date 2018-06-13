
import material from 'gmf/material'

import MdXCellGroup from './MdXCellGroup.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXCellGroup.name, MdXCellGroup)
}