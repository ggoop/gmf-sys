<template>
  <md-card>
    <form novalidate @submit.prevent="validateForm">
      <md-card-header>
        <md-card-header-text>
          <div class="md-title">创建新帐号</div>
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
            <label>电子邮件地址</label>
            <md-input v-model="mainDatas.account" autocomplete="off" :disabled="sending"></md-input>
            <span class="md-helper-text">我们会向此地址发送一封电子邮件，验证您是否佣有该邮件地址。</span>
            <span class="md-error" v-if="!$v.mainDatas.account.required">请输入电子邮件地址!</span>
            <span class="md-error" v-else-if="!$v.mainDatas.account.email">请输入有效的电子邮件地址!</span>
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
        <router-link :to="{name:'auth.login'}">我有帐号，直接登录</router-link>
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
        email,
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
        this.submitPost()
      }
    },
    submitPost() {
      this.sending = true;
      this.$http.post('sys/auth/register', this.mainDatas).then(response => {
        this.sending = false;
        //continue
        //this.$go({name:'app.users.show',params:{id:'0'}});
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