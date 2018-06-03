import material from 'gmf/material'
import MdAutocomplete from './MdAutocomplete'

export default Vue => {
  material(Vue)
  Vue.component(MdAutocomplete.name, MdAutocomplete)
}