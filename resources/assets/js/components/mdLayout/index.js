import material from 'vue-material/material'
import MdLayout from './MdLayout'

export default Vue => {
  material(Vue)
  Vue.component(MdLayout.name, MdLayout)
}
