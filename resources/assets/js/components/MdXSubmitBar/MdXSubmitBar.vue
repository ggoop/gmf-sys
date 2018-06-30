<template>
  <div :class="b()">
    <div :class="b('tip')" v-if="tip || $slots.tip">
      {{ tip }}
      <slot name="tip" />
    </div>
    <div :class="b('bar')">
      <md-x-icon v-if="showBack" :class="b('back')" name="md:arrow_back" @click="$emit('back')"></md-x-icon>
      <slot />
      <div :class="b('price')">
        <template v-if="hasPrice">
          <span>{{ label }}</span>
          <span :class="b('price-integer')">{{ currency }}{{ priceInterger }}.</span>
          <span :class="b('price-decimal')">{{ priceDecimal }}</span>
        </template>
      </div>
      <slot name="speed" v-if="speed || $slots.speed">
        <md-x-icon @click="$emit('submit')" :class="b('speed')" class="md-elevation-3" :name="speed"></md-x-icon>
      </slot>
      <slot v-else-if="loading||buttonText" name="button">
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
      showBack: Boolean,
      showBack: Boolean,
      label: {
        type: String,
        default: "合计"
      },
      speed: String,
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
    box-shadow: 1px 2px 1px 2px rgba(191, 189, 189, 0.91);

    &__tip {
      color: $orange;
      font-size: 12px;
      line-height: 18px;
      padding: 10px 10px;
      background-color: #fff7cc;
    }
    &__back {
      margin: 0px 8px;
      color: #615d5d;
    }
    &__speed {
      width: 46px;
      height: 46px;
      position: absolute;
      right: 15px;
      bottom: 15px;
      text-align: center;
      line-height: 46px;
      z-index: 10;
      border-radius: 50%;
      background-color: $red;
      color: $white;
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