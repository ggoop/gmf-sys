import material from 'vue-material/material'
import MdRef from './MdRef.vue';
import MdRefInput from './MdRefInput.vue';
import MdRefBodyEntity from './MdRefBodyEntity.vue';
export default Vue => {
  material(Vue)
  Vue.component(MdRef.name, MdRef)
  Vue.component(MdRefInput.name, MdRefInput)
  Vue.component(MdRefBodyEntity.name, MdRefBodyEntity)
}
