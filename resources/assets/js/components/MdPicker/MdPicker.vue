<template>
  <div class="md-picker layout-column" :class="[$mdActiveTheme,'md-toolbar-position-'+mdToolbarPosition]">
    <div :class="b('toolbar')" class="md-1px-tb" v-if="mdShowToolbar">
      <slot>
        <div :class="b('cancel')" @click="onCancel">{{ mdCancelText }}</div>
        <div :class="b('title')" class="md-ellipsis" v-if="mdTitle" v-text="title" />
        <div :class="b('confirm')" @click="onConfirm">{{ mdConfirmText }}</div>
      </slot>
    </div>
    <div v-if="loading" :class="b('loading')">
      <md-x-loading />
    </div>
    <md-flexbox :gutter="0" :class="b('container')">
      <md-flexbox-item :span="mdColumnWidth && mdColumnWidth[index]" v-for="(one, index) in currentData" :key="index" style="margin-left:0;">
        <div class="md-picker-item" :id="`md-picker-${uuid}-${index}`"></div>
      </md-flexbox-item>
    </md-flexbox>
  </div>
</template>
<script>
  import Scroller from './scroller'
  import {
    MdFlexbox,
    MdFlexboxItem
  } from '../MdFlexbox'
  import Manager from './chain'
  import value2name from 'gmf/core/utils/MdValue2Name'
  import isArray from 'gmf/core/utils/MdIsArray'
  import MdComponent from 'gmf/core/MdComponent'
  export default new MdComponent({
    name: 'MdPicker',
    components: {
      MdFlexbox,
      MdFlexboxItem
    },
    created() {
      this.mapDataToRows();
      if (this.mdColumns !== 0) {
        const length = this.mdColumns
        this.store = new Manager(this.currentData, length, this.mdFixedColumns || this.mdColumns)
        this.currentData = this.store.getColumns(this.value)
      }
    },
    mounted() {
      this.uuid = Math.random().toString(36).substring(3, 8)
      this.$nextTick(() => {
        this.render(this.currentData, this.currentValue)
      })
    },
    props: {
      mdData: {
        default: () => [],
        type: [Array, Function]
      },
      mdToolbarPosition: {
        type: String,
        default: 'top'
      },
      mdColumns: {
        type: Number,
        default: 0
      },
      mdFixedColumns: {
        type: Number,
        default: 0
      },
      value: {
        default: () => [],
        type: [Array]
      },
      mdItemClass: {
        type: String,
        default: 'scroller-item'
      },
      mdColumnWidth: Array,
      mdShowToolbar: Boolean,
      mdConfirmText: {
        type: String,
        default: '确定'
      },
      mdCancelText: {
        type: String,
        default: '取消'
      },
      mdTitle: String
    },
    data() {
      return {
        loading: false,
        scroller: [],
        count: 0,
        uuid: '',
        currentData: [],
        currentValue: this.value
      }
    },
    computed: {
      usesLocalData() {
        return isArray(this.mdData);
      }
    },
    watch: {
      value(val) {
        if (JSON.stringify(val) !== JSON.stringify(this.currentValue)) {
          this.currentValue = val
        }
      },
      currentValue(val, oldVal) {
        this.$emit('input', val)
        // render all the scroller for chain datas
        if (this.mdColumns !== 0) {
          if (val.length > 0) {
            if (JSON.stringify(val) !== JSON.stringify(oldVal)) {
              this.currentData = this.store.getColumns(val)
              this.$nextTick(function () {
                this.render(this.currentData, val)
              })
            }
          }
        } else {
          if (val.length) {
            for (let i = 0; i < val.length; i++) {
              if (this.scroller[i] && this.scroller[i].value !== val[i]) {
                this.scroller[i].select(val[i])
              }
            }
          } else {
            this.render(this.currentData, [])
          }
        }
      },
      mdData(val) {
        if (this.usesLocalData) {
          this.mapDataToRows();
        }
      },
      currentData(newData) {
        if (Object.prototype.toString.call(newData[0]) === '[object Array]') {
          this.$nextTick(() => {
            this.render(newData, this.currentValue)
            // emit on-change after rerender
            this.$nextTick(() => {
              this.emitValueChange(this.getValue())

              if (JSON.stringify(this.getValue()) !== JSON.stringify(this.currentValue)) {
                if (!this.mdColumns || (this.mdColumns && this.getValue().length === this.store.count)) {
                  this.currentValue = this.getValue()
                }
              }
            })
          })
        } else {
          if (this.mdColumns !== 0) {
            if (!newData.length) {
              return
            }
            const length = this.mdColumns
            this.store = new Manager(newData, length, this.mdFixedColumns || this.mdColumns)
            this.currentData = this.store.getColumns(this.currentValue)
          }
        }
      }
    },
    methods: {
      onConfirm() {
        this.$emit('md-confirm', this.getValue());
      },
      onCancel() {
        this.$emit('md-cancel');
      },
      fetchLocalData() {
        return this.mdData.filter(r => r);
      },
      async fetchServerData() {
        this.loading = true;
        const response = await this.mdData();
        this.loading = false;
        if (isArray(response)) {
          return response;
        }
        return response.data.data;
      },
      async mapDataToRows() {
        const val = this.usesLocalData ? this.fetchLocalData() : await this.fetchServerData();
        if (JSON.stringify(val) !== JSON.stringify(this.currentData)) {
          this.currentData = val
        }
      },
      getNameValues() {
        return value2name(this.currentValue, this.currentData)
      },
      getId(i) {
        return `#md-picker-${this.uuid}-${i}`
      },
      render(data, value) {
        this.count = this.currentData.length
        const _this = this
        if (!data || !data.length) {
          return
        }
        let count = this.currentData.length
        // set first item as value
        if (value.length < count) {
          for (let i = 0; i < count; i++) {
            if (process.env.NODE_ENV === 'development' &&
              typeof data[i][0] === 'undefined' &&
              isArray(this.currentData) &&
              this.currentData[0] &&
              typeof this.currentData[0].value !== 'undefined' &&
              !this.mdColumns) {
              console.error('[VUX error] 渲染出错，如果为联动模式，需要指定 mdColumns(列数)')
            }
            this.$set(_this.currentValue, i, data[i][0].value || data[i][0])
          }
        }

        for (let i = 0; i < data.length; i++) {
          /**
           * Still don't know why this happens
           */
          if (!document.querySelector(_this.getId(i))) {
            return
          }

          _this.scroller[i] && _this.scroller[i].destroy()
          _this.scroller[i] = new Scroller(_this.getId(i), {
            data: data[i],
            defaultValue: value[i] || data[i][0].value,
            itemClass: _this.mdItemClass,
            onSelect(value) {
              _this.$set(_this.currentValue, i, value)
              if (!this.mdColumns || (this.mdColumns && _this.getValue().length === _this.store.count)) {
                _this.$nextTick(() => {
                  _this.$emit('md-change', _this.getValue())
                })
              }
              if (_this.mdColumns !== 0) {
                _this.renderChain(i + 1)
              }
            }
          })
          if (_this.currentValue) {
            _this.scroller[i].select(value[i])
          }
        }
      },
      renderChain(i) {
        if (!this.mdColumns) {
          return
        }

        // do not render for last scroller
        if (i > this.count - 1) {
          return
        }

        const _this = this
        let ID = this.getId(i)
        // destroy old one
        this.scroller[i].destroy()
        let list = this.store.getChildren(_this.getValue()[i - 1])
        this.scroller[i] = new Scroller(ID, {
          data: list,
          itemClass: _this.mdItemClass,
          onSelect(value) {
            _this.$set(_this.currentValue, i, value)
            _this.$nextTick(() => {
              _this.$emit('md-change', _this.getValue())
            })
            _this.renderChain(i + 1)
          }
        })
        // list is Array(empty) as maybe
        if (list.length) {
          this.$set(this.currentValue, i, list[0].value)
          this.renderChain(i + 1)
        } else {
          this.$set(this.currentValue, i, null)
        }
      },
      getValue() {
        let data = []
        for (let i = 0; i < this.currentData.length; i++) {
          if (this.scroller[i]) {
            data.push(this.scroller[i].value)
          } else {
            return []
          }
        }
        return data
      },
      emitValueChange(val) {
        if (!this.mdColumns || (this.mdColumns && val.length === this.store.count)) {
          this.$emit('md-change', val)
        }
      }
    },

    beforeDestroy() {
      for (let i = 0; i < this.count; i++) {
        this.scroller[i] && this.scroller[i].destroy()
        this.scroller[i] = null
      }
    }
  });
</script>
<style lang="scss">
  @import "~gmf/components/MdAnimation/variables";
  @import '~gmf/style/variables';

  .md-picker {
    &__toolbar {
      display: flex;
      height: 40px;
      line-height: 40px;
      justify-content: space-between;
    }
    &__container {}

    &__cancel,
    &__confirm {
      color: $blue;
      padding: 0 15px;

      &:active {
        background-color: $active-color;
      }
    }
    &__loading {
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 2;
      position: absolute;
      background-color: rgba(255, 255, 255, .9);

      .md-x-loading {
        top: 50%;
        left: 0;
        width: 100%;
        z-index: 1;
        position: absolute;
        pointer-events: none;
        transform: translateY(-50%);
        circle {
          stroke: $blue;
        }
      }
    }


    &__title {
      max-width: 50%;
      text-align: center;
    }

    .scroller-component {
      display: block;
      position: relative;
      height: 238px;
      overflow: hidden;
      width: 100%;
    }

    .scroller-content {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      z-index: 1;
    }

    .scroller-mask {
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      margin: 0 auto;
      width: 100%;
      z-index: 3;
      transform: translateZ(0px);
      background-image: -webkit-linear-gradient(top, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.6)),
      -webkit-linear-gradient(bottom, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.6));
      background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.6)),
      linear-gradient(to top, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.6));
      background-position: top, bottom;
      background-size: 100% 102px;
      background-repeat: no-repeat;
    }

    .scroller-item {
      text-align: center;
      font-size: 16px;
      height: 34px;
      line-height: 34px;
      color: #000;
    }

    .scroller-indicator {
      width: 100%;
      height: 34px;
      position: absolute;
      left: 0;
      top: 102px;
      z-index: 3;
      background-image: -webkit-linear-gradient(top, #d0d0d0, #d0d0d0, transparent, transparent),
      -webkit-linear-gradient(bottom, #d0d0d0, #d0d0d0, transparent, transparent);
      background-image: linear-gradient(to bottom, #d0d0d0, #d0d0d0, transparent, transparent),
      linear-gradient(to top, #d0d0d0, #d0d0d0, transparent, transparent);
      background-position: top, bottom;
      background-size: 100% 1px;
      background-repeat: no-repeat;
    }
    .scroller-item {
      line-clamp: 1;
      -webkit-line-clamp: 1;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    &.md-toolbar-position-bottom {
      .md-picker__toolbar {
        order: 1;
      }
    }
  }
</style>