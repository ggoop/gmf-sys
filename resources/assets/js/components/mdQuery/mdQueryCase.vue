<template>
  <div class="md-query-case">
    <slot></slot>
    <md-dialog :md-active.sync="showDialog" :md-click-outside-to-close="false" @md-opened="onOpen" @md-closed="onClose" class="md-query-case-dialog">
      <md-button class="md-icon-button md-dialog-button-close" @click.native="cancel()">
        <md-icon>close</md-icon>
      </md-button>
      <md-dialog-content class="no-padding layout-column layout-fill">
        <md-tabs class="md-primary layout-column layout-fill flex" :md-swipeable="true" md-right :md-dynamic-height="false">
          <md-tab md-label="条件" md-icon="filter_list" v-if="mdShowWhere">
            <md-layout md-gutter class="layout-fill">
              <md-query-case-where :md-entity-id="options.entity_id" :md-items="options.wheres"></md-query-case-where>
            </md-layout>
          </md-tab>
          <md-tab md-label="栏目" md-icon="more_horiz" v-if="mdShowField">
            <md-layout md-gutter class="layout-fill">
              <md-query-case-field :md-entity-id="options.entity_id" :md-items="options.fields"></md-query-case-field>
            </md-layout>
          </md-tab>
          <md-tab md-label="排序" md-icon="sort" v-if="mdShowOrder">
            <md-layout md-gutter class="layout-fill">
              <md-query-case-order :md-entity-id="options.entity_id" :md-items="options.orders"></md-query-case-order>
            </md-layout>
          </md-tab>
          <md-tab md-label="设置" md-icon="build">
            <md-layout md-gutter class="layout-fill">
            </md-layout>
          </md-tab>
        </md-tabs>
      </md-dialog-content>
      <md-dialog-actions>
        <div class="layout layour-row">
          <md-field class="layout-align-center-center query-cases">
            <md-select v-model="options.case_id">
              <md-option value="0">新方案</md-option>
              <md-option value="-1">不使用方案</md-option>
              <md-option :value="item.id" v-for="item in cases" :key="item.id">{{ item.name }}</md-option>
            </md-select>
          </md-field>
          <md-button v-if="options.case_id!='-1'" class="md-icon-button md-warn" @click.native="onSaveCase">
            <md-icon>save</md-icon>
          </md-button>
        </div>
        <span class="flex"></span>
        <md-button class="md-accent md-raised" @click.native="query">确定</md-button>
        <md-button class="md-warn" @click.native="cancel">取消</md-button>
      </md-dialog-actions>
      <md-loading :loading="loading"></md-loading>
      <md-dialog-prompt :md-active.sync="diaNewCaseNameShow" md-title="输入方案名称" md-confirm-text="确认" md-cancel-text="取消" @md-confirm="onNewCaseSave" v-model="options.case_name">
      </md-dialog-prompt>
    </md-dialog>
  </div>
</template>
<style scoped>
.query-cases {
  margin: 0px;
  padding: 0px;
  min-height: auto;
}

</style>
<script>
export default {
  props: {
    mdQueryId: String,
    mdCaseId: String,
    mdShowWhere: {
      type: Boolean,
      default: true
    },
    mdShowField: {
      type: Boolean,
      default: true
    },
    mdShowOrder: {
      type: Boolean,
      default: true
    },
  },

  data() {
    return {
      inited: false,
      loading: 0,
      showDialog: false,
      diaNewCaseNameShow: false,
      options: {
        case_id: '0',
        case_name: '',
        query_id: '',
        query_name: '',
        size: 0,
        wheres: [],
        fields: [],
        orders: []
      },
      cases: []
    }
  },
  watch: {
    'options.case_id' (val) {
      if (val && val&&val.length>2) {
        this.fetchCase();
      }
    },
  },
  computed: {

  },
  methods: {
    async init() {
      if (this.inited) {
        return;
      }
      if (!this.mdQueryId) return;

      await this.fetchCase();
      const promise = new Promise((resolve, reject) => {
        this.$emit('init', this.options, { resolve, reject });
      });
      promise.then((value) => {
        this.inited = true;
      }, (reason) => {
        this.inited = false;
      });
    },
    async fetchCase() {
      try {
        const response = await this.$http.get('sys/queries/' + this.mdQueryId, { params: { case_id: this.options.case_id } });
        this.options.query_id = response.data.data.query_id;
        this.options.query_name = response.data.data.query_name;
        this.options.case_id = response.data.data.case_id || '';
        this.options.case_name = response.data.data.case_name || '';
        this.options.size = response.data.data.size;
        this.options.entity_id = response.data.data.entity_id;
        this.options.entity_name = response.data.data.entity_name;
        this.options.entity_comment = response.data.data.entity_comment;
        this.options.filter = response.data.data.filter;

        this.options.wheres = response.data.data.wheres;
        this.options.orders = response.data.data.orders;
        this.options.fields = response.data.data.fields;
      } catch (e) {
        this.$toast(e);
      }
    },
    async fetchCases() {
      try {
        if (!this.options.query_id) return;
        const response = await this.$http.get('sys/queries/' + this.options.query_id + '/cases');
        this.cases = response.data.data;
      } catch (e) {
        this.$toast(e);
      }
    },
    async onNewCaseSave(type) {
      if (type == 'ok' && this.options.case_name) {
        await this.saveCase();
      }
    },
    async onSaveCase() {
      if (this.options.case_id&&this.options.case_id.length>2) {
        await this.saveCase();
      } else {
        this.options.case_name = '';
        this.diaNewCaseNameShow = true;
      }
    },
    async saveCase() {
      var caseModel = {
        size: this.options.size,
        name: this.options.case_name,
        query_id: this.options.query_id,
        wheres: this.options.wheres.filter(v => !v.sys_deleted).map(v => this._.omit(v, ['sys_deleted', 'sys_created', 'sys_updated', 'vueRowId'])),
        orders: this.options.orders.filter(v => !v.sys_deleted).map(v => this._.omit(v, ['sys_deleted', 'sys_created', 'sys_updated', 'vueRowId'])),
        fields: this.options.fields.filter(v => !v.sys_deleted).map(v => this._.omit(v, ['sys_deleted', 'sys_created', 'sys_updated', 'vueRowId']))
      };
      if (this.options.case_id&&this.options.case_id.length>2) {
        caseModel.id = this.options.case_id;
      }
      try {
        const response = await this.$http.post('sys/query-cases/', caseModel);
        await this.fetchCases();
        this.options.case_id = response.data.data.id;
        this.options.case_name = response.data.data.name;
      } catch (e) {
        this.$toast(e);
      }
    },
    query() {
      var caseModel = this.getQueryCase();
      this.$emit('query', caseModel);
      this.showDialog = false;
    },
    async open() {
      await this.init();
      await this.fetchCases();
      this.showDialog = true;
    },
    cancel() {
      this.$emit('cancel', this.options);
      this.showDialog = false;
    },
    onOpen() {
      this.$emit('open', this.options);
    },
    onClose() {
      this.$emit('close', this.options);
    },
    getQueryCase() {
      var qc = {
        size: this.options.size,
        name: this.options.case_name,
        query_id: this.options.query_id || this.mdQueryId,
        wheres: { case: { items: [], boolean: 'and' } },
        orders: [],
        fields: []
      };
      if (this.options.case_id == '-1') {
        return qc;
      }
      if (this.options.case_id&&this.options.case_id.length>2) {
        qc.id = this.options.case_id;
      }
      this._.each(this.options.wheres, (v) => {
        if (v && (!v.sys_deleted)) {
          var item = this.formatCaseWhereItem(v);
          if (item) qc.wheres.case.items.push(item);
        }
      });
      this._.each(this.options.orders, (v) => {
        if (v && (!v.sys_deleted)) {
          qc.orders.push({ name: v.name, direction: v.direction, comment: v.comment });
        }
      });
      this._.each(this.options.fields, (v) => {
        if (v && (!v.sys_deleted)) {
          qc.fields.push({ name: v.name, comment: v.comment });
        }
      });
      return qc;
    },
    formatCaseWhereItem(where) {
      var has = false;
      var whereItem = {
        name: where.name,
        comment: where.comment,
        operator: where.operator_enum
      };
      whereItem.value = this.getCaseWhereValue(where);
      if (whereItem.value === false) { return false; }
      return whereItem;
    },
    getCaseWhereValue(where) {
      if (where.operator_enum.indexOf('null') >= 0) {
        return '';
      }
      if (!where.value) {
        return false;
      }
      if (where.operator_enum === 'between') {
        if (where.value && where.value2) {
          var values = [];
          if (where.type_type == 'ref') {
            values.push(getRefWhereItemValue(where, where.value));
            values.push(getRefWhereItemValue(where, where.value2));
          } else {
            values.push(where.value);
            values.push(where.value2);
          }
          return values[0] !== false && values[1] !== false ? values : false;
        }
      } else {
        if (where.type_type == 'ref') {
          return this.getRefWhereItemValue(where, where.value);
        } else {
          return where.value;
        }
      }
      return false;
    },
    getRefWhereItemValue(where, values) {
      if (values === false) return false;
      var valueField = 'id',
        temp = false,
        value = false;
      if (where.refs && where.refs.valueField) {
        valueField = where.refs.valueField;
      }
      if (where.multiple && values && values.length > 0) {
        value = [];
        for (var i = 0; i < values.length; i++) {
          temp = values[i][valueField];
          if (temp !== '' && temp !== undefined && temp !== false) {
            value.push(temp);
          }
        }
      } else if (values && values[valueField]) {
        value = values[valueField];
      }
      return value;
    },
  },
  created() {

  },
  mounted() {},
};

</script>
<style lang="scss">
.md-query-case {
  >.md-button {
    color: #34957a;
  }
}

.md-query-case-dialog.md-dialog {
  min-width: 7rem;
  min-height: 70%;
  max-width: 80%;
  max-height: 90%;

  .md-tabs .md-tab {
    padding: 0px;
  }
}

</style>
