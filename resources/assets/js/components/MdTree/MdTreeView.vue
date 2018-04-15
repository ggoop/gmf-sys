<template>
  <div class="md-tree-view" :class="[$mdActiveTheme]">
    <md-tree :md-selection="mdSelection" :md-label-field="mdLabelField" :md-auto-select="mdAutoSelect" :nodes="nodes">
    </md-tree>
  </div>
</template>
<script>
import MdComponent from 'gmf/core/MdComponent'
export default new MdComponent({
  name: 'MdTreeView',
  props: {
    nodes: {
      type: Array,
      default: []
    },
    mdLabelField: {
      type: String,
      default: 'name'
    },
    mdAutoSelect: {
      type: Boolean,
      default: true
    },
    mdSelection: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      selecteds: [],
      current: null,
    };
  },
  methods: {
    setCurrent(current) {
      if (this.current) {
        this.current.outCurrent();
      }
      this.current = current;
      this.focus();
    },
    selectItem(node, unSelected) {
      const index = this.selecteds.indexOf(node);
      if (unSelected && index >= 0) {
        this.selecteds.splice(index, 1);
      } else if (index < 0) {
        this.selecteds.push(node);
      }
      this.select();
    },
    addNode(node) {

    },
    insertNode(node) {

    },
    focus() {
      if (!this.current) {

      }
      if (this.current) {
        this.$emit('focus', this.current.node);
      }
    },
    select() {
      this.$emit('select', this.selecteds);
    },
    expand(node) {
      this.$emit('expand',node);
    },
  },
});
</script>