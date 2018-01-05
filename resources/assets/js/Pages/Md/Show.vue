<template>
  <md-table class="main-table" md-card md-fixed-header v-model="mainDatas.fields">
    <md-table-toolbar class="md-dense">
      <div class="md-toolbar-row class-title">
        <md-chip class="md-primary hide-xs">{{mainDatas.type_name}}</md-chip>
        <span class="md-chip">{{mainDatas.comment}}</span>
        <span class="md-chip" v-if="mainDatas.table_name">表名: {{mainDatas.table_name}}</span>
      </div>
    </md-table-toolbar>
    <md-table-row slot="md-table-row" slot-scope="{ item }">
      <md-table-cell md-label="名称">
        <span>{{ item.name }}</span>
        <span v-if="mainDatas.type=='entity'&&item.field_name&&item.name!=item.field_name">
          ({{item.field_name}})
        </span>
        <span v-if="item.collection">
          :[]
        </span>
      </md-table-cell>
      <md-table-cell md-label="类型">
        <a v-if="item.type&&item.type.type=='entity'" @click="goItem(item)">{{ item.type.name }}</a>
        <a v-else-if="item.type&&item.type.type=='enum'" @click="goItem(item)">{{ item.type.name }}</a>
        <span v-else>{{ item.type.name }}</span>
      </md-table-cell>
      <md-table-cell md-label="显示名称">{{ item.comment }}</md-table-cell>
    </md-table-row>
    <md-table-empty-state md-label="没有元数据信息"></md-table-empty-state>
  </md-table>
</template>
<script>
export default {
  data() {
    return {
      mainDatas: { fields: [] }
    };
  },
  watch: {
    '$route': function() {
      this.fetchData();
    }
  },
  methods: {
    goItem(item) {
      if (item && item.type && item.type.id) {
        this.$go({ name: 'md.show', params: { id: item.type.id } });
      }
    },
    fetchData() {
      const options = {};
      this.$http.get('sys/entities/' + this.$route.params.id, { params: options }).then(response => {
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
