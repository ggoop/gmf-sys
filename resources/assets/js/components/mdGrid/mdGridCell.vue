<template>
  <td @click="handleClick" :class="[objClass]">
    <md-grid-cell-edit v-if="status=='editor'" class="md-grid-cell-container" :column="column" :row="row">
      <slot name="editor"></slot>
    </md-grid-cell-edit>
    <md-grid-cell-show v-else class="md-grid-cell-container" :column="column" :row="row">
      <slot></slot>
    </md-grid-cell-show>
  </td>
</template>
<script>
import getClosestVueParent from '../../core/utils/getClosestVueParent';
import mdGridCellShow from './mdGridCellShow';
import mdGridCellEdit from './mdGridCellEdit';
export default {
  components: {
    mdGridCellShow,
    mdGridCellEdit
  },
  props: {
    column: { type: Object },
    row: { type: Object },
    selection: { default: false, type: Boolean },
    type: { default: 'td', type: String },
  },
  computed: {
    objClass() {
      return {
        'is-tool': this.column && this.column.isTool,
        'md-grid-selection': this.selection
      };
    },
    canEdit() {
      return (!this.selection) && this.column && (this.column.templateEditor || this.column.canEdit);
    },
  },
  data() {
    return {
      parentTable: {},
      status: 'display'
    };
  },
  methods: {
    handleClick(event) {
      if (!this.canFireEvents) return;
      this.$emit('click', event);
      this.beginEdit();
    },
    beginEdit() {
      if (!this.canEdit) {
        if (this.parentTable.focusCell) {
          this.parentTable.focusCell.endEdit();
        }
        this.parentTable.focusCell = this;
        return;
      }
      if (this.status == 'display') {
        if (this.parentTable.focusCell) {
          this.parentTable.focusCell.endEdit();
        }
        this.parentTable.focusCell = this;
        this.status = 'editor';
      }
    },
    endEdit() {
      this.status = 'display'
    },
  },
  mounted() {
    this.parentTable = getClosestVueParent(this.$parent, 'md-grid');
    this.$nextTick(() => {
      this.canFireEvents = true;
    });
  },
};
</script>