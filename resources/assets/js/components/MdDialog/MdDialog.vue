<template>
  <md-portal>
    <transition name="md-dialog">
      <div class="md-dialog" :class="[dialogClasses, $mdActiveTheme]" v-on="$listeners" @keydown.esc="onEsc" v-if="mdActive">
        <md-focus-trap>
          <div class="md-dialog-container">
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
  name: "MdDialog",
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
      default: "md-dialog-overlay"
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
      default: ''
    },
    mdAnimateFromSource: Boolean
  },
  computed: {
    dialogClasses() {
      const s= {
        "md-dialog-fullscreen": this.mdFullscreen
      };
      if(this.mdPosition){
        s['md-position-'+this.mdPosition]=true;
      }
      return s;
    }
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
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
@import "~gmf/components/MdElevation/mixins";

.md-dialog {
  @include md-elevation(24);
  min-width: 280px;
  max-width: 80%;
  max-height: 80%;
  margin: auto;
  display: flex;
  flex-flow: column;
  overflow: hidden;
  position: fixed;
  top: 50%;
  left: 50%;
  z-index: 60;
  border-radius: 2px;
  backface-visibility: hidden;
  pointer-events: auto;
  transform: translate(-50%, -50%);
  transform-origin: center center;
  transition: opacity 0.15s $md-transition-stand-timing,
    transform 0.2s $md-transition-stand-timing;
  will-change: opacity, transform, left, top;

  > .md-dialog-tabs,
  > .md-dialog-title,
  > .md-dialog-content,
  > .md-dialog-actions {
    transition: opacity 0.3s $md-transition-default-timing,
      transform 0.25s $md-transition-default-timing;
    will-change: opacity, transform;
  }
  &.md-position-top {
     max-width:100%;
      width: 100%;
      top: 0;
      right: auto;
      bottom: auto;
      left: 50%;
      transform: translate3d(-50%, 0, 0);
    }
   &.md-position-right {
     max-height:100%;
      top: 50%;
      right: 0;
      bottom: auto;
      left: auto;
      transform: translate3d(0, -50%, 0);
    }

   &.md-position-bottom {
     max-width:100%;
      width: 100%;
      top: auto;
      bottom: 0;
      right: auto;
      left: 50%;
      transform: translate3d(-50%, 0, 0);
    }

   &.md-position-left {
     max-height:100%;
      top: 50%;
      right: auto;
      bottom: auto;
      left: 0;
      transform: translate3d(0, -50%, 0);
    }
}

.md-dialog-enter-active,
.md-dialog-leave-active {
  opacity: 0;
  transform: translate(-50%, -50%) scale(0.9);

  > .md-dialog-tabs,
  > .md-dialog-title,
  > .md-dialog-content,
  > .md-dialog-actions {
    opacity: 0;
    transform: scale(0.95) translate3D(0, 10%, 0);
  }
}

.md-dialog-container {
  display: flex;
  flex-flow: column;
  flex: 1;

  .md-tabs {
    flex: 1;
  }

  .md-tabs-navigation {
    padding: 0 12px;
  }

  .md-tab {
    @include md-layout-xsmall {
      padding: 12px;
    }
  }
}

.md-dialog-fullscreen {
  @include md-layout-xsmall {
    max-width: 100%;
    max-height: 100%;
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    border-radius: 0;
    transform: none;

    &.md-dialog-enter {
      opacity: 0;
      transform: translate3D(0, 30%, 0);
    }

    &.md-dialog-leave-active {
      opacity: 0;
      transform: translate3D(0, 0, 0);
    }
  }
}
</style>
