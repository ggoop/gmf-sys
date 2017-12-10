import Vue from 'vue';
import 'regenerator-runtime/runtime'
import MdCss from 'core/MdCss'
import material from './material'
import * as MdComponents from './components'
import lodash from 'lodash';
import ga from 'vue-ga'
import { sync } from 'vuex-router-sync';
import Start from './Start';


window._ = window._ || lodash;

window.Vue = window.Vue || Vue;

let VueMaterial = Vue => {
  material(Vue)
  Object.values(MdComponents).forEach((MdComponent) => {
    Vue.use(MdComponent)
  })
}

VueMaterial.version = '__VERSION__'

const start =new Start;
start.use(VueMaterial);


export default VueMaterial

export {start,VueMaterial}
