import material from 'gmf/material'

import MdSearch from './MdSearch.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdSearch.name, MdSearch)
}