<template>
  <md-x-cell
    :icon="leftIcon"
    :title="label"
    :center="center"
    :border="border"
    :required="required"
    :class="b({
      error,
      disabled: $attrs.disabled,
      'has-icon': hasIcon,
      'min-height': type === 'textarea' && !autosize
    })"
  >
    <slot name="label" slot="title" />
    <textarea
      v-if="type === 'textarea'"
      v-bind="$attrs"
      v-on="listeners"
      ref="textarea"
      :class="b('control')"
      :value="value"
    />
    <input
      v-else
      v-bind="$attrs"
      v-on="listeners"
      :class="b('control')"
      :type="type"
      :value="value"
    >
    <div
      v-if="errorMessage"
      v-text="errorMessage"
      :class="b('error-message')"
    />
    <div
      v-if="hasIcon"
      v-show="$slots.icon || value"
      :class="b('icon')"
      @touchstart.prevent="onClickIcon"
    >
      <slot name="icon">
        <md-x-icon :name="icon" />
      </slot>
    </div>
    <div v-if="$slots.button" :class="b('button')" slot="extra">
      <slot name="button" />
    </div>
  </md-x-cell>
</template>

<script>
import create from "gmf/core/MdComponent";

export default new create({
  name: "MdXField",
  inheritAttrs: false,
  props: {
    value: null,
    icon: String,
    label: String,
    error: Boolean,
    center: Boolean,
    leftIcon: String,
    required: Boolean,
    onIconClick: Function,
    autosize: [Boolean, Object],
    errorMessage: String,
    type: {
      type: String,
      default: "text"
    },
    border: {
      type: Boolean,
      default: true
    }
  },

  watch: {
    value() {
      this.$nextTick(this.adjustSize);
    }
  },

  mounted() {
    this.$nextTick(this.adjustSize);
  },

  computed: {
    hasIcon() {
      return this.$slots.icon || this.icon;
    },

    listeners() {
      return {
        ...this.$listeners,
        input: this.onInput,
        keypress: this.onKeypress
      };
    }
  },

  methods: {
    onInput(event) {
      this.$emit("input", event.target.value);
    },

    onClickIcon() {
      this.$emit("click-icon");
      this.onIconClick && this.onIconClick();
    },

    onKeypress(event) {
      if (this.type === "number") {
        const { keyCode } = event;
        const allowPoint = this.value.indexOf(".") === -1;
        const isValidKey =
          (keyCode >= 48 && keyCode <= 57) ||
          (keyCode === 46 && allowPoint) ||
          keyCode === 45;
        if (!isValidKey) {
          event.preventDefault();
        }
      }
      this.$emit("keypress", event);
    },

    adjustSize() {
      if (!(this.type === "textarea" && this.autosize)) {
        return;
      }

      const el = this.$refs.textarea;
      /* istanbul ignore if */
      if (!el) {
        return;
      }

      el.style.height = "auto";

      let height = el.scrollHeight;
      if (this.isObj(this.autosize)) {
        const { maxHeight, minHeight } = this.autosize;
        if (maxHeight) {
          height = Math.min(height, maxHeight);
        }
        if (minHeight) {
          height = Math.max(height, minHeight);
        }
      }

      if (height) {
        el.style.height = height + "px";
      }
    }
  }
});
</script>
<style lang="scss">
.md-x-field {
  .md-x-cell__title {
    max-width: 90px;
  }

  .md-x-cell__value {
    position: relative;
  }

  &__control {
    border: 0;
    margin: 0;
    padding: 0;
    line-height: 24px;
    display: block;
    width: 100%;
    resize: none;
    box-sizing: border-box;

    &:disabled {
      opacity: 1;
      color: #666;
      background-color: transparent;
    }
  }

  &__icon {
    position: absolute;
    right: 0;
    top: 50%;
    padding: 10px 0 10px 10px;
    transform: translate3d(0, -50%, 0);

    .md-x-icon {
      display: block;
    }
  }

  &__button {
    padding-left: 10px;
  }

  &__error-message {
    color: #f44;
    font-size: 12px;
    text-align: left;
  }

  &--disabled {
    .md-x-field__control {
      color: #666;
    }
  }

  &--error {
    .md-x-field__control {
      &,
      &::placeholder {
        color: #f44;
      }
    }
  }

  &--min-height {
    .md-x-field__control {
      min-height: 60px;
    }
  }

  &--has-icon {
    .md-x-field__control {
      padding-right: 20px;
    }
  }
}
</style>
