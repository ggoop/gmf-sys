import material from 'gmf/material'

import MdXNavBar from './MdXNavBar.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXNavBar.name, MdXNavBar)
}