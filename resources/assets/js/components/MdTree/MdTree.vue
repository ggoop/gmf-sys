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
  name: 'MdTree',
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
    this.$options.components.MdTree = require('./MdTree.vue');
  }
};
</script>

<style lang="scss">
  @import "~components/MdAnimation/variables";
  .md-tree-view {
  overflow: auto;
  flex: 1;
  max-height: 100%;
  min-height: 100%;
  ul {
    list-style-type: none;
    li {
      margin: 0;
      +li {
        margin: 0;
      }
    }
  }
  >ul {
    margin: 0;
    padding: 0;
  }
  ul>li>ul {
    padding-left: 22px;
  }
  .md-tree-node {
    cursor: context-menu;
    line-height: 30px;
    padding: 0 8px;
    +ul {
      overflow: hidden;
      height: 0px;
    }

    &:hover {
      background-color: rgba(0, 0, 0, 0.05);
    }
    &.is-active {
      background-color: rgba(0, 0, 0, 0.05);
      font-weight: bold;
    }
    &.is-opened {
      .md-expand-indicator {
        transform: rotateZ(180deg) translate3D(0, 0, 0);
      }
      +ul {
        height: inherit;
      }
    }
    .md-checkbox {
      margin: 4px 8px 4px 0;
    }
  }
}
</style>