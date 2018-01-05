<template>
  <md-table md-card md-fixed-header>
  </md-table>
</template>
<script>
export default {
  data() {
    return {
      mainDatas: [],
      sending: false,
    };
  },
  computed: {

  },
  methods: {
    goItem(item) {
      this.sending = true;
      this.$http.post('sys/auth/checker', item).then(response => {
        this.sending = false;
        const u=response.data.data;
        if(u){
          this.$go({name:'auth.password',params:{id:u.id}});
        }
      }).catch(err => {
        this.sending = false;
        this.$toast(err);
      });
    },
    fetchData() {
      this.mainDatas = authCache.get() || [];
    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>