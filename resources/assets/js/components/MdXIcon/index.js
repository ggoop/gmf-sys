import material from 'gmf/material'

import MdXIcon from './MdXIcon.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXIcon.name, MdXIcon)
}