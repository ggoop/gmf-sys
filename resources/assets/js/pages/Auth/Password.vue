<template>
  <md-card>
    <form novalidate @submit.prevent="validateForm">
      <md-card-header>
        <md-card-header-text>
          <div class="md-title">欢迎</div>
          <p v-if="appName">使用您的{{appName}}帐号</p>
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
          <md-button class="md-icon-button md-list-action" :to="{name:'auth.chooser',query:routeQuery}">
            <md-icon class="md-primary">expand_more</md-icon>
          </md-button>
        </md-list-item>
      </md-list>
      <md-card-content>
        <md-layout>
          <md-field :class="getValidationClass('password')">
            <label>输入您的密码</label>
            <md-input v-model="mainDatas.password" autocomplete="off" type="password" :disabled="sending"></md-input>
            <span class="md-error" v-if="!$v.mainDatas.password.required">请输入密码</span>
          </md-field>
        </md-layout>
      </md-card-content>
      <md-card-actions>
        <router-link :to="{name:'auth.password.find',params:{id:mainDatas.id},query:routeQuery}">忘记了密码</router-link>
        <span class="flex"></span>
        <md-button type="submit" class="md-primary md-raised" :disabled="sending">登 录</md-button>
      </md-card-actions>
      <md-divider></md-divider>
      <auth-sns></auth-sns>
      <md-progress-bar md-mode="indeterminate" v-if="sending" />
    </form>
  </md-card>
</template>
<script>
import AuthSns from './Sns';
import { validationMixin } from 'vuelidate';
import { required, email, minLength, maxLength } from 'vuelidate/lib/validators';
export default {
  name: 'GmfPagesAuthPassword',
  components: {
    AuthSns
  },
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
        this.submitPost()
      }
    },
    async submitPost() {
      try {
        this.sending = true;
        const response = await this.$http.post('sys/auth/login', this.mainDatas);
        this.sending = false;
        this.$setConfigs({ user: response.data.data, token: response.data.token });
        await this.$root.$loadConfigs();
        this.$go(this.$route.query.continue ? this.$route.query.continue : this.$root.configs.home);
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
          this.$go({ name: 'auth.chooser', query: this.routeQuery });
        }
        const response = await this.$http.post('sys/auth/checker', { id: thId });
        const u = response.data.data;
        this.mainDatas = response.data.data;
      } catch (err) {
        this.$toast(err);
        this.$go({ name: 'auth.identifier', query: this.routeQuery });
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
