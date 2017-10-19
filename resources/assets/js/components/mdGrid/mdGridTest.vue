<template>
  <div style="padding:40px;margin:20px">
    <md-grid :datas="datas" :auto-load="true" :row-focused="false">
      <md-grid-column field="id" label="id" :hidden="true" />
      <md-grid-column field="code" label="编码" editable/>
      <md-grid-column field="name" label="名称" />
      <md-grid-column field="date" label="日期" :formatter="formatter" />
    </md-grid>
    <div>grid2</div>
    <md-grid :datas="fetchData">
      <md-grid-column field="id" label="id" :hidden="true" />
      <md-grid-column field="code" label="编码" editable/>
      <md-grid-column field="name" label="名称" />
      <md-grid-column field="date" label="日期" :formatter="formatter" />
    </md-grid>
  </div>
</template>
<script>
export default {
  data() {
    return {
      datas: [],
      refValues: []
    };
  },
  methods: {
    onLineAdd() {
      this.$refs['lineRef'].open();
    },

    formatter(value, columnProperties) {
      return `Hi, ${value}`;
    },
    getDatas({ pager, filter, sort }) {
      var datas = [
        { id: 'John', code: 'Lennon1', name: 'Guitar', date: '04/10/1940', type_enum: 'indect' }
      ];
      this.datas = datas;
    },
    async fetchData({ pager, filter, sort }) {
      const response = await this.$http.get('cbo/countries', { params: pager });
      return response;
    }
  },
  created() {
    this.getDatas({});
  }
};
</script>