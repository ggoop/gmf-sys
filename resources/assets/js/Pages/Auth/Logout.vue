<template>
  <md-progress-bar md-mode="indeterminate" v-if="sending" />
</template>
<script>
export default {
  name: 'GmfPagesAuthLogout',
  data() {
    return {
      sending: true,
    };
  },
  methods: {
    async fetchData() {
      try {
        const response = await this.$http.post('sys/auth/logout');
        await this.$root.$loadConfigs();
        this.$go({name:'auth.login'});
      } catch (err) {
         this.$toast(err);
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