<template>
  <md-portal>
    <transition :name="`md-popup-animate-${mdPosition}`">
      <div class="md-popup" :class="[`md-position-${mdPosition}`, $mdActiveTheme]" v-on="$listeners" @keydown.esc="onEsc" v-if="mdActive">
        <md-focus-trap>
          <div class="md-popup-container">
            <slot />
            <keep-alive>
              <md-overlay :class="mdBackdropClass" md-fixed :md-active="mdActive" @click="onClick" v-if="mdBackdrop" />
            </keep-alive>
          </div>
        </md-focus-trap>
      </div>
    </transition>
  </md-portal>
</template>
<script>
import MdComponent from "gmf/core/MdComponent";
import MdPortal from "gmf/components/MdPortal/MdPortal";
import MdOverlay from "gmf/components/MdOverlay/MdOverlay";
import MdFocusTrap from "gmf/components/MdFocusTrap/MdFocusTrap";

export default new MdComponent({
  name: "MdPopup",
  components: {
    MdPortal,
    MdOverlay,
    MdFocusTrap
  },
  props: {
    mdActive: Boolean,
    mdBackdrop: {
      type: Boolean,
      default: true
    },
    mdBackdropClass: {
      type: String,
      default: "md-popup-overlay"
    },
    mdCloseOnEsc: {
      type: Boolean,
      default: true
    },
    mdClickOutsideToClose: {
      type: Boolean,
      default: true
    },
    mdFullscreen: {
      type: Boolean,
      default: true
    },
    mdPosition: {
      type: String,
      default: 'bottom'
    }
  },
  computed: {
  },
  watch: {
    mdActive(isActive) {
      this.$nextTick().then(() => {
        if (isActive) {
          this.$emit("md-opened");
        } else {
          this.$emit("md-closed");
        }
      });
    }
  },
  methods: {
    closeDialog() {
      this.$emit("update:mdActive", false);
    },
    onClick() {
      if (this.mdClickOutsideToClose) {
        this.closeDialog();
      }
    },
    onEsc() {
      if (this.mdCloseOnEsc) {
        this.closeDialog();
      }
    }
  }
});

</script>
<style lang="scss">
$popup-background-color:#eee !default;

.md-popup {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
  background: $popup-background-color;
  z-index: 501;
  transition-property: transform;
  transition-duration: 300ms;
  max-height: 100%;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;

  &.md-position-left {
    width: auto;
    height: 100%;
    top: 0;
    right: auto;
    bottom: auto;
    left: 0;
  }
  &.md-position-right {
    width: auto;
    height: 100%;
    top: 0;
    right: 0;
    bottom: auto;
    left: auto;
  }
  &.md-position-top {
    width: 100%;
    top: 0;
    right: auto;
    bottom: auto;
    left: 0;
  }
}

.md-popup-animate-bottom-enter,
.md-popup-animate-bottom-leave-active {
  transform: translate3d(0, 100%, 0);
}

.md-popup-animate-left-enter,
.md-popup-animate-left-leave-active {
  transform: translate3d(-100%, 0, 0);
}

.md-popup-animate-right-enter,
.md-popup-animate-right-leave-active {
  transform: translate3d(100%, 0, 0);
}

.md-popup-animate-top-enter,
.md-popup-animate-top-leave-active {
  transform: translate3d(0, -100%, 0);
}

</style>
