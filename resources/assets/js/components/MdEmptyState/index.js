import material from 'gmf/material'
import MdEmptyState from './MdEmptyState'

export default Vue => {
  material(Vue)
  Vue.component(MdEmptyState.name, MdEmptyState)
}