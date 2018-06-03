import material from 'gmf/material'
import MdApp from './MdApp'
import MdAppToolbar from './MdAppToolbar'
import MdAppContent from './MdAppContent'
import MdAppDrawer from './MdAppDrawer'
import MdAppBackground from './MdAppBackground'
export default Vue => {
  material(Vue)
  Vue.component(MdApp.name, MdApp)
  Vue.component(MdAppToolbar.name, MdAppToolbar)
  Vue.component(MdAppContent.name, MdAppContent)
  Vue.component(MdAppDrawer.name, MdAppDrawer)
  Vue.component(MdAppBackground.name, MdAppBackground)
}
