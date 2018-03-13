<template>
  <div class="md-tree-node layout-row layout-align-start-center" :class="[classes]"  @click="click">
    <md-checkbox v-model="checkbox" v-if="!!mdSelection" @change="select"></md-checkbox>
    <div class="md-tree-node-name flex">
      {{node[mdLabelField]}}
    </div>
    <div @click="toggle($event)" v-if="node.childs&&node.childs.length">
      <md-icon class="md-expand-indicator">keyboard_arrow_down</md-icon>
    </div>
  </div>
</template>
<script>
import getClosestVueParent from 'gmf/core/utils/getClosestVueParent';
export default {
  name: 'MdTreeNode',
  props: {
    node: {
      type: Object,
      default: {}
    },
    mdLabelField: {
      type: String,
      default: 'name'
    },
    mdAutoSelect: Boolean,
    mdSelection: Boolean
  },
  data() {
    return {
      checkbox: false,
      selectedRows: [],
      isActive: false,
      isOpened: false,
    };
  },
  computed: {
    classes() {
      return {
        'is-active': this.isActive,
        'is-opened': this.isOpened
      };
    },
    status() {
      if (!this.node.childs || this.node.childs.length == 0) return 'leafed';
      return this.isOpened ? 'opened' : 'closed';
    }
  },
  methods: {
    toggle(event) {
      event && event.preventDefault && event.preventDefault();
      event && event.stopPropagation && event.stopPropagation();
      if (!this.isOpened) {
        this.parentContainer && this.parentContainer.expand(this.node);
      }
      this.isOpened = !this.isOpened;
    },
    click() {
      this.parentContainer && this.parentContainer.setCurrent(this);
      this.isActive = true;
      if (!this.isOpened) {
        this.parentContainer && this.parentContainer.expand(this.node);
      }
      this.isOpened = !this.isOpened;
    },
    outCurrent() {
      this.isActive = false;
    },
    select(selected) {
      this.parentContainer && this.parentContainer.selectItem(this.node, !selected);
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.parentContainer = getClosestVueParent(this.$parent, 'md-tree-view');
      if (!this.parentContainer) {
        this.$destroy();
        throw new Error('You should wrap the md-tree in a md-tree-view');
      }
    });
  }
};
</script>