export default {
  methods: {
    async fetchLineDatas({ pager, filter, sort }) {
      if (!this.model.main.id) {
        return [];
      }
      const options = this._.extend({}, { sortField: sort.field, sortOrder: sort.order }, pager);
      return await this.$http.get(this.route + '/' + this.model.main.id + '/lines', { params: options });
    },
    beforeSave() {
      if (this.$refs.grid) {
        this.$refs.grid.endEdit();
        this.model.main.lines = this.$refs.grid.getPostDatas();
      }
    },
    afterLoadData() {
      this.$refs.grid && this.$refs.grid.refresh();
    },
    afterCreate() {
      this.$refs.grid && this.$refs.grid.refresh();
    },
    afterCopy() {
      this.$refs.grid && this.$refs.grid.refresh();
    },
    afterSave() {
      this.$refs.grid && this.$refs.grid.refresh();
    },
  }
};