<template>
  <md-progress-bar md-mode="indeterminate" v-if="sending" />
</template>
<script>
import authCache from './AuthCache';
export default {
  name: 'GmfPagesAuthLogin',
  data() {
    return {
      sending: true,
    };
  },
  methods: {
    fetchData() {},
  },
  mounted() {
    const us = authCache.get();
    if (us && us.length > 1) {
      this.$go({ name: 'auth.chooser' });
    } else if (us && us.length == 1) {
      this.$go({ name: 'auth.password', params: { id: us[0].id } });
    } else {
      this.$go({ name: 'auth.identifier' });
    }
  },
};
</script>