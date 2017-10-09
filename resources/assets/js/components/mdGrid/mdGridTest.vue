<template>
  <div style="padding:40px;margin:20px">
    <md-ref @init="init_group_ref" md-ref-id="suite.cbo.country.ref" ref="lineRef" @confirm="lineRefClose"></md-ref>
    <md-table-card class="flex">
      <md-table class="flex">
        <md-table-header>
          <md-table-row>
            <md-table-head>阿米巴</md-table-head>
            <md-table-head>阿米巴</md-table-head>
            <md-table-head>阿米巴</md-table-head>
            <md-table-head>阿米巴</md-table-head>
            <md-table-head>利润</md-table-head>
          </md-table-row>
        </md-table-header>
        <md-table-body>
          <md-table-row v-for="(row, rowIndex) in refValues" :key="rowIndex" :md-item="row" md-selection>
            <md-table-cell>
              <md-input-container>
                <md-input-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" v-model="row.group4"></md-input-ref>
              </md-input-container>
            </md-table-cell>
            <md-table-cell>
              <md-input-container>
                <md-input-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" v-model="row.group3"></md-input-ref>
              </md-input-container>
            </md-table-cell>
            <md-table-cell>
              <md-input-container>
                <md-input-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" v-model="row.group2"></md-input-ref>
              </md-input-container>
            </md-table-cell>
            <md-table-cell>
              <md-input-container>
                <md-input-ref @init="init_group_ref" md-ref-id="suite.amiba.group.ref" v-model="row.group1"></md-input-ref>
              </md-input-container>
            </md-table-cell>
            <md-table-cell>
              <md-input-container>
                <md-input v-model="row.profit"></md-input>
              </md-input-container>
            </md-table-cell>
          </md-table-row>
        </md-table-body>
      </md-table>
    </md-table-card>
    <button @click="onLineAdd">open ref</button>
    <md-grid :datas="datas" :auto-load="true">
      <md-grid-column field="id" label="id" :hidden="true"></md-grid-column>
      <md-grid-column field="code" label="编码"></md-grid-column>
      <md-grid-column field="name" label="名称"></md-grid-column>
      <md-grid-column field="date" label="日期" :formatter="formatter"></md-grid-column>
      <md-grid-column label="自定义">
        <template scope="row">
          {{ row.name }} wrote {{ row.qty }} songs.
        </template>
        <template slot="editor" scope="row">
          <input type="date"> {{ row.qty }}
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
      refValues: []
    };
  },
  methods: {
    onLineAdd() {
      this.$refs['lineRef'].open();
    },
    lineRefClose(datas) {
      this._.forEach(datas, (v, k) => {
        this.refValues.push({ group1: v, group2: v, group3: v, group4: v });
      });
    },
    init_group_ref(options) {
      options.wheres.purpose = false;
    },
    formatter(value, columnProperties) {
      return `Hi, ${value}`;
    },
    getDatas({ pager, filter, sort }) {
      var datas = [
        { id: 'John', code: 'Lennon1', name: 'Guitar', date: '04/10/1940', qty: 72 },
        { id: 'Paul', code: 'McCartney2', name: 'Bass', date: '18/06/1942', qty: 70 },
        { id: 'George', code: 'Harrison3', name: 'Guitar', date: '25/02/1943', qty: 22 },
        { id: 'Ringo', code: 'Starr4', name: 'Drums', date: '07/07/1940', qtys: 2 },
        { id: 'Paul', code: 'McCartney5', name: 'Bass', date: '18/06/1942', qty: 70 },
        { id: 'George', code: 'Harrison6', name: 'Guitar', date: '25/02/1943', qty: 22 },
        { id: 'Ringo', code: 'Starr7', name: 'Drums', date: '07/07/1940', qtys: 2 },
        { id: 'Paul', code: 'McCartney8', name: 'Bass', date: '18/06/1942', qty: 70 },
        { id: 'George', code: 'Harrison9', name: 'Guitar', date: '25/02/1943', qty: 22 },
        { id: 'Ringo', code: 'Starr10', name: 'Drums', date: '07/07/1940', qtys: 2 },
        { id: 'Ringo', code: 'Starr11', name: 'Drums', date: '07/07/1940', qtys: 2 },
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