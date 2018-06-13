import material from 'gmf/material'

import MdXSwipe from './MdXSwipe.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXSwipe.name, MdXSwipe)
}