import material from 'gmf/material'
import MdDrawer from './MdDrawer'

export default Vue => {
  material(Vue)
  Vue.component(MdDrawer.name, MdDrawer)
}
