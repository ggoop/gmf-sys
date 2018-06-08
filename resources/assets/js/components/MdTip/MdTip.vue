<template>
  <transition name="md-fade">
    <div v-show="value" :class="b([style, position])">
      <!-- text only -->
      <div v-if="style === 'text'">{{ message }}</div>
      <div v-if="style === 'html'" v-html="message" />

      <!-- with icon -->
      <template v-if="style === 'default'">
        <md-loading2 v-if="type === 'loading'" color="white" :type="loadingType" />
        <i v-else :class="b('icon')" class="md-icon md-icon-font">{{iconType}}</i>
        <div v-if="isDef(message)" :class="b('text')">{{ message }}</div>
      </template>
    </div>
  </transition>
</template>

<script>
  const STYLE_LIST = ['success', 'fail', 'loading'];

  import Popup from '../../core/mixins/MdPopup';

  import MdComponent from '../../core/MdComponent'
  export default new MdComponent({
    name: 'MdTip',
    props: {
      forbidClick: Boolean,
      message: [String, Number],
      type: {
        type: String,
        default: 'text'
      },
      loadingType: {
        type: String,
        default: 'circular'
      },
      position: {
        type: String,
        default: 'middle'
      },
      lockScroll: {
        type: Boolean,
        default: false
      }
    },
    mixins: [Popup],
    data() {
      return {
        clickable: false
      };
    },

    computed: {
      style() {
        return STYLE_LIST.indexOf(this.type) !== -1 ? 'default' : this.type;
      },
      iconType(){
        if(this.type=='success')return 'done';
        if(this.type=='fail')return 'done';
        return this.type
      }
    },

    mounted() {
      this.toggleClickale();
    },

    watch: {
      value() {
        this.toggleClickale();
      },

      forbidClick() {
        this.toggleClickale();
      }
    },

    methods: {
      toggleClickale() {
        const clickable = this.value && this.forbidClick;
        if (this.clickable !== clickable) {
          this.clickable = clickable;
          const action = clickable ? 'add' : 'remove';
          document.body.classList[action]('md-tip-unclickable');
        }
      }
    }
  });
</script>
<style lang="scss">
  .md-tip {
    position: fixed;
    top: 50%;
    left: 50%;
    display: flex;
    color: #fff;
    font-size: 12px;
    line-height: 1.2;
    border-radius: 5px;
    word-break: break-all;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    box-sizing: border-box;
    transform: translate3d(-50%, -50%, 0);
    background-color: rgba(0, 0, 0, .7);

    &--unclickable {
      pointer-events: none;
    }

    &--text {
      padding: 12px;
      min-width: 220px;
    }

    &--default {
      width: 120px;
      min-height: 120px;
      padding: 15px;

      .md-tip__icon {
        font-size: 50px;
      }

      .md-tip__loading {
        margin: 10px 0 5px;
      }

      .md-tip__text {
        font-size: 14px;
        padding-top: 10px;
      }
    }

    &--top {
      top: 50px;
    }

    &--bottom {
      top: auto;
      bottom: 50px;
    }
  }
</style>