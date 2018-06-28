import material from 'gmf/material'

import MdXSubmitBar from './MdXSubmitBar.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXSubmitBar.name, MdXSubmitBar)
}