import material from 'gmf/material'
import MdBadge from './MdBadge'

export default Vue => {
  material(Vue)
  Vue.component(MdBadge.name, MdBadge)
}