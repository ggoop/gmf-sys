<template>
  <md-tree-view ref="tree" md-label-field="comment" :nodes="node.childs" @expand="expandNode"></md-tree-view>
</template>
<script>
export default {
  props: {
    mdEntityId: {
      type: String
    }
  },
  watch: {
    mdEntityId(val) {
      this.loadAllNodes();
    },
  },
  data() {
    return {
      node: {
        type_id: '',
        path: '',
        path_name: '',
        childs: []
      }
    }
  },
  methods: {
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
        this._.forEach(response.data.data.fields, (v, k) => {
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
  created() {

  },
  mounted() {
    this.loadAllNodes();
  },
};
</script>