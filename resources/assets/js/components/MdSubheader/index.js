import material from 'gmf/material'
import MdSubheader from './MdSubheader'

export default Vue => {
  material(Vue)
  Vue.component(MdSubheader.name, MdSubheader)
}
