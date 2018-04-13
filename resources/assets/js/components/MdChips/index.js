import material from 'gmf/material'
import MdChips from './MdChips'
import MdChip from './MdChip'

export default Vue => {
  material(Vue)
  Vue.component(MdChips.name, MdChips)
  Vue.component(MdChip.name, MdChip)
}
