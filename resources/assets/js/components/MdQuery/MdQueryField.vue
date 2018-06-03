<template>
  <md-dialog :md-active.sync="isActive" @md-opened="onOpen" @md-closed="closeDialog">
    <md-toolbar>
      <h1 class="md-title">选择更多内容</h1>
    </md-toolbar>
    <md-dialog-content class="no-padding layout-column flex">
      <md-tree-view ref="tree" md-label-field="comment" :nodes="node.childs" @expand="expandNode"></md-tree-view>
    </md-dialog-content>
    <md-dialog-actions>
      <span class="flex"></span>
      <md-button class="md-accent md-raised" @click.native="confirm">确定</md-button>
      <md-button class="md-warn" @click.native="closeDialog">取消</md-button>
    </md-dialog-actions>
  </md-dialog>
</template>
<script>
import forEach from 'lodash/forEach'
export default {
  props: {
    mdActive: Boolean,
    mdEntityId: {
      type: String
    }
  },
  data() {
    return {
      isActive: false,
      node: {
        type_id: '',
        path: '',
        path_name: '',
        childs: []
      }
    }
  },
  watch: {
     async mdActive(isActive) {
      this.isActive = isActive;
      await this.$nextTick();
    },
  },
  methods: {
    confirm() {
      this.$emit('confirm', this.getItems());
      this.closeDialog();
    },
    closeDialog() {
      this.isActive=false;
      this.$emit('update:mdActive', false);
    },
    onOpen() {
      this.loadAllNodes();
    },
    loadAllNodes() {
      this.node.type_id = this.mdEntityId;
      this.loadEntityNodes(this.node);
    },
    loadEntityNodes(parentNode) {
      if (!parentNode || !parentNode.type_id) {
        return;
      }
      if (!parentNode.childs || parentNode.childs.length > 1) {
        return;
      }
      if (parentNode.childs.length == 1 && parentNode.childs[0] && parentNode.childs[0].type_id !== '') {
        return;
      }
      this.$http.get('sys/entities/' + parentNode.type_id).then(response => {
        parentNode.childs.splice(0, parentNode.childs.length);
        forEach(response.data.data.fields, (v, k) => {
          var item = {
            name: v.name,
            comment: v.comment,
            type_id: v.type.id,
            type_name: v.type.name,
            type_type: v.type.type
          };
          item.path = item.name;
          item.path_name = item.comment;
          if (v.type.type === 'entity') {
            item.childs = [{ type_id: '' }];
          }
          if (parentNode.path) {
            item.path = parentNode.path + '.' + item.path;
          }
          if (parentNode.path_name) {
            item.path_name = parentNode.path_name + '.' + item.path_name;
          }
          parentNode.childs.push(item);
        });
      }, response => {});
    },
    getItems() {
      return this.$refs.tree.selecteds;
    },
    expandNode(node) {
      if (node.type_type === 'entity') {
        this.loadEntityNodes(node);
      }
    }
  },
};

</script>
