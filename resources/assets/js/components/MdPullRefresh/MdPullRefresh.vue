<template>
  <div class="md-pull-refresh">
    <div class="md-pull-refresh-track"
      :style="style"
      @touchstart="onTouchStart"
      @touchmove="onTouchMove"
      @touchend="onTouchEnd"
      @touchcancel="onTouchEnd"
    >
      <div class="md-pull-refresh-header">
        <slot v-if="status === 'normal'" name="normal" />
        <slot v-if="status === 'pulling'" name="pulling">
          <span class="text">{{ mdPullingText || $t('pulling') }}</span>
        </slot>
        <slot v-if="status === 'loosing'" name="loosing">
          <span class="text">{{ mdLoosingText || $t('loosing') }}</span>
        </slot>
        <slot v-if="status === 'loading'" name="loading">
          <div class="md-pull-refresh-loading">
            <md-loading />
            <span class="text">{{ mdLoadingText || $t('loadingTip') }}</span>
          </div>
        </slot>
      </div>
      <slot />
    </div>
  </div>
</template>
<script>
import scrollUtils from "gmf/core/utils/MdScroll";
import Touch from "gmf/core/mixins/MdTouch/MdTouch";
import { on, off } from 'gmf/core/utils/MdInteractionEvents';

export default {
  name: "MdPullRefresh",
  mixins: [Touch],
  props: {
    mdPullingText: String,
    mdLoosingText: String,
    mdLoadingText: String,
    mdAnimationDuration: {
      type: Number,
      default: 300
    },
    mdHeadHeight: {
      type: Number,
      default: 50
    },
    value: {
      type: Boolean,
      required: true
    }
  },

  data() {
    return {
      status: "normal",
      height: 0,
      duration: 0
    };
  },
  computed: {
    style() {
      return {
        transition: `${this.duration}ms`,
        transform: `translate3d(0,${this.height}px, 0)`
      };
    }
  },

  mounted() {
    this.scrollEl = scrollUtils.getScrollEventTarget(this.$el);
  },

  watch: {
    value(val) {
      this.duration = this.mdAnimationDuration;
      this.getStatus(val ? this.mdHeadHeight : 0, val);
    }
  },
  methods: {
    onTouchStart(event) {
      if (this.status === "loading") {
        return;
      }
      if (this.getCeiling()) {
        this.duration = 0;
        this.touchStart(event);
      }
    },

    onTouchMove(event) {
      if (this.status === "loading") {
        return;
      }

      this.touchMove(event);

      if (!this.ceiling && this.getCeiling()) {
        this.duration = 0;
        this.startY = event.touches[0].clientY;
        this.deltaY = 0;
      }

      if (this.ceiling && this.deltaY >= 0) {
        if (this.direction === "vertical") {
          this.getStatus(this.ease(this.deltaY));
          event.preventDefault();
        }
      }
    },

    onTouchEnd() {
      if (this.status === "loading") {
        return;
      }

      if (this.ceiling && this.deltaY) {
        this.duration = this.mdAnimationDuration;
        if (this.status === "loosing") {
          this.getStatus(this.mdHeadHeight, true);
          this.$emit("input", true);
          this.$emit("refresh");
        } else {
          this.getStatus(0);
        }
      }
    },

    getCeiling() {
      this.ceiling = scrollUtils.getScrollTop(this.scrollEl) === 0;
      return this.ceiling;
    },

    ease(height) {
      const { mdHeadHeight } = this;
      return height < mdHeadHeight
        ? height
        : height < mdHeadHeight * 2
          ? Math.round(mdHeadHeight + (height - mdHeadHeight) / 2)
          : Math.round(mdHeadHeight * 1.5 + (height - mdHeadHeight * 2) / 4);
    },
    getStatus(height, isLoading) {
      this.height = height;

      const status = isLoading
        ? "loading"
        : height === 0
          ? "normal"
          : height < this.mdHeadHeight ? "pulling" : "loosing";

      if (status !== this.status) {
        this.status = status;
      }
    }
  }
};
</script>


<style lang="scss">
.md-pull-refresh {
  user-select: none;
  overflow: hidden;
  height: 100%;
  .md-pull-refresh-track {
    position: relative;
    height: 100%;
  }

  .md-pull-refresh-header {
    width: 100%;
    height: 50px;
    left: 0;
    overflow: hidden;
    position: absolute;
    text-align: center;
    top: -50px;
    font-size: 14px;
    color: #999;
    line-height: 50px;

    .md-pull-refresh-text {
      display: block;
    }
    .loading {
      width: 16px;
      height: 16px;
      margin-right: 5px;
    }
    span,
    .loading {
      vertical-align: middle;
      display: inline-block;
    }
  }
}
</style>
