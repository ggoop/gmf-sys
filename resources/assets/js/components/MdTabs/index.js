import material from 'gmf/material'
import MdTabs from './MdTabs'
import MdTab from './MdTab'

export default Vue => {
  material(Vue)
  Vue.component(MdTabs.name, MdTabs)
  Vue.component(MdTab.name, MdTab)
}
