<template>
  <md-card>
    <form novalidate @submit.prevent="validateForm">
      <md-card-header>
        <md-card-header-text>
          <div class="md-title">重置密码</div>
        </md-card-header-text>
      </md-card-header>
      <md-list>
        <md-list-item>
          <md-avatar>
            <md-image :md-src="mainDatas.avatar"></md-image>
          </md-avatar>
          <div class="md-list-item-text">
            <span>{{ mainDatas.name }}</span>
            <span>{{ mainDatas.account }}</span>
          </div>
          <md-button class="md-icon-button md-list-action" :to="{name:'auth.chooser'}">
            <md-icon class="md-primary">expand_more</md-icon>
          </md-button>
        </md-list-item>
      </md-list>
      <md-card-content>
        <md-layout>
          <md-field :class="getValidationClass('password')">
            <label>输入您的新密码</label>
            <md-input v-model="mainDatas.password" autocomplete="off" type="password" :disabled="sending"></md-input>
            <span class="md-error" v-if="!$v.mainDatas.password.required">请输入密码</span>
          </md-field>
        </md-layout>
        <md-layout>
          <md-field :class="getValidationClass('password_confirmation')">
            <label>确认密码</label>
            <md-input v-model="mainDatas.password_confirmation" autocomplete="off" type="password" :disabled="sending"></md-input>
            <span class="md-error" v-if="!$v.mainDatas.password_confirmation.sameAsPassword">确认密码不符合要求</span>
          </md-field>
        </md-layout>
      </md-card-content>
      <md-card-actions>
        <span class="flex"></span>
        <md-button type="submit" class="md-primary md-raised" :disabled="sending">设置</md-button>
      </md-card-actions>
      <md-progress-bar md-mode="indeterminate" v-if="sending" />
    </form>
  </md-card>
</template>
<script>
import { validationMixin } from 'vuelidate';
import { required, sameAs, minLength, maxLength } from 'vuelidate/lib/validators';
export default {
  name: 'GmfPagesAuthPassword',
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
      password: {
        required,
        minLength: minLength(6),
        maxLength: maxLength(30)
      },
      password_confirmation:{
        sameAsPassword:sameAs('password')
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
    async submitPost() {
      try {
        this.sending = true;
        this.mainDatas.token=this.$route.params.token;
        const response = await this.$http.post('sys/auth/reset', this.mainDatas);
        this.sending = false;
        this.$setConfigs({ user: response.data.data, token: response.data.token });
        await this.$root.$loadConfigs();
        this.$go(this.$root.configs.home);
      } catch (err) {
        this.sending = false;
        this.$toast(err);
      }
    },
    async fetchData() {
      try {
        this.sending = true;
        const thId = this.$route.params.id;
        if (!thId) {
          this.$go({ name: 'auth.chooser' });
        }
        const response = await this.$http.post('sys/auth/checker', { id: thId });
        const u = response.data.data;
        this.mainDatas = response.data.data;

        this.mainDatas.token=this.$route.params.token;
        await this.$http.post('sys/auth/vcode', this.mainDatas);
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