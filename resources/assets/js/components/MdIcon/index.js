import material from 'gmf/material'
import MdIcon from './MdIcon'



export default Vue => {
  material(Vue)
  Vue.component(MdIcon.name, MdIcon);
}
