<template>
  <div :class="b('pane')" v-show="isSelected">
    <slot v-if="inited" />
    <div v-if="$slots.title" ref="title">
      <slot name="title" />
    </div>
  </div>
</template>

<script>
  import create from 'gmf/core/MdComponent';
  import findParent from 'gmf/core/mixins/MdFindParent';

  export default new create({
    name: 'MdXTab',
    mixins: [findParent],
    props: {
      title: [String,Object],
      disabled: Boolean
    },

    data() {
      return {
        inited: false
      };
    },

    computed: {
      index() {
        return this.parent.tabs.indexOf(this);
      },

      isSelected() {
        return this.index === this.parent.curActive;
      }
    },

    watch: {
      'parent.curActive' () {
        this.inited = this.inited || this.isSelected;
      }
    },

    created() {
      this.findParent('md-x-tabs');
      this.parent.tabs.push(this);
    },

    mounted() {
      if (this.$slots.title) {
        this.parent.renderTitle(this.$refs.title, this.index);
      }
    },

    destroyed() {
      this.parent.tabs.splice(this.index, 1);
    }
  });
</script>
<style lang="scss">
  @import '~gmf/style/variables';
  $van-tabs-line-height: 44px;
  $van-tabs-card-height: 30px;

  .md-x-tabs {
    position: relative;

    &__wrap {
      top: 0;
      left: 0;
      right: 0;
      z-index: 99;
      overflow: hidden;
      position: absolute;

      &--page-top {
        position: fixed;
      }

      &--content-bottom {
        top: auto;
        bottom: 0;
      }

      &--scrollable {
        .md-x-tab {
          flex: 0 0 22%;
        }

        .md-x-tabs__nav {
          overflow: hidden;
          overflow-x: auto;
          -webkit-overflow-scrolling: touch;

          &::-webkit-scrollbar {
            display: none;
          }
        }
      }
    }

    &__nav {
      display: flex;
      user-select: none;
      position: relative;
      background-color: $white;

      &--line {
        height: 100%;
        padding-bottom: 15px;
        /* 15px padding to hide scrollbar in mobile safari */
        box-sizing: content-box;
      }

      &--card {
        margin: 0 15px;
        border-radius: 2px;
        box-sizing: border-box;
        height: $van-tabs-card-height;
        border: 1px solid $gray-darker;

        .md-x-tab {
          color: $gray-darker;
          border-right: 1px solid $gray-darker;
          line-height: calc($van-tabs-card-height - 2px);

          &:last-child {
            border-right: none;
          }

          &.md-x-tab--active {
            color: $white;
            background-color: $gray-darker;
          }
        }
      }
    }

    &__line {
      z-index: 1;
      left: 0;
      bottom: 15px;
      height: 2px;
      position: absolute;
      background-color: $red;
    }

    &--line {
      padding-top: $van-tabs-line-height;

      .md-x-tabs__wrap {
        height: $van-tabs-line-height;
      }
    }

    &--card {
      padding-top: $van-tabs-card-height;

      .md-x-tabs__wrap {
        height: $van-tabs-card-height;
      }
    }
  }

  .md-x-tab {
    flex: 1;
    cursor: pointer;
    padding: 0 5px;
    font-size: 14px;
    position: relative;
    color: $text-color;
    line-height: $van-tabs-line-height;
    text-align: center;
    box-sizing: border-box;
    background-color: $white;
    min-width: 0;
    /* hack for flex ellipsis */
    span {
      display: block;
    }

    &:active {
      background-color: $active-color;
    }

    &--active {
      color: $red;
    }

    &--disabled {
      color: $gray;

      &:active {
        background-color: $white;
      }
    }
  }
</style>