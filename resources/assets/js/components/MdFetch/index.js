import material from 'vue-material/material'

//news
import MdFetch from './MdFetch'


export default Vue => {
  material(Vue)
  Vue.component(MdFetch.name, MdFetch)
}
