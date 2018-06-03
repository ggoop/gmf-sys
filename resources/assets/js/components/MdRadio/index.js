import material from 'gmf/material'
import MdRadio from './MdRadio'

export default Vue => {
  material(Vue)
  Vue.component(MdRadio.name, MdRadio)
}
