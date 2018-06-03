import material from 'gmf/material'
import MdImage from './MdImage'

export default Vue => {
  material(Vue)
  Vue.component(MdImage.name, MdImage)
}
