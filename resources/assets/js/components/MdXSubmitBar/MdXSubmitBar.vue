<template>
  <div :class="b()">
    <div :class="b('tip')" v-if="tip || $slots.tip">
      {{ tip }}<slot name="tip" />
    </div>
    <div :class="b('bar')">
      <slot />
      <div :class="b('price')">
        <template v-if="hasPrice">
          <span>{{ label }}</span>
          <span :class="b('price-integer')">{{ currency }}{{ priceInterger }}.</span>
          <span :class="b('price-decimal')">{{ priceDecimal }}</span>
        </template>
      </div>
      <slot name="button">
        <md-x-button :type="buttonType" :disabled="disabled" :loading="loading" @click="$emit('submit')">
          {{ loading ? '' : buttonText }}
        </md-x-button>
      </slot>
    </div>
  </div>
</template>

<script>
import create from "gmf/core/MdComponent";

export default new create({
  name: "MdXSubmitBar",
  props: {
    tip: String,
    type: Number,
    price: Number,
    label: {
      type: String,
      default: "合计"
    },
    loading: Boolean,
    disabled: Boolean,
    buttonText: {
      type: String,
      default: "保存"
    },
    currency: {
      type: String,
      default: "¥"
    },
    buttonType: {
      type: String,
      default: "danger"
    }
  },

  computed: {
    hasPrice() {
      return typeof this.price === "number";
    },
    priceInterger() {
      return Math.floor(this.price / 100);
    },
    priceDecimal() {
      const decimal = Math.floor(this.price % 100);
      return (decimal < 10 ? "0" : "") + decimal;
    }
  }
});
</script>

<style lang="scss">
@import "~gmf/style/variables";
.md-x-submit-bar {
  left: 0;
  bottom: 0;
  width: 100%;
  z-index: 100;
  position: fixed;
  user-select: none;

  &__tip {
    color: $orange;
    font-size: 12px;
    line-height: 18px;
    padding: 10px 10px;
    background-color: #fff7cc;
  }

  &__bar {
    height: 50px;
    display: flex;
    font-size: 16px;
    align-items: center;
    background-color: $white;
  }

  &__price {
    flex: 1;
    text-align: right;
    color: $gray-darker;
    padding-right: 12px;

    span {
      display: inline-block;
    }
  }

  &__price-integer {
    color: $red;
  }

  &__price-decimal {
    color: $red;
    font-size: 12px;
  }

  .md-x-button {
    width: 110px;
    height: 100%;
    border-radius: 0;
    font-size: 16px;

    &--disabled {
      border: none;
    }
  }
}
</style>
