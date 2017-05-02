import orgOrgEdit from './orgOrgEdit.vue';
import orgOrgList from './orgOrgList.vue';

import orgDeptEdit from './orgDeptEdit.vue';
import orgDeptList from './orgDeptList.vue';

import orgWorkEdit from './orgWorkEdit.vue';
import orgWorkList from './orgWorkList.vue';

import orgTeamEdit from './orgTeamEdit.vue';
import orgTeamList from './orgTeamList.vue';


export default function install(Vue) {
	Vue.component('orgOrgEdit', orgOrgEdit);
    Vue.component('orgOrgList', orgOrgList);

    Vue.component('orgDeptEdit', orgDeptEdit);
    Vue.component('orgDeptList', orgDeptList);

    Vue.component('orgWorkEdit', orgWorkEdit);
    Vue.component('orgWorkList', orgWorkList);

    Vue.component('orgTeamEdit', orgTeamEdit);
    Vue.component('orgTeamList', orgTeamList);
}