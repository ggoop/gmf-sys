
import mdQuery from './MdQuery.vue';
import mdQueryField from './MdQueryField.vue';
import mdQueryCase from './MdQueryCase.vue';
import mdQueryCaseWhere from './MdQueryCaseWhere.vue';
import mdQueryCaseOrder from './MdQueryCaseOrder.vue';
import mdQueryCaseField from './MdQueryCaseField.vue';
export default function install(Vue) {
  Vue.component('mdQuery', mdQuery);
  Vue.component('mdQueryField', mdQueryField);
  Vue.component('mdQueryCase', mdQueryCase);
  Vue.component('mdQueryCaseWhere', mdQueryCaseWhere);
  Vue.component('mdQueryCaseOrder', mdQueryCaseOrder);
  Vue.component('mdQueryCaseField', mdQueryCaseField);
}
