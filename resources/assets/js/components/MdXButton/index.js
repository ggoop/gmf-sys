import material from 'gmf/material'

import MdXButton from './MdXButton.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXButton.name, MdXButton)
}