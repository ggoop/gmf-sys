import material from 'gmf/material'
import MdSwitch from './MdSwitch'

export default Vue => {
  material(Vue)
  Vue.component(MdSwitch.name, MdSwitch)
}
