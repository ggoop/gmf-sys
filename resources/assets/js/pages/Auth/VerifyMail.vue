<template>
  <md-card>
    <md-card-header>
      <md-card-header-text>
        <div class="md-title">帐号认证</div>
        <div class="md-body-1">电子邮件账号认证</div>
      </md-card-header-text>
    </md-card-header>
    <md-list>
      <md-list-item>
        <md-avatar>
          <md-image :md-src="mainDatas.avatar"></md-image>
        </md-avatar>
        <div class="md-list-item-text">
          <span>{{ mainDatas.name }}</span>
          <span>{{ mainDatas.email }}</span>
        </div>
        <md-button class="md-icon-button md-list-action" :to="{name:'auth.chooser',query:routeQuery}">
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
        <md-button class="md-primary" @click="onOtherClick">不想认证了</md-button>
        <span class="flex"></span>
        <md-button type="submit" class="md-primary md-raised" :disabled="disabledConfirmBtn">认证</md-button>
      </md-card-actions>
    </form>
    <md-progress-bar md-mode="indeterminate" v-if="sending" />
  </md-card>
</template>
<script>
import { validationMixin } from 'vuelidate';
import { required, email, minLength, maxLength } from 'vuelidate/lib/validators';
export default {
  name: 'GmfPagesAuthVerifyMail',
  mixins: [validationMixin],
  data() {
    return {
      mainDatas: {},
      loading: 0,
      isSended: false,
      sending: false,
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
    routeQuery() {
      const q = {};
      if (this.$route.query && this.$route.query.continue) q.continue = this.$route.query.continue;
      return q;
    },
    disabledSendBtn() {
      return this.sending || this.isSended || !!this.mainDatas.token;
    },
    disabledConfirmBtn() {
      return this.sending || !this.mainDatas.token;
    },
    tipLabel() {
      return '验证码将发送到 ' + this.mainDatas.email;
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
    onOtherClick() {
      this.$go(this.$route.query.continue ? this.$route.query.continue : this.$root.configs.home);
    },
    onSendCode() {
      this.sending = true;
      const options = { id: this.mainDatas.id, account: this.mainDatas.account, type: 'verify-mail', mode: 'mail' };
      this.$http.post('sys/auth/vcode-create', options).then(response => {
        this.isSended = true;
        this.sending = false;
        this.$toast('验证码已发送到您的邮件上，请及时查收!');
      }).catch(err => {
        this.sending = false;
        this.$toast(err);
      });
    },
    validateForm() {
      this.$v.$touch();
      if (!this.$v.$invalid) {
        this.submitPost()
      }
    },
    submitPost() {
      this.sending = true;
      const options = { id: this.mainDatas.id, account: this.mainDatas.account, token: this.mainDatas.token };
      this.$http.post('sys/auth/verify-mail', options).then(response => {
        this.sending = false;
        this.$go(this.$route.query.continue ? this.$route.query.continue : this.$root.configs.home);
      }).catch(err => {
        this.sending = false;
        this.$toast(err);
      });
    },
    async fetchData() {
      try {
        this.sending = true;
        const response = await this.$http.post('sys/auth/checker', { id: this.$root.configs.user.id });
        this.mainDatas = response.data.data;
      } catch (err) {
        this.$toast(err);
        this.$go(this.$route.query.continue ? this.$route.query.continue : this.$root.configs.home);
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
