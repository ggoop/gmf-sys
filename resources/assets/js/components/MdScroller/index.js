import material from 'gmf/material'
import MdScroller from './MdScroller'

export default Vue => {
  material(Vue)
  Vue.component(MdScroller.name, MdScroller)
}
