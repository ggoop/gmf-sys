
import mdQuery from './mdQuery.vue';

import mdQueryCase from './mdQueryCase.vue';
import mdQueryCaseWhere from './mdQueryCaseWhere.vue';
import mdQueryCaseOrder from './mdQueryCaseOrder.vue';
import mdQueryCaseField from './mdQueryCaseField.vue';
export default function install(Vue) {
  Vue.component('mdQuery', mdQuery);
  Vue.component('mdQueryCase', mdQueryCase);
  Vue.component('mdQueryCaseWhere', mdQueryCaseWhere);
  Vue.component('mdQueryCaseOrder', mdQueryCaseOrder);
  Vue.component('mdQueryCaseField', mdQueryCaseField);
}
