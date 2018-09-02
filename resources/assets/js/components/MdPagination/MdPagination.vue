<template>
  <div class="md-pagination layout layout-align-center-center" v-if="pager"> 
    <md-button class="md-pagination-item" v-show="select_start>1" @click="goPage(1)">首页</md-button> 
    <md-button class="md-pagination-item" v-show="select_start>1" @click="previousPage">上一页</md-button>    
    <md-button class="md-ellipsis"  v-show="showPrevMore" disabled>...</md-button> 
    <md-button class="md-pagination-item" v-for="num in pageList" :disabled="currentPage==num" :key="num" @click="goPage(num)">{{num}}</md-button> 
    <md-button class="md-ellipsis"  v-show="showNextMore" disabled>...</md-button> 
    <md-button class="md-pagination-item" v-show="select_end<lastPage" @click="nextPage">下一页</md-button> 
    <md-button class="md-pagination-item" v-show="select_end<lastPage" @click="goPage(lastPage)">末页</md-button>  
    <md-menu md-size="auto" class="md-pagination-select" v-if="options" md-align-trigger>
      <md-button md-menu-trigger class="md-pagination-select-button">{{'每页'+currentSize+'条'}}</md-button>
      <md-menu-content>
        <md-menu-item>每页显示：</md-menu-item>
        <md-menu-item v-for="item in optionsList" @click="changeSize(item)" :key="item" :class="{'md-primary':item==currentSize}">{{item}}</md-menu-item>
      </md-menu-content>
    </md-menu>
  </div> 
</template>
<script>
export default {
  name: "MdPagination",
  props: {
    pager: {
      type: [Object, Boolean]
    },
    options: {
      type: [Array, Boolean],
      default: function() {
        return [5, 10, 20, 50];
      }
    }
  },
  data() {
    return {
      showPrevMore: false,
      showNextMore: false,
      select_start: 1,
      select_end: 1,
      select_page: 1,
      select_size: 0
    };
  },
  watch: {
    currentSize(val) {
      this.select_size = this.currentSize;
    },
    currentPage() {
      this.select_page = this.currentPage;
    }
  },
  computed: {
    optionsList() {
      if (!this.options) return false;
      if (this.currentSize > 0 && this.options.indexOf(this.currentSize) < 0) {
        this.options.push(this.currentSize);
      }
      return this.options;
    },
    currentSize() {
      if (this.pager && this.pager.size) return parseInt(this.pager.size, 10);
      if (this.pager && this.options && this.options.length > 0)
        return this.options[0];
      return 0;
    },
    currentPage() {
      if (this.pager && this.pager.page) return parseInt(this.pager.page, 10);
      return 1;
    },
    lastPage() {
      if (this.pager && this.pager.lastPage)
        return parseInt(this.pager.lastPage, 10);
      if (this.pager && this.pager.size && this.pager.total)
        return Math.ceil(this.pager.total / this.pager.size);
      return 1;
    },
    pageList() {
      const array = [];
      const offset = {
        start: this.currentPage - 4,
        end: this.currentPage + 4
      };
      if (offset.start < 1) {
        offset.end = offset.end + (1 - offset.start);
        offset.start = 1;
      }
      if (offset.end > this.lastPage) {
        offset.start = offset.start - (offset.end - this.lastPage);
        offset.end = this.lastPage;
      }
      if (offset.start < 1) offset.start = 1;

      this.showPrevMore = offset.start > 1;
      this.showNextMore = offset.end < this.lastPage;
      this.select_start = offset.start;
      this.select_end = offset.end;
      for (let i = offset.start; i <= offset.end; i++) {
        array.push(i);
      }
      return array;
    }
  },
  methods: {
    emitPaginationEvent() {
      if (!this.canFireEvents) return;
      if (
        this.select_size == this.currentSize &&
        this.select_page == this.currentPage
      ) {
        return;
      }

      this.$emit("pagination", {
        size: this.select_size,
        page: this.select_page
      });
    },
    changeSize(size) {
      if (this.canFireEvents && this.select_size != size) {
        this.select_size = size;
        this.select_page = 1;
        this.emitPaginationEvent();
      }
    },
    previousPage() {
      if (this.canFireEvents) {
        this.select_page--;
        this.emitPaginationEvent();
      }
    },
    nextPage() {
      if (this.canFireEvents) {
        this.select_page++;
        this.emitPaginationEvent();
      }
    },
    goPage(page) {
      if (this.canFireEvents) {
        this.select_page = page;
        this.emitPaginationEvent();
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.select_page = this.currentPage;
      this.select_size = this.currentSize;
      this.canFireEvents = true;
    });
  }
};
</script>
<style lang="scss">
@import "~gmf/components/MdLayout/mixins";
.md-pagination {
  padding: 0px;
  margin: 0px;
  display: flex;
  list-style: none;
  min-height: 40px;
  .md-button {
    min-width: 15px;
    margin: 0px;
  }
  .md-pagination-item {
    padding: 0px 5px;
  }
  .md-pagination-select-button {
    padding-right: 10px;
    &:after {
      border: 5px solid transparent;
      border-top-color: #404040;
      content: "";
      display: inline-block;
      position: absolute;
      right: 3px;
      top: 47%;
      transition: border-color 0.1s;
    }
  }
}
</style>
