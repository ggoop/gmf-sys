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
  computed: {
    routeQuery() {
      const q = {};
      if (this.$route.query && this.$route.query.continue) q.continue = this.$route.query.continue;
      return q;
    }
  },
  mounted() {
    const us = authCache.get();
    if (us && us.length > 1) {
      this.$go({ name: 'auth.chooser', query: this.routeQuery });
    } else if (us && us.length == 1) {
      this.$go({ name: 'auth.password', params: { id: us[0].id }, query: this.routeQuery });
    } else {
      this.$go({ name: 'auth.identifier', query: this.routeQuery });
    }
  },
};

</script>
