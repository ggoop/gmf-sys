import material from 'vue-material/material'
import MdRef from './MdRef.vue';
import MdRefInput from './MdRefInput.vue';

export default Vue => {
  material(Vue)
  Vue.component(MdRef.name, MdRef)
  Vue.component(MdRefInput.name, MdRefInput)
}