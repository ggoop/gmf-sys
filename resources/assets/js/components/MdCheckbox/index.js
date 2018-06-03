import material from 'gmf/material'
import MdCheckbox from './MdCheckbox'

export default Vue => {
  material(Vue)
  Vue.component(MdCheckbox.name, MdCheckbox)
}
