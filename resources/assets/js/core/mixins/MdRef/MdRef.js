export default {
  props: {
    value: {
      type: Object,
    },
    multiple: {
      type: Boolean,
      default: true
    },
    mdRefId: String,
    mdRefType: {
      type: String,
      default: 'entity'
    },
    mdQ: String,
    options: {
      type: Object,
      default () {
        return {
          wheres: {},
          orders: []
        }
      }
    },
    mdActive: Boolean,
    mdInit: { type: Function },
  },
  data() {
    return {
      selectedRows: [],
      loading: 0,
    };
  },
  watch: {
    value(value) {
      if (!value) {
        this.selectedRows = [];
      } else {
        if (common.isArray(value)) {
          this.selectedRows = value;
        } else {
          this.selectedRows = [value];
        }
      }
    },
    mdQ() {
      this.onDoSearch();
    },
    mdActive(v) {
      v && this.onLoad();
    }
  },
  methods: {
    onDoSearch: _.debounce(function() {
      if (!this.mdActive) return;
      if (this.loadData) {
        this.loadData(this.mdQ);
      }
    }, 500),
    onLoad() {
      if (!this.mdActive) return;
      if (this.mdInit) {
        Promise.all([this.mdInit(this.options)]).then(v => {
          this.loadData && this.loadData();
        });
      } else {
        this.loadData && this.loadData();
      }
    },
    getReturnValue() {
      return this.selectedRows.map(row => this._.pick(row, Object.keys(row).filter(f => f !== 'vueRowId')));
    },
    onCancel() {
      this.$emit('md-cancel', false);
    },
    onConfirm() {
      this.$emit('md-confirm', this.getReturnValue());
    },
  },
}
