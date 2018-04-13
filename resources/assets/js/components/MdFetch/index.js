import material from 'gmf/material'

//news
import MdFetch from './MdFetch'


export default Vue => {
  material(Vue)
  Vue.component(MdFetch.name, MdFetch)
}
