import material from 'gmf/material'
import MdSteppers from './MdSteppers'
import MdStep from './MdStep'

export default Vue => {
  material(Vue)
  Vue.component(MdSteppers.name, MdSteppers)
  Vue.component(MdStep.name, MdStep)
}
