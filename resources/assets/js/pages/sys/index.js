import sysLanguageList from './sysLanguageList.vue';
import sysLanguageEdit from './sysLanguageEdit.vue';

import sysProfileEdit from './sysProfileEdit.vue';
import sysProfileList from './sysProfileList.vue';

import sysPermitEdit from './sysPermitEdit.vue';
import sysPermitList from './sysPermitList.vue';

import sysRoleEdit from './sysRoleEdit.vue';
import sysRoleList from './sysRoleList.vue';


export default function install(Vue) {
    Vue.component('sysLanguageList', sysLanguageList);
    Vue.component('sysLanguageEdit', sysLanguageEdit);

    Vue.component('sysProfileEdit', sysProfileEdit);
    Vue.component('sysProfileList', sysProfileList);

    Vue.component('sysPermitEdit', sysPermitEdit);
    Vue.component('sysPermitList', sysPermitList);

    Vue.component('sysRoleEdit', sysRoleEdit);
    Vue.component('sysRoleList', sysRoleList);
	
}