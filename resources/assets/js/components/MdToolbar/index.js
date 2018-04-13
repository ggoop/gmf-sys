import material from 'gmf/material'
import MdToolbar from './MdToolbar'

export default Vue => {
  material(Vue)
  Vue.component(MdToolbar.name, MdToolbar)
}
