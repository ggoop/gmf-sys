import material from 'gmf/material'
import MdDivider from './MdDivider'

export default Vue => {
  material(Vue)
  Vue.component(MdDivider.name, MdDivider)
}
