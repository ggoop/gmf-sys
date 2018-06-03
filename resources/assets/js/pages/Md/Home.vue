<template>
  <md-table class="main-table" md-card md-fixed-header v-model="mainDatas">
    <md-table-row slot="md-table-row" slot-scope="{ item }">
      <md-table-cell md-label="类型">
        <a @click="goItem(item)">{{ item.name }}</a>
      </md-table-cell>
      <md-table-cell md-label="名称">
        <a @click="goItem(item)">{{ item.comment }}</a>
      </md-table-cell>
    </md-table-row>
    <md-table-empty-state md-label="没有元数据信息"></md-table-empty-state>
  </md-table>
</template>
<script>
export default {
  data() {
    return {
      mainDatas: []
    };
  },
  methods: {
    goItem(item) {
      this.$go({ name: 'md.show', params: { id: item.id } });
    },
    fetchData() {
      const options = {};
      this.$http.get('sys/entities', { params: options }).then(response => {
        this.mainDatas = response.data.data;
      }).catch(err => {
        this.$toast(err);
      });
    },
  },
  mounted() {
    this.fetchData();
  },
};

</script>
<style lang="scss" scoped>
.main-table {
  height: 100%;
  width: 1000px;
  max-width: 100%;
  margin: 0 auto;
}

</style>
