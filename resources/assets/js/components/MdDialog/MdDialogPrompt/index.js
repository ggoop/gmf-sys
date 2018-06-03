import material from 'gmf/material'
import MdDialog from '../MdDialog'
import MdDialogPrompt from './MdDialogPrompt'

export default Vue => {
  material(Vue)
  Vue.component(MdDialog.name, MdDialog)
  Vue.component(MdDialogPrompt.name, MdDialogPrompt)
}
