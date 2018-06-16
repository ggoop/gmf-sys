import material from 'gmf/material'

import MdXPopup from './MdXPopup.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXPopup.name, MdXPopup)
}