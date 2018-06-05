<template>
  <div class="md-scroll-load">
    <slot />
    <div v-show="loading" class="loading">
      <slot name="loading">
        <md-loading class="icon"/>
        <span class="text">{{ loadingText || $t('loadingTip') }}</span>
      </slot>
    </div>
  </div>
</template>

<script>
import scrollUtils from "gmf/core/utils/MdScroll";
import Touch from "gmf/core/mixins/MdTouch/MdTouch";
import { on, off } from 'gmf/core/utils/MdInteractionEvents';


export default {
  name: 'MdScrollLoad',
  model: {
    prop: 'loading'
  },
  props: {
    loading: Boolean,
    finished: Boolean,
    immediateCheck: {
      type: Boolean,
      default: true
    },
    offset: {
      type: Number,
      default: 300
    },
    loadingText: String
  },

  mounted() {
    this.scroller = scrollUtils.getScrollEventTarget(this.$el);
    this.handler(true);

    if (this.immediateCheck) {
      this.$nextTick(this.onScroll);
    }
  },

  destroyed() {
    this.handler(false);
  },

  activated() {
    this.handler(true);
  },

  deactivated() {
    this.handler(false);
  },

  watch: {
    loading() {
      this.$nextTick(this.onScroll);
    },

    finished() {
      this.$nextTick(this.onScroll);
    }
  },

  methods: {
    onScroll() {
      if (this.loading || this.finished) {
        return;
      }

      const el = this.$el;
      const { scroller } = this;
      const scrollerHeight = scrollUtils.getVisibleHeight(scroller);

      /* istanbul ignore next */
      if (!scrollerHeight || scrollUtils.getComputedStyle(el).display === 'none') {
        return;
      }

      const scrollTop = scrollUtils.getScrollTop(scroller);
      const targetBottom = scrollTop + scrollerHeight;

      let reachBottom = false;

      /* istanbul ignore next */
      if (el === scroller) {
        reachBottom = scroller.scrollHeight - targetBottom < this.offset;
      } else {
        const elBottom =
          scrollUtils.getElementTop(el) -
          scrollUtils.getElementTop(scroller) +
          scrollUtils.getVisibleHeight(el);
        reachBottom = elBottom - scrollerHeight < this.offset;
      }

      /* istanbul ignore else */
      if (reachBottom) {
        this.$emit('input', true);
        this.$emit('load');
      }
    },

    handler(bind) {
      /* istanbul ignore else */
      if (this.binded !== bind) {
        this.binded = bind;
        (bind ? on : off)(this.scroller, 'scroll', this.onScroll);
      }
    }
  }
};
</script>
<style lang="scss">
.md-scroll-load {
  >.loading {
    text-align: center;
    .text {
      display: inline-block;
      vertical-align: middle;
    }
    .icon {
      width: 16px;
      height: 16px;
      margin-right: 5px;
    }
    .text {
      font-size: 13px;
      color: #999;
      line-height: 50px;
    }
  }
}

</style>
