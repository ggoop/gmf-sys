import material from 'gmf/material'
import MdPopup from './MdPopup'

export default Vue => {
  material(Vue)
  Vue.component(MdPopup.name, MdPopup)
}
