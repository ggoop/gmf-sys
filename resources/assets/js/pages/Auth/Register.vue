<template>
  <md-card>
    <form novalidate @submit.prevent="validateForm">
      <md-card-header>
        <md-card-header-text>
          <div class="md-title">创建新的{{appName}}帐号</div>
        </md-card-header-text>
      </md-card-header>
      <md-card-content>
        <md-layout>
          <md-field :class="getValidationClass('name')" md-clearable>
            <label>输入您的真实姓名</label>
            <md-input v-model="mainDatas.name" autocomplete="off" :disabled="sending"></md-input>
            <span class="md-error" v-if="!$v.mainDatas.name.required">姓名</span>
          </md-field>
        </md-layout>
        <md-layout>
          <md-field :class="getValidationClass('account')" md-clearable>
            <label>账号</label>
            <md-input v-model="mainDatas.account" autocomplete="off" :disabled="sending"></md-input>
            <span class="md-helper-text">输入电子邮件地址或者手机号。</span>
            <span class="md-error" v-if="!$v.mainDatas.account.required">请输入输入电子邮件地址或者手机号。!</span>
          </md-field>
        </md-layout>
        <md-layout>
          <md-field :class="getValidationClass('password')">
            <label>密码</label>
            <md-input v-model="mainDatas.password" autocomplete="off" type="password" :disabled="sending"></md-input>
            <span class="md-error" v-if="!$v.mainDatas.password.required">请输入密码</span>
            <span class="md-helper-text">请至少使用 6 个字符。请勿使用您用于登录其他网站的密码或容易被猜到的密码</span>
          </md-field>
        </md-layout>
      </md-card-content>
      <md-card-actions>
        <md-button type="submit" class="md-primary md-raised" :disabled="sending">注册</md-button>
      </md-card-actions>
      <md-card-actions class="login-memo">
        <router-link :to="{name:'auth.login',query:routeQuery}">我有帐号，直接登录</router-link>
      </md-card-actions>
      <md-progress-bar md-mode="indeterminate" v-if="sending" />
    </form>
  </md-card>
</template>
<script>
import { validationMixin } from 'vuelidate';
import { required, email, minLength, maxLength } from 'vuelidate/lib/validators';
export default {
  name: 'GmfPagesAuthRegister',
  props: {},
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
      name: {
        required
      },
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
      if (this.$route.query && this.$route.query.continue) q.continue = this.$route.query.continue;
      return q;
    },
    appName(){
      return this.$root.appName ? " " + this.$root.appName + " " : ""; 
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
    validateForm() {
      this.$v.$touch();
      if (!this.$v.$invalid) {

        var m = /^1[358][0123456789]\d{8}$/;
        var e = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
        if (!m.test(this.mainDatas.account) && !e.test(this.mainDatas.account)) {
          alert('账号需要是电子邮件或者手机号!');
          return false;
        }
        this.submitPost()
      }
    },
    submitPost() {
      this.sending = true;
      this.$http.post('sys/auth/register', this.mainDatas).then(response => {
        this.sending = false;
        this.$go(this.$route.query.continue ? this.$route.query.continue : { name: 'app.users.show', params: { id: '0' } });
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
