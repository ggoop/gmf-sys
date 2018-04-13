import material from 'gmf/material'
import MdHighlightText from './MdHighlightText'

export default Vue => {
  material(Vue)
  Vue.component(MdHighlightText.name, MdHighlightText)
}
