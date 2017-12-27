<template>
  <md-progress-bar md-mode="indeterminate" v-if="sending" />
</template>
<script>
export default {
  name: 'GmfPagesAuthPasswordFind',
  data() {
    return {
      mainDatas: {},
      sending: false,
    };
  },
  methods: {
    async fetchData() {
      try {
        this.sending = true;
        const thId = this.$route.params.id;
        if (!thId) {
          this.$go({ name: 'auth.login' });
        }
        const response = await this.$http.post('sys/auth/checker', { id: thId });
        const u = response.data.data;
        if (!u) {
          this.$go({ name: 'auth.login' });
        }
        if (u.mobile) {
          this.$go({ name: 'auth.password.find.sms', params: { id: u.id } });
        } else if (u.email) {
          this.$go({ name: 'auth.password.find.mail', params: { id: u.id } });
        } else {
          this.$go({ name: 'auth.password.find.word', params: { id: u.id } });
        }
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