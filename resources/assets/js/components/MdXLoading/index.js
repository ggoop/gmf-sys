import material from 'gmf/material'

import MdXLoading from './MdXLoading.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXLoading.name, MdXLoading)
}