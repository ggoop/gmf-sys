import sysLanguageList from './sysLanguageList.vue';
import sysLanguageEdit from './sysLanguageEdit.vue';

import sysProfileEdit from './sysProfileEdit.vue';
import sysProfileList from './sysProfileList.vue';

import sysPermitEdit from './sysPermitEdit.vue';
import sysPermitList from './sysPermitList.vue';

import sysRoleEdit from './sysRoleEdit.vue';
import sysRoleList from './sysRoleList.vue';

import orgOrgEdit from './orgOrgEdit.vue';
import orgOrgList from './orgOrgList.vue';

import orgDeptEdit from './orgDeptEdit.vue';
import orgDeptList from './orgDeptList.vue';

import orgWorkEdit from './orgWorkEdit.vue';
import orgWorkList from './orgWorkList.vue';

import orgTeamEdit from './orgTeamEdit.vue';
import orgTeamList from './orgTeamList.vue';


export default function install(Vue) {
    Vue.component('sysLanguageList', sysLanguageList);
    Vue.component('sysLanguageEdit', sysLanguageEdit);

    Vue.component('sysProfileEdit', sysProfileEdit);
    Vue.component('sysProfileList', sysProfileList);

    Vue.component('sysPermitEdit', sysPermitEdit);
    Vue.component('sysPermitList', sysPermitList);

    Vue.component('sysRoleEdit', sysRoleEdit);
    Vue.component('sysRoleList', sysRoleList);
	
	Vue.component('orgOrgEdit', orgOrgEdit);
    Vue.component('orgOrgList', orgOrgList);

    Vue.component('orgDeptEdit', orgDeptEdit);
    Vue.component('orgDeptList', orgDeptList);

    Vue.component('orgWorkEdit', orgWorkEdit);
    Vue.component('orgWorkList', orgWorkList);

    Vue.component('orgTeamEdit', orgTeamEdit);
    Vue.component('orgTeamList', orgTeamList);
}