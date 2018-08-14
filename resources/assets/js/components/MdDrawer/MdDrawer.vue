<template>
  <div class="md-drawer" :class="[$mdActiveTheme, drawerClasses]">
    <slot />
    <md-overlay :md-active="mdActive" @click="closeDrawer" v-if="mdFixed" />
    <md-overlay :md-active="mdActive" @click="closeDrawer" md-attach-to-parent v-else />
  </div>
</template>
<script>
import MdComponent from 'gmf/core/MdComponent'
import MdOverlay from 'gmf/components/MdOverlay/MdOverlay'
import MdPropValidator from 'gmf/core/utils/MdPropValidator'

export default new MdComponent({
  name: 'MdDrawer',
  components: {
    MdOverlay
  },
  props: {
    mdLeft: Boolean,
    mdRight: Boolean,
    mdClickOutsideToClose:{
      type:Boolean,
      default:true
    },
    mdPermanent: {
      type: String,
      ...MdPropValidator('md-permanent', [
        'full',
        'clipped',
        'card'
      ])
    },
    mdPersistent: {
      type: String,
      ...MdPropValidator('md-persistent', [
        'mini',
        'full'
      ])
    },
    mdActive: Boolean,
    mdFixed: Boolean
  },
  watch: {
    mdActive(visible) {
      if (visible) {
        this.$emit('md-opened')
      } else {
        this.$emit('md-closed')
      }
    }
  },
  computed: {
    drawerClasses() {
      let classes = {
        'md-left': this.mdLeft,
        'md-right': this.mdRight,
        'md-temporary': this.isTemporary,
        'md-persistent': this.mdPersistent,
        'md-permanent': this.mdPermanent,
        'md-active': this.mdActive,
        'md-fixed': this.mdFixed
      }

      if (this.mdPermanent) {
        classes['md-permanent-' + this.mdPermanent] = true
      }

      if (this.mdPersistent) {
        classes['md-persistent-' + this.mdPersistent] = true
      }

      return classes
    },
    isTemporary() {
      return !this.mdPermanent && !this.mdPersistent
    },
    mode() {
      if (this.mdPersistent) {
        return 'persistent'
      }

      if (this.mdPermanent) {
        return 'permanent'
      }

      return 'temporary'
    },
    submode() {
      if (this.mdPersistent) {
        return this.mdPersistent
      }

      if (this.mdPermanent) {
        return this.mdPermanent
      }
    }
  },
  methods: {
    closeDrawer() {
      this.mdClickOutsideToClose&&this.$emit('update:mdActive', false)
    }
  }
})

</script>
<style lang="scss">
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
@import "~gmf/components/MdElevation/mixins";
@import "~gmf/components/MdCard/base";

@mixin md-drawer-base () {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
}

@mixin md-drawer-temporary () {
  @include md-drawer-base;
  z-index: 30;
  transform: translate3D(-100%, 0, 0);
  transition: transform .4s $md-transition-stand-timing;
  will-change: transform, box-shadow;
}

@mixin md-drawer-temporary-active () {
  transform: translate3D(0, 0, 0);
  transition-timing-function: $md-transition-default-timing;

  @include md-layout-xsmall {
    @include md-elevation(16);
  }
}

.md-drawer {
  @include md-drawer-temporary;
  width: 280px;
  max-width: calc(100vw - 56px);
  @include md-layout-xsmall {
    width: 320px;
  }

  &.md-right {
    right: 0;
    left: auto;
    transform: translate3D(100%, 0, 0);
  }

  &.md-fixed {
    position: fixed;
  }

  &.md-active {
    @include md-drawer-temporary-active;
  }

  &:not(.md-temporary) {
    ~.md-overlay {
      @include md-layout-small-and-up {
        background: none;
        pointer-events: none;
      }
    }
  }

  &.md-temporary {
    +.md-app-container .md-content {
      border-left: none;
    }

    &.md-active {
      @include md-elevation(16);
    }
  }

  &.md-permanent {
    @include md-layout-small-and-up {
      position: relative;
      transform: translate3D(0, 0, 0);
    }
  }

  &.md-permanent-full {
    @include md-layout-small-and-up {
      z-index: 3;
      .md-list {
        padding-top: 0;
      }
    }
  }

  &.md-permanent-clipped,
  &.md-permanent-card {
    @include md-layout-small-and-up {
      z-index: 2;
    }
  }

  &.md-permanent-card {
    @include md-layout-small-and-up {
      @include md-card;
      margin: 8px;
      z-index: 2;
    }

    @include md-layout-medium-and-up {
      margin: 16px;
    }

    @include md-layout-large-and-up {
      margin: 24px;
    }
  }

  &.md-persistent {
    &:not(.md-active) {
      +.md-app-container .md-content {
        border-left: none;
      }
    }
  }

  &.md-persistent-mini {
    border-right: 1px solid;
    transform: translate3D(0, 64px, 0);
    transition: .3s $md-transition-stand-timing;
    transition-property: transform, width;
    will-change: transform, box-shadow;
    &.md-permanent-full {
      transform: translate3D(0, 0, 0);
    }
    &.md-active {
      +.md-app-container .md-content {
        border-left: none;
      }
    }

    &:not(.md-active) {
      width: 70px !important;
      z-index: 1;
      white-space: nowrap;

      @include md-layout-xsmall {
        width: 0!important;
      }

      .md-toolbar:not(.md-fixed) {
        display: none;
      }

      .md-list-item-content {
        padding: 0 23px;
      }
    }

    &.md-active {
      position: relative;
      transform: translate3D(0, 0, 0);
      white-space: normal;
    }
  }

  .md-list-item-container {
    font-size: 14px;
    text-transform: none;
  }
}

</style>
