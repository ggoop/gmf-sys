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
    fetchData() {

    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>