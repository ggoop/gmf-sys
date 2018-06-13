import material from 'gmf/material'

import MdXField from './MdXField.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXField.name, MdXField)
}