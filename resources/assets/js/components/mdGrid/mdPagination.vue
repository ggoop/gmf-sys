<template>
  <div class="md-pagination layout-row layout-align-center-center" v-if="pager">
    <md-button class="md-icon-button md-pagination-previous" @click.native="previousPage" :disabled="currentPage === 1">
      <md-icon>keyboard_arrow_left</md-icon>
    </md-button>
    <span class="md-pagination-info">{{pageInfo}}</span>
    <md-button class="md-icon-button md-pagination-next" @click.native="nextPage" :disabled="shouldDisable">
      <md-icon>keyboard_arrow_right</md-icon>
    </md-button>
    <span class="md-pagination-label nowrap">每页:</span>
    <md-field class="md-pagination-select">
      <md-select v-model="currentSize" md-class="md-pagination-menu" class="md-pagination-select" v-if="options">
        <md-option v-for="amount in options" :key="amount" :value="amount">{{ amount }}</md-option>
      </md-select>
    </md-field>
  </div>
</template>
<script>
export default {
  props: {
    pager: Object,
    options: {
      type: [Array, Boolean],
      default: function() {
        return [5, 10, 20, 50]
      }
    }
  },
  data() {
    return {
      subTotal: 0,
      totalItems: 0,
      currentPage: 1,
      currentSize: 0
    };
  },
  watch: {
    'pager.total' (val) {
      this.totalItems = isNaN(val) ? Number.MAX_SAFE_INTEGER : parseInt(val, 10);
    },
    'pager.size' (val) {
      this.currentSize = parseInt(val, 10);
    },
    'pager.page' (val) {
      this.currentPage = parseInt(val, 10);
    },
    'currentSize' (val) {
      this.changeSize();
    },
  },
  computed: {
    lastPage() {
      return false;
    },
    shouldDisable() {
      return this.currentSize * this.currentPage >= this.totalItems;
    },
    pageInfo() {
      if (!this.pager.total) {
        return '0' + '/' + this.pager.total;
      }
      var sub = this.currentPage * this.currentSize;
      sub = sub > this.pager.total ? this.pager.total : sub;
      return (((this.currentPage - 1) * this.currentSize) + 1) + '-' + sub + '/' + this.pager.total;
    }
  },
  methods: {
    emitPaginationEvent() {
      if (this.canFireEvents) {
        this.$emit('pagination', { size: this.currentSize, page: this.currentPage });
      }
    },
    changeSize() {
      if (this.canFireEvents) {
        this.currentPage = 1;
        this.$emit('size', this.currentSize);
        this.emitPaginationEvent();
      }
    },
    previousPage() {
      if (this.canFireEvents) {
        this.currentPage--;
        this.$emit('page', this.currentPage);
        this.emitPaginationEvent();
      }
    },
    nextPage() {
      if (this.canFireEvents) {
        this.currentPage++;
        this.$emit('page', this.currentPage);
        this.emitPaginationEvent();
      }
    }
  },
  mounted() {
    if (!this.currentSize && this.pager) this.currentSize = this.pager.size;
    if (!this.currentSize && this.options) this.currentSize = parseInt(this.options[0]);
    this.$nextTick(() => {
      this.canFireEvents = true;
    });
  }
};

</script>
<style lang="scss">
@import "~gmf/components/MdLayout/mixins";
.md-menu-content.md-select-menu.md-pagination-menu {
  width: auto;
}

.md-pagination {
  color: #404040;
  .md-select {
    width: auto;
    min-width: 20px;
    margin: 0 1px;
  }
  @include md-layout-xsmall {
    .md-pagination-label,
    .md-pagination-select {
      display: none;
    }
  }
  .md-field {

    margin: 0px 10px;
    min-height: 0px;
    padding: 0;
    min-width: auto;
    width: auto;
    &.md-pagination-select {
      width: 50px;
    }
  }
  .md-pagination-info {
    min-width: 74px;
    text-align: center;
  }
  .md-button {
    min-width: 15px;
    margin: 0px;
  }
}

</style>
