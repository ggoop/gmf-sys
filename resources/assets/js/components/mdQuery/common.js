export default {
  props: {
    mdQueryId: String,
    mdEntityId: String,
    mdItems: Array
  },
  data() {
    return {
      selectItems: [],
      loading: false
    };
  },
  methods: {
    onItemSelect(datas) {
      this.selectItems = datas;
    },
    onItemAdd() {
      this.$refs.newItemDialog.open();
    },
    onItemUp(item, index) {
      if (index <= 0) return;
      this.mdItems.splice(index, 1);
      item = JSON.parse(JSON.stringify(item));
      this.mdItems.splice(index - 1, 0, item);
    },
    onItemDown(item, index) {
      if (index > this.mdItems.length - 1) return;
      this.mdItems.splice(index, 1);
      item = JSON.parse(JSON.stringify(item));
      this.mdItems.splice(index + 1, 0, item);
    },
    onItemRemove() {
      var ind = -1;
      for (var i = this.mdItems.length - 1; i >= 0; i--) {
        ind = -1;
        this._.forEach(this.selectItems, (sv, sk) => {
          if (this.mdItems[i].name === sv.name) {
            ind = i;
          }
        });
        if (ind >= 0) {
          this.mdItems.splice(ind, 1);
        }
      }
    },
    onNewItemConfirm() {
      var selectedItems = this.$refs.onNewItemTree.getItems();
      this._.forEach(selectedItems, (v, k) => {
        var need = false,
          item = this.formatFieldToItem(v);
        this._.forEach(this.mdItems, (va, ka) => {
          if (va.name == item.name) {
            need = true;
          }
        });
        if (need === false) {
          this.mdItems.push(item);
        }
      });
      this.$refs.newItemDialog.close();
    },
    onNewItemCancel() {
      this.$refs.newItemDialog.close();
    },
    formatFieldToItem(field) {
      return {
        name: field.path,
        comment: field.path_name,
        type_id: field.type_id,
        type_name: field.type_name,
        type_type: field.type_type,
      };
    },
  }
};