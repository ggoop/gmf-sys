import material from 'gmf/material'
import MdScrollLoad from './MdScrollLoad'

export default Vue => {
  material(Vue)
  Vue.component(MdScrollLoad.name, MdScrollLoad)
}
