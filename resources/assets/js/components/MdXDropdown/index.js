import material from 'gmf/material'

import MdXDropdown from './MdXDropdown.vue';
import MdXDropdowns from './MdXDropdowns.vue';
export default Vue => {
  material(Vue)
  Vue.component(MdXDropdown.name, MdXDropdown)
  Vue.component(MdXDropdowns.name, MdXDropdowns)
}