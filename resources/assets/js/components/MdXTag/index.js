import material from 'gmf/material'

import MdXTag from './MdXTag.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXTag.name, MdXTag)
}