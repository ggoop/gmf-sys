<template>
  <md-card>
    <form novalidate @submit.prevent="validateUser">
      <md-card-header>
        <md-card-header-text>
          <div class="md-title">登录</div>
          <div class="md-body-1">使用您的帐号登录</div>
        </md-card-header-text>
        <md-button class="md-icon-button md-list-action" :to="{name:'auth.chooser',query:routeQuery}">
          <md-icon class="md-primary">expand_more</md-icon>
        </md-button>
      </md-card-header>
      <md-card-content>
        <md-layout>
          <md-field :class="getValidationClass('account')" md-clearable>
            <label>电子邮件地址或电话号码</label>
            <md-input v-model="mainDatas.account" autocomplete="off" :disabled="sending"></md-input>
            <span class="md-error" v-if="!$v.mainDatas.account.required">请输入电子邮件地址或电话号码</span>
          </md-field>
        </md-layout>
      </md-card-content>
      <md-card-actions>
        <router-link v-if="canRegister" :to="{name:'auth.register',query:routeQuery}">免费注册</router-link>
        <span class="flex"></span>
        <md-button type="submit" class="md-primary md-raised" :disabled="sending">下一步</md-button>
      </md-card-actions>
      <md-divider></md-divider>
      <auth-sns></auth-sns>
      <md-progress-bar md-mode="indeterminate" v-if="sending" />
    </form>
  </md-card>
</template>
<script>
import authCache from './AuthCache';

import AuthSns from './Sns';
import { validationMixin } from 'vuelidate';
import { required, email, minLength, maxLength } from 'vuelidate/lib/validators';
export default {
  name: 'GmfPagesAuthIdentifier',
  components: {
    AuthSns
  },
  mixins: [validationMixin],
  data() {
    return {
      mainDatas: {},
      loading: 0,
      sending: false,
    };
  },
  validations: {
    mainDatas: {
      account: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(30)
      }
    }
  },
  computed: {
    canRegister(){
      if(!this.$root.configs.auth||!this.$root.configs.auth.register)return false;
      return this.$root.configs.auth.register;
    },
    routeQuery() {
      const q = {};
      if (this.$route.query && this.$route.query.continue) q.continue = this.$route.query.continue;
      return q;
    }
  },
  methods: {
    getValidationClass(fieldName) {
      const field = this.$v.mainDatas[fieldName]
      if (field) {
        return {
          'md-invalid': field.$invalid && field.$dirty
        }
      }
    },
    validateUser() {
      this.$v.$touch();
      if (!this.$v.$invalid) {
        this.submitPost()
      }
    },
    submitPost() {
      this.sending = true;
      this.$http.post('sys/auth/checker', this.mainDatas).then(response => {
        this.sending = false;
        const u=response.data.data;
        if(u){
          authCache.add(u);
          this.$go({name:'auth.password',params:{id:u.id},query:this.routeQuery});
        }
      }).catch(err => {
        this.sending = false;
        this.$toast(err);
      });
    },
    fetchData() {

    },
  },
  created() {},
  mounted() {
    this.fetchData();
  },
};
</script>