import material from './material'
import * as MdComponents from './components/min'

export function install(Vue, options = {}) {
  material(Vue)
  Object.values(MdComponents).forEach((MdComponent) => {
    Vue.use(MdComponent)
  });
}
const plugin = {
  version: '__VERSION__',
  install,
}
export default plugin;

let GlobalVue = null
if (typeof window !== 'undefined') {
  GlobalVue = window.Vue
} else if (typeof global !== 'undefined') {
  GlobalVue = global.Vue
}
if (GlobalVue) {
  GlobalVue.use(plugin);
}