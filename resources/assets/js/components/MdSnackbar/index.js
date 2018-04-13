import material from 'gmf/material'
import MdSnackbar from './MdSnackbar'

export default Vue => {
  material(Vue)
  Vue.component(MdSnackbar.name, MdSnackbar)
}
