import material from 'gmf/material'
import MdAvatar from './MdAvatar'

export default Vue => {
  material(Vue)
  Vue.component(MdAvatar.name, MdAvatar)
}
