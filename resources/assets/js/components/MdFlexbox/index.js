import MdFlexbox from './MdFlexbox'
import MdFlexboxItem from './MdFlexboxItem'

export {
  MdFlexbox,
  MdFlexboxItem
}

export default function install(Vue) {
  Vue.component(MdFlexbox.name, MdFlexbox);
  Vue.component(MdFlexboxItem.name, MdFlexboxItem);
}
