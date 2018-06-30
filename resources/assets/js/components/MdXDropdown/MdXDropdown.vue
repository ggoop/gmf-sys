<template>
  <div :class="[b(),{'md-active':isActived}]">
    <div :class="b('title')" @click="onClick()" ref="title">
      <slot name="title">
        <span class="md-ellipsis">{{isObj(title)?title.name:title }}</span>
        <md-x-icon class="right-icon" name="arrow" />
      </slot>
    </div>
    <div :class="b('container')" v-show="isActived" :style="styles">
      <div :class="b('wrap')">
        <slot></slot>
      </div>
      <div :class="b('overlay')" @click="isActived=false"></div>
    </div>
  </div>
</template>

<script>
  import create from "gmf/core/MdComponent";
  import context from "./context";
  import manager from "./manager";
  export default new create({
    name: "MdXDropdown",
    props: {
      title: [String, Object],
      disabled: Boolean
    },
    data() {
      return {
        isActived: false,
        styles: {}
      };
    },
    watch: {
      isActived(val) {
        this[val ? "open" : "close"]();
      }
    },
    created() {
      this._popupId = "md-x-dropdown-" + context.plusKey("id");
    },
    methods: {
      onClick() {
        this.isActived = !this.isActived;
      },
      open() {
        this.setStyles();
        manager.open(this);
      },
      close() {
        manager.close(this._popupId);
      },
      getTop(e) {  
        var offset = e.offsetTop;  
        if (e.offsetParent != null) offset += this.getTop(e.offsetParent);  
        return offset;
      },
      setStyles() {
        this.styles.top = (this.$refs.title.offsetHeight + this.getTop(this.$refs.title)) + 'px';
      }
    },
    mounted() {
      this.setStyles();
    },
  });
</script>
<style lang="scss">
  @import "~gmf/style/variables";
  $line-height: 30px;
  .md-x-dropdown {
    flex: 1;
    box-sizing: border-box;
    &__title {
      line-height: $line-height;
      flex: 1;
      cursor: pointer;
      text-align: center;
      box-sizing: border-box;
    }
    &__container {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      top: 32px;
      z-index: 100;
    }
    &__overlay {
      background-color: rgba(0, 0, 0, 0.7);
      width: 100%;
      height: 100%;
    }
    &__wrap {
      background: #fff;
    }
    .right-icon {
      line-height: $line-height;
      margin: 0px;
      font-size: 12px;
      min-width: 0px;
      width: auto;
      margin-left: 5px;
    }
    .right-icon::before {
      transition: 0.3s;
      transform: rotate(90deg);
    }
    &.md-active {
      .md-x-dropdown__title {
        color: $red;
      }
      .right-icon::before {
        transform: rotate(-90deg);
      }
    }
  }
</style>