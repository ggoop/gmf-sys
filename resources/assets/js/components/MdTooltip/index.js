import material from 'gmf/material'
import MdTooltip from './MdTooltip'

export default Vue => {
  material(Vue)
  Vue.component(MdTooltip.name, MdTooltip)
}
