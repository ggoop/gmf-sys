<template>
  <md-card>
    <form novalidate @submit.prevent="validateUser">
      <md-card-header>
        <md-card-header-text>
          <div class="md-title">账号关联</div>
          <div class="md-body-1">添加更多账号</div>
        </md-card-header-text>
        <md-button class="md-icon-button md-list-action" :to="{name:'auth.account.join',query:routeQuery}">
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
          <md-field :class="getValidationClass('password')">
            <label>输入您的密码</label>
            <md-input v-model="mainDatas.password" autocomplete="off" type="password" :disabled="sending"></md-input>
            <span class="md-error" v-if="!$v.mainDatas.password.required">请输入密码</span>
          </md-field>
        </md-layout>
      </md-card-content>
      <md-card-actions>
        <span class="flex"></span>
        <md-button type="submit" class="md-primary md-raised" :disabled="sending">绑定</md-button>
      </md-card-actions>
      <md-divider></md-divider>
      <auth-sns title="绑定合作账号" type="join"></auth-sns>
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
  name: 'GmfPagesAuthAccountJoin',
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
      },
      password: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(30)
      }
    }
  },
  computed: {
    routeQuery() {
      const q = {};
      if (this.$route.query && this.$route.query.continue) p.continue = this.$route.query.continue;
      return q;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.fetchData();
    });
  },
  beforeRouteUpdate(to, from, next) {
    this.fetchData();
    next();
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
      this.$http.post('sys/auth/join', this.mainDatas).then(response => {
        this.sending = false;
      }).catch(err => {
        this.sending = false;
        this.$toast(err);
      });
    },
    fetchData() {

    },
  },
};

</script>
