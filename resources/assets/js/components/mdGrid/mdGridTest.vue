<template>
  <div style="padding:40px;margin:20px">
    <md-grid :datas="datas">
      <md-grid-column field="id" label="id" :hidden="true"></md-grid-column>
      <md-grid-column field="code" label="编码"></md-grid-column>
      <md-grid-column field="name" label="名称"></md-grid-column>
      <md-grid-column field="date" label="日期" :formatter="formatter"></md-grid-column>
      <md-grid-column label="自定义">
        <template scope="row">
          {{ row.name }} wrote {{ row.qty }} songs.
        </template>
      </md-grid-column>
    </md-grid>
    <md-divider style="margin:20px 0px;"></md-divider>
    <md-grid :datas="fetchData">
      <md-grid-column field="id" label="id" :hidden="true"></md-grid-column>
      <md-grid-column field="code" label="编码"></md-grid-column>
      <md-grid-column field="name" label="名称"></md-grid-column>
      <md-grid-column field="date" label="日期" :formatter="formatter"></md-grid-column>
      <md-grid-column label="自定义">
        <template scope="row">
          {{ row.name }} wrote {{ row.qty }} songs.
        </template>
      </md-grid-column>
    </md-grid>
  </div>
</template>
<script>
export default {
  data() {
    return {
      datas: [],
    };
  },
  methods: {
    formatter(value, columnProperties) {
      return `Hi, ${value}`;
    },
    getDatas({ pager, filter, sort }) {
      var datas = [
        { id: 'John', code: 'Lennon', name: 'Guitar', date: '04/10/1940', qty: 72 },
        { id: 'Paul', code: 'McCartney', name: 'Bass', date: '18/06/1942', qty: 70 },
        { id: 'George', code: 'Harrison', name: 'Guitar', date: '25/02/1943', qty: 22 },
        { id: 'Ringo', code: 'Starr', name: 'Drums', date: '07/07/1940', qtys: 2 },
      ];
      this.datas = datas;
    },
    async fetchData({ pager, filter, sort }) {
      const response = await this.$http.get('cbo/countries',{params:pager});
      return response;
    }
  },
  created() {
    this.getDatas({});
  }
};
</script>