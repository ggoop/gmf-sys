<template>
  <md-card md-theme="blue">
    <md-card-header>
      <md-card-header-text>
        <div class="md-title">帐号帮助</div>
        <div class="md-body-1">获取验证码</div>
      </md-card-header-text>
    </md-card-header>
    <md-list>
      <md-list-item>
        <md-avatar>
          <md-image :md-src="mainDatas.avatar"></md-image>
        </md-avatar>
        <div class="md-list-item-text">{{ mainDatas.name }}</div>
        <md-button class="md-icon-button md-list-action" :to="{name:'auth.chooser'}">
          <md-icon class="md-primary">expand_more</md-icon>
        </md-button>
      </md-list-item>
    </md-list>
    <form novalidate @submit.prevent="validateForm">
      <md-card-content>
        <p>请输入您可以立即查收邮件的电子邮件地址</p>
        <md-field :class="getValidationClass('email')">
          <label>电子邮件地址</label>
          <md-input v-model="mainDatas.email" autocomplete="off" :disabled="sending"></md-input>
          <span class="md-error" v-if="!$v.mainDatas.email.required">请输入电子邮件地址</span>
        </md-field>
      </md-card-content>
      <md-card-actions>
        <span class="flex"></span>
        <md-button type="submit" class="md-primary md-raised" :disabled="disabledConfirmBtn">下一步</md-button>
      </md-card-actions>
    </form>
    <md-progress-bar md-mode="indeterminate" v-if="sending" />
  </md-card>
</template>
<script>
import { validationMixin } from 'vuelidate';
import { required, email, minLength, maxLength } from 'vuelidate/lib/validators';
export default {
  name: 'GmfPagesAuthPasswordFindWord',
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
      email: {
        required,
        email
      }
    }
  },
  computed: {
    disabledSendBtn(){
      return this.sending||!!this.mainDatas.vcode;
    },
    disabledConfirmBtn(){
      return this.sending||!this.mainDatas.vcode;
    },
    tipLabel(){
      return this.$root.appName+' 会将验证码发送到 ••• •••• ••';
    }
  },
  methods: {
    onSendCode(){
      this.$toast('验证码已发送到您的邮件上，请及时查收!');
    },
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
      this.$http.post('sys/auth/login', this.mainDatas).then(response => {
        this.sending = false;
        this.$go('/');
      }).catch(err => {
        this.sending = false;
        this.$toast(err);
      });
    },
    async fetchData() {
      try {
        this.sending=true;
        const thId = this.$route.params.id;
        if (!thId) {
          this.$go({ name: 'auth.login' });
        }
        const response = await this.$http.post('sys/auth/checker', { id: thId });
        const u = response.data.data;
        this.mainDatas = response.data.data;
      } catch (err) {
        this.$toast(err);
        this.$go({ name: 'auth.identifier' });
      }finally{
        this.sending=false;
      }
    },
  },
  async mounted() {
    await this.fetchData();
  },
};
</script>
<style lang="scss" scoped>
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
.md-card-actions {
  justify-content: center;
}
</style>