export default {
  props: {
    mdQueryId: String,
    mdEntityId: String,
    mdItems: Array
  },
  data() {
    return {
      newItemDialogShow:false,
      loading: false
    };
  },
  methods: {
    getItemIndex(item) {
      var ind = -1;
      this.mdItems.forEach((v, k) => {
        if (v.vueRowId === item.vueRowId) {
          ind = k;
          return k;
        }
      });
      return ind;
    },
    onItemAdd() {
      this.newItemDialogShow=true;
    },
    onItemUp(item,event) {
      if (event) {
        event.preventDefault && event.preventDefault();
        event.stopPropagation && event.stopPropagation();
      }
      const index=this.getItemIndex(item);
      if(index<=0)return;
      this.mdItems.splice(index, 1);
      item = JSON.parse(JSON.stringify(item));
      this.mdItems.splice(index - 1, 0, item);
    },
    onItemDown(item,event) {
      if (event) {
        event.preventDefault && event.preventDefault();
        event.stopPropagation && event.stopPropagation();
      }
      const index=this.getItemIndex(item);
      if (index > this.mdItems.length - 1) return;
      this.mdItems.splice(index, 1);
      item = JSON.parse(JSON.stringify(item));
      this.mdItems.splice(index + 1, 0, item);
    },
    onItemRemove(options) {
      var ind = -1;
      for (var i = this.mdItems.length - 1; i >= 0; i--) {
        ind = -1;
        this._.forEach(options.data, (sv, sk) => {
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
      this.newItemDialogShow=false;
    },
    onNewItemCancel() {
      this.newItemDialogShow=false;
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