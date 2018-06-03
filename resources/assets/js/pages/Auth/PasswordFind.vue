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
  computed: {
    routeQuery() {
      const q = {};
      if (this.$route.query && this.$route.query.continue) q.continue = this.$route.query.continue;
      return q;
    }
  },
  methods: {
    async fetchData() {
      try {
        this.sending = true;
        const thId = this.$route.params.id;
        if (!thId) {
          this.$go({ name: 'auth.login', query: this.routeQuery });
        }
        const response = await this.$http.post('sys/auth/checker', { id: thId });
        const u = response.data.data;
        if (!u) {
          this.$go({ name: 'auth.login', query: this.routeQuery });
        }
        if (u.mobile) {
          this.$go({ name: 'auth.password.find.sms', params: { id: u.id }, query: this.routeQuery });
        } else if (u.email) {
          this.$go({ name: 'auth.password.find.mail', params: { id: u.id }, query: this.routeQuery });
        } else {
          this.$go({ name: 'auth.password.find.word', params: { id: u.id } });
        }
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
