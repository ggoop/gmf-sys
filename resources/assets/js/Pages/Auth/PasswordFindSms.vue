<template>
  <md-card>
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
        <div class="md-list-item-text">
          <span>{{ mainDatas.name }}</span>
          <span>{{ mainDatas.mobile }}</span>
        </div>
        <md-button class="md-icon-button md-list-action" :to="{name:'auth.chooser'}">
          <md-icon class="md-primary">expand_more</md-icon>
        </md-button>
      </md-list-item>
    </md-list>
    <md-card-content>
      <p>{{ tipLabel }}</p>
    </md-card-content>
    <md-card-actions>
      <md-button class="md-primary md-raised" @click="onSendCode" :disabled="disabledSendBtn">发送验证码</md-button>
    </md-card-actions>
    <form novalidate @submit.prevent="validateForm">
      <md-card-content>
        <md-layout>
          <md-field :class="getValidationClass('token')">
            <label>验证码</label>
            <md-input v-model="mainDatas.token" autocomplete="off" :disabled="sending"></md-input>
            <span class="md-error" v-if="!$v.mainDatas.token.required">请输入验证码</span>
            <span class="md-error" v-if="!$v.mainDatas.token.minLength||!$v.mainDatas.token.maxLength">验证码格式不符合要求</span>
          </md-field>
        </md-layout>
      </md-card-content>
      <md-card-actions>
        <md-button class="md-primary" @click="onOtherClick">我没有使用手机</md-button>
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
  name: 'GmfPagesAuthPasswordFindSms',
  props: {},
  mixins: [validationMixin],
  data() {
    return {
      mainDatas: {},
      loading: 0,
      sending: false,
      isSended: false,
    };
  },
  validations: {
    mainDatas: {
      token: {
        required,
        minLength: minLength(6),
        maxLength: maxLength(6)
      }
    }
  },
  computed: {
    disabledSendBtn() {
      return this.sending || this.isSended || !!this.mainDatas.token;
    },
    disabledConfirmBtn() {
      return this.sending || !this.isSended || !this.mainDatas.token;
    },
    tipLabel() {
      return this.$root.appName+' 会将验证码发送到 ' + this.mainDatas.mobile;
    }
  },
  methods: {
    onOtherClick() {
      this.$go({ name: 'auth.password.find.mail', params: { id: this.mainDatas.id } });
    },
    onSendCode() {
      this.sending = true;
      this.$http.post('sys/auth/password-send-sms', this.mainDatas).then(response => {
        this.isSended = true;
        this.sending = false;
        this.$toast('验证码已发送到您的手机上，请及时查收!');
      }).catch(err => {
        this.sending = false;
        this.$toast(err);
      });
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
      this.$http.post('sys/auth/vcode-check', this.mainDatas).then(response => {
        this.sending = false;
        this.$go({name:'auth.reset',params:{id:this.mainDatas.id,token:this.mainDatas.token}});
      }).catch(err => {
        this.sending = false;
        this.$toast(err);
      });
    },
    async fetchData() {
      try {
        this.sending = true;
        const thId = this.$route.params.id;
        if (!thId) {
          this.$go({ name: 'auth.login' });
        }
        const response = await this.$http.post('sys/auth/checker', { id: thId });
        this.mainDatas = response.data.data;
      } catch (err) {
        this.$toast(err);
        this.$go({ name: 'auth.identifier' });
      } finally {
        this.sending = false;
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