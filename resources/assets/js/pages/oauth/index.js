import oauthClientEdit from './oauthClientEdit.vue';
import oauthClientList from './oauthClientList.vue';

export default function install(Vue) {
    Vue.component('oauthClientEdit', oauthClientEdit);
    Vue.component('oauthClientList', oauthClientList);
}
