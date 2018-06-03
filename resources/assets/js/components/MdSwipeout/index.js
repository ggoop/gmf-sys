import material from 'gmf/material'

import MdSwipeout from './MdSwipeout.vue'
import MdSwipeoutItem from './MdSwipeoutItem.vue'
import MdSwipeoutButton from './MdSwipeoutButton.vue'

export {
  MdSwipeout,
  MdSwipeoutItem,
  MdSwipeoutButton
}

export default Vue => {
  material(Vue)
  Vue.component(MdSwipeout.name, MdSwipeout)
  Vue.component(MdSwipeoutItem.name, MdSwipeoutItem)
  Vue.component(MdSwipeoutButton.name, MdSwipeoutButton)
}
