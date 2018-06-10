<template>
  <div class="md-scroll-load">
    <slot />
    <div v-show="value" class="loading">
      <slot name="loading">
        <md-loading class="icon"/>
        <span class="text">{{ mdLoadingText || $t('loadingTip') }}</span>
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
  props: {
    mdFinished: Boolean,
    mdImmediateCheck: {
      type: Boolean,
      default: true
    },
    mdOffset: {
      type: Number,
      default: 300
    },
    mdLoadingText: String
  },
  data() {
    return {
      value:false
    };
  },
  mounted() {
    this.scroller = scrollUtils.getScrollEventTarget(this.$el);
    this.handler(true);

    if (this.mdImmediateCheck) {
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
    value() {
      this.$nextTick(this.onScroll);
    },

    mdFinished() {
      this.$nextTick(this.onScroll);
    }
  },

  methods: {
    loadFinish(){
      this.value=false;
    },
    onScroll() {
      if (this.value || this.mdFinished) {
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
        reachBottom = scroller.scrollHeight - targetBottom < this.mdOffset;
      } else {
        const elBottom =
          scrollUtils.getElementTop(el) -
          scrollUtils.getElementTop(scroller) +
          scrollUtils.getVisibleHeight(el);
        reachBottom = elBottom - scrollerHeight < this.mdOffset;
      }

      /* istanbul ignore else */
      if (reachBottom) {
        this.value=true;
        this.$emit('load',this.loadFinish);
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
  max-height: 100%;
  max-width: 100%;
  min-height: 100%;
  overflow: auto;
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
