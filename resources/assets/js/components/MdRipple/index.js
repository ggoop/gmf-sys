import material from 'gmf/material'
import MdRipple from './MdRipple'

export default Vue => {
  material(Vue)
  Vue.component(MdRipple.name, MdRipple)
}
