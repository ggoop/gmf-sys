import Vue from 'vue';
import 'regenerator-runtime/runtime'
import MdStyle from './style/MdStyle'
import material from './material'
import * as MdComponents from './components/core';
import { sync } from 'vuex-router-sync';
import Start from './Start';

window.Vue = window.Vue || Vue;

let VueMaterial = Vue => {
  material(Vue)
  Object.values(MdComponents).forEach((MdComponent) => {
    Vue.use(MdComponent)
  })
}

VueMaterial.version = '__VERSION__'

const start = new Start;
start.use(VueMaterial);


export default VueMaterial

export { start, VueMaterial }
