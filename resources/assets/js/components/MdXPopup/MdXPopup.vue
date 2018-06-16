<template>
  <transition :name="currentTransition">
    <div v-if="inited || !lazyRender" v-show="value" :class="b({ full:mdFull,[position]: position })">
      <slot />
    </div>
  </transition>
</template>

<script>
  import create from 'gmf/core/MdComponent';
  import Popup from 'gmf/core/mixins/MdPopup';

  export default new create({
    name: 'MdXPopup',

    mixins: [Popup],

    props: {
      mdFull: Boolean,
      transition: String,
      lazyRender: {
        type: Boolean,
        default: true
      },
      overlay: {
        type: Boolean,
        default: true
      },
      closeOnClickOverlay: {
        type: Boolean,
        default: true
      },
      position: {
        type: String,
        default: ''
      }
    },

    data() {
      return {
        inited: this.value
      };
    },

    computed: {
      currentTransition() {
        return this.transition || (this.position === '' ? 'md-fade' : `md-x-popup-slide-${this.position}`);
      }
    },

    watch: {
      value() {
        this.inited = this.inited || this.value;
      }
    }
  });
</script>
<style lang="scss">
  @import '~gmf/style/variables';
  .md-x {
    &-modal {
      position: fixed;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background-color: rgba(0, 0, 0, 0.7);
    }

    &-overflow-hidden {
      overflow: hidden !important;
    }

    &-popup {
      position: fixed;
      background-color: $white;
      top: 50%;
      left: 50%;
      transform: translate3d(-50%, -50%, 0);
      transition: .2s ease-out;



      &--top {
        width: 100%;
        top: 0;
        right: auto;
        bottom: auto;
        left: 50%;
        transform: translate3d(-50%, 0, 0);
      }

      &--right {
        height: 100%;
        width: 70%;
        top: 50%;
        right: 0;
        bottom: auto;
        left: auto;
        transform: translate3d(0, -50%, 0);
      }

      &--bottom {
        width: 100%;
        top: auto;
        bottom: 0;
        right: auto;
        left: 50%;
        transform: translate3d(-50%, 0, 0);
      }

      &--left {
        top: 50%;
        right: auto;
        bottom: auto;
        left: 0;
        transform: translate3d(0, -50%, 0);
      }
      &--full {
        height: 100%;
        width: 100%;
      }
    }
  }

  .md-x-popup-slide-top-enter,
  .md-x-popup-slide-top-leave-active {
    transform: translate3d(-50%, -100%, 0);
  }

  .md-x-popup-slide-right-enter,
  .md-x-popup-slide-right-leave-active {
    transform: translate3d(100%, -50%, 0);
  }

  .md-x-popup-slide-bottom-enter,
  .md-x-popup-slide-bottom-leave-active {
    transform: translate3d(-50%, 100%, 0);
  }

  .md-x-popup-slide-left-enter,
  .md-x-popup-slide-left-leave-active {
    transform: translate3d(-100%, -50%, 0);
  }
</style>