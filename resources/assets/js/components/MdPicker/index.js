import material from 'gmf/material'
import MdPicker from './MdPicker'

export default Vue => {
  material(Vue)
  Vue.component(MdPicker.name, MdPicker)
}
