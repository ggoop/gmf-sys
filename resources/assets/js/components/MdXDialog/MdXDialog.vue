<template>
  <transition name="md-x-dialog-bounce">
    <div v-show="value" :class="b()">
      <div v-if="title" v-text="title" :class="b('header')" />
      <div :class="b('content')" class="md-1px">
        <slot>
          <div v-if="message" v-html="message" :class="b('message', { withtitle: title })" />
        </slot>
      </div>
      <div :class="b('footer', { 'buttons': showCancelButton && showConfirmButton })">
        <md-x-button
          v-show="showCancelButton"
          :loading="loading.cancel"
          size="large"
          :class="b('cancel')"
          @click="handleAction('cancel')"
        >
          {{ cancelButtonText || '取消' }}
        </md-x-button>
        <md-x-button
          v-show="showConfirmButton"
          size="large"
          :loading="loading.confirm"
          :class="[b('confirm'), { 'md-1px-l': showCancelButton && showConfirmButton }]"
          @click="handleAction('confirm')"
        >
          {{ confirmButtonText || '确认'}}
        </md-x-button>
      </div>
    </div>
  </transition>
</template>

<script>
import create from "gmf/core/MdComponent";
import Popup from "gmf/core/mixins/MdPopup";
export default new create({
  name: "MdXDialog",
  components: {},
  mixins: [Popup],
  props: {
    title: String,
    message: String,
    callback: Function,
    beforeClose: Function,
    confirmButtonText: String,
    cancelButtonText: String,
    showCancelButton: Boolean,
    showConfirmButton: {
      type: Boolean,
      default: true
    },
    overlay: {
      type: Boolean,
      default: true
    },
    closeOnClickOverlay: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      loading: {
        confirm: false,
        cancel: false
      }
    };
  },

  methods: {
    handleAction(action) {
      if (this.beforeClose) {
        this.loading[action] = true;
        this.beforeClose(action, state => {
          if (state !== false) {
            this.onClose(action);
          }
          this.loading[action] = false;
        });
      } else {
        this.onClose(action);
      }
    },

    onClose(action) {
      this.$emit("input", false);
      this.$emit(action);
      this.callback && this.callback(action);
    }
  }
});
</script>
<style lang="scss">
@import "~gmf/style/variables";

.md-x-dialog {
  position: fixed;
  top: 50%;
  left: 50%;
  width: 85%;
  font-size: 16px;
  overflow: hidden;
  transition: 0.2s;
  border-radius: 4px;
  background-color: $white;
  transform: translate3d(-50%, -50%, 0);

  &__header {
    padding: 15px 0 0;
    text-align: center;
  }

  &__content {
    &::after {
      border-bottom-width: 1px;
    }
  }

  &__message {
    line-height: 1.5;
    padding: 15px 20px;

    &--withtitle {
      color: $gray-dark;
      font-size: 14px;
    }
  }

  &__footer {
    overflow: hidden;
    user-select: none;

    &--buttons {
      display: flex;

      .van-button {
        flex: 1;
      }
    }
  }

  .md-x-button {
    border: 0;
  }

  &__confirm {
    &,
    &:active {
      color: #00c000;
    }
  }

  &-bounce-enter {
    opacity: 0;
    transform: translate3d(-50%, -50%, 0) scale(0.7);
  }

  &-bounce-leave-active {
    opacity: 0;
    transform: translate3d(-50%, -50%, 0) scale(0.9);
  }
}
</style>
