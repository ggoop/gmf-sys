import material from 'gmf/material'
import MdButton from './MdButton'

export default Vue => {
  material(Vue)
  Vue.component(MdButton.name, MdButton)
}
