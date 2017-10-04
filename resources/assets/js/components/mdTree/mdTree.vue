<template>
  <ul>
    <li v-for="node in nodes">
      <md-tree-node :node="node" :md-label-field="mdLabelField" :md-selection="mdSelection" :md-auto-select="mdAutoSelect">
      </md-tree-node>
      <md-tree v-if="node.childs&&node.childs.length" :md-label-field="mdLabelField" :nodes="node.childs" :md-selection="mdSelection" :md-auto-select="mdAutoSelect">
      </md-tree>
    </li>
  </ul>
</template>
<script>
export default {
  props: {
    nodes: {
      type: Array,
      default: []
    },
    mdLabelField: {
      type: String,
      default: 'name'
    },
    mdAutoSelect: Boolean,
    mdSelection: Boolean
  },
  methods: {},
  beforeCreate: function() {
    this.$options.components.mdTree = require('./mdTree.vue');
  }
};
</script>