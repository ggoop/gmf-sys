import material from 'gmf/material'
import MdSticky from './MdSticky'

export default Vue => {
  material(Vue)
  Vue.component(MdSticky.name, MdSticky)
}
