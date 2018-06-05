import material from 'gmf/material'
import MdPullRefresh from './MdPullRefresh'

export default Vue => {
  material(Vue)
  Vue.component(MdPullRefresh.name, MdPullRefresh)
}
