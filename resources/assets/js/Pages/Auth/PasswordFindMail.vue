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
        <div class="md-list-item-text">{{ mainDatas.name }}</div>
        <md-button class="md-icon-button md-list-action" :to="{name:'auth.chooser'}">
          <md-icon class="md-primary">expand_more</md-icon>
        </md-button>
      </md-list-item>
    </md-list>
    <md-card-content>
      <p>{{ tipLabel }}</p>
    </md-card-content>
    <md-card-actions>
      <md-button class="md-primary" @click="onOtherClick">试试其他方式</md-button>
      <span class="flex"></span>
      <md-button class="md-primary md-raised" @click="onSendCode" :disabled="disabledSendBtn">发送验证码</md-button>
    </md-card-actions>
    <md-progress-bar md-mode="indeterminate" v-if="sending" />
  </md-card>
</template>
<script>
import common from 'gmf/core/utils/common';
export default {
  name: 'GmfPagesAuthPasswordFindMail',
  data() {
    return {
      mainDatas: {},
      loading: 0,
      isSended:false,
      sending: false,
    };
  },
  computed: {
    disabledSendBtn(){
      return this.sending||this.isSended||!!this.mainDatas.vcode;
    },
    disabledConfirmBtn(){
      return this.sending||!this.mainDatas.vcode;
    },
    tipLabel(){
      return 'U9HUB 会将验证码发送到 '+common.regEmail(this.mainDatas.email);
    }
  },
  methods: {
    onOtherClick(){
      this.$go({ name: 'auth.password.find.word',params:{id:this.mainDatas.id} });
    },
    onSendCode(){
      this.sending = true;
      this.$http.post('sys/auth/password-send-mail', this.mainDatas).then(response => {
        this.isSended=true;
        this.sending = false;
        this.$toast('验证码已发送到您的邮件上，请及时查收!');
        this.$go({ name: 'auth.login' });
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
        this.mainDatas =response.data.data;
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