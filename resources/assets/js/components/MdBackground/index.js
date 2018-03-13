import MdBackground from './MdBackground.vue';
import material from 'vue-material/material'

export default Vue => {
  material(Vue)
  Vue.component(MdBackground.name, MdBackground)
}