import MdBackground from './MdBackground.vue';
import material from 'gmf/material'

export default Vue => {
  material(Vue)
  Vue.component(MdBackground.name, MdBackground)
}