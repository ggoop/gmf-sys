import material from 'gmf/material'
import MdProgressBar from './MdProgressBar/MdProgressBar'
import MdProgressSpinner from './MdProgressSpinner/MdProgressSpinner'

export default Vue => {
  material(Vue)
  Vue.component(MdProgressBar.name, MdProgressBar)
  Vue.component(MdProgressSpinner.name, MdProgressSpinner)
}
