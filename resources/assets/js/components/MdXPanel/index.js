import material from 'gmf/material'

import MdXPanel from './MdXPanel.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdXPanel.name, MdXPanel)
}