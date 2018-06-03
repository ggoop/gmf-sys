<template>
  <td @click="cellClicked($event)" :class="[objClass]" @dblclick="cellDblClick($event)" @mousemove="cellMousemove($event)" @mousedown="cellMousedown($event)" @mouseout="cellMouseup($event)" @mouseup="cellMouseup($event)">
    <div class="md-grid-cell-container">
      <slot></slot>
    </div>
  </td>
</template>
<script>
import getClosestVueParent from 'gmf/core/utils/MdGetClosestVueParent';
export default {
  components: {

  },
  props: {
    column: { type: Object },
  },
  computed: {
    objClass() {
      const ss = {
        'is-tool': this.column && this.column.isTool,
        'multiple': this.column && this.column.multiple,
        'is-resizing': this.isResizing,
        'is-sorting': this.isSorting,
        'can-resizing': this.canResizing,
        'can-sorting': this.canSorting,
      };
      if (this.column.isSortable()) {
        ss['has-sort'] = true;
        if (this.column.field == this.sort.field) {
          ss[`has-sort sort-${this.sort.order}`] = true;
        }
      }
      return ss;
    },
    sort() {
      return this.parentTable.sort || {};
    }
  },
  data() {
    return {
      parentTable: {},
      isResizing: false,
      isSorting: false,
      canResizing: false,
      canSorting: false,
      oldX: 0,
      oldWidth: 0
    };
  },
  methods: {
    cellMousemove(event) {
      if (event.offsetX > event.target.offsetWidth - 20) {
        this.canResizing = true;
      } else {
        this.canResizing = false;
      }
      if (event.offsetX > 10 && event.offsetX < 50) {
        this.canSorting = true;
      } else {
        this.canSorting = false;
      }
      if (this.isResizing == true) {
        if (this.oldWidth + (event.x - this.oldX) > 0) {
          this.column.width = (this.oldWidth + (event.x - this.oldX)) + 'px';
        }
      }
    },
    cellMousedown(event) {
      if (event.offsetX > event.target.offsetWidth - 20) {
        this.isResizing = true;
        this.oldX = event.x;
        this.oldWidth = parseInt(this.column.width);
      }
    },
    cellMouseup(event) {
      this.isResizing = false;
    },
    cellClicked(event) {
      if (!this.canFireEvents) return;
      if (event && event.offsetX > 10 && event.offsetX < 50) {
        if (this.column.isSortable()) {
          this.emitSort();
        }
      }
    },
    cellDblClick(event) {
      if (!this.canFireEvents) return;
      if (event.offsetX > event.target.offsetWidth - 20) {
        this.column.width = (parseInt(this.column.width) + 100) + 'px';
      }
    },
    emitSort() {
      if (!this.canFireEvents) return;
      const sort = { field: this.column.field };
      if (this.sort.field !== this.column.field) {
        sort.order = 'asc';
      } else if (this.sort.order === 'desc') {
        sort.field = '';
      } else {
        sort.order = (this.sort.order === 'asc' ? 'desc' : 'asc');
      }
      this.parentTable.onSorting(sort);
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
