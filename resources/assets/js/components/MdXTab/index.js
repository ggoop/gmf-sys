import material from 'gmf/material'

import MdXTab from './MdXTab.vue';
import MdXTabs from './MdXTabs.vue';
export default Vue => {
  material(Vue)
  Vue.component(MdXTab.name, MdXTab)
  Vue.component(MdXTabs.name, MdXTabs)
}