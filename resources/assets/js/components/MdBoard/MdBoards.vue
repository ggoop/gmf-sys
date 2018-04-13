<template>
  <div class="md-boards" :class="[$mdActiveTheme,boardClasses]">
    <div class="md-boards-content flex" ref="boardsContent" :style="{minHeight:contentHeight}">
      <div class="md-boards-wrapper" :style="{ transform: `translate3D(-${contentWidth}, 0, 0)` }">
        <slot></slot>
      </div>
    </div>
    <div class="md-boards-arrows layout" v-if="mdControls">
      <md-button @click="movePrevBoard()" class="prev">
        <i class="md-icon">chevron_left</i>
      </md-button>
      <span class="flex"></span>
      <md-button @click="moveNextBoard()" class="next">
        <i class="md-icon">chevron_right</i>
      </md-button>
    </div>
    <nav class="md-boards-navigation" :class="navigationClasses" ref="boardNavigation">
      <span style="flex: 1"></span>
      <button v-for="header in boardList" :key="header.id" type="button" class="md-board-header" :class="getHeaderClass(header)" :disabled="header.disabled" @click="setActiveBoard(header, true)" ref="boardHeader">
        <div class="md-board-header-container">
          <md-icon :class="{'md-primary':activeBoard === header.id}">fiber_manual_record</md-icon>
        </div>
      </button>
      <span style="flex: 1"></span>
      <span ref="indicator"></span>
    </nav>
  </div>
</template>
<script>
import MdComponent from 'core/MdComponent'
import throttle from 'lodash/throttle'
export default new MdComponent({
  props: {
    mdFixed: Boolean,
    mdCentered: Boolean,
    mdRight: Boolean,
    mdDynamicHeight: {
      type: Boolean,
      default: true
    },
    mdElevation: {
      type: [String, Number],
      default: 0
    },
    mdAuto: {
      type: Boolean,
      default: false
    },
    mdDuration: {
      type: Number,
      default: 5000
    },
    mdControls: {
      type: Boolean,
      default: false
    },
    mdInfinite: {
      type: Boolean,
      default: false
    },
    mdSwipeable: Boolean,
    mdSwipeDistance: {
      type: Number,
      default: 100
    }
  },
  data: () => ({
    boardList: {},
    activeBoard: null,
    activeBoardNumber: 0,
    hasIcons: false,
    hasLabel: false,
    transitionControl: null,
    transitionOff: false,
    contentHeight: '0px',
    contentWidth: '0px',
    autoTransition: null
  }),
  computed: {
    boardClasses() {
      return {
        'md-dynamic-height': this.mdDynamicHeight,
        'md-transition-off': this.transitionOff
      };
    },
    navigationClasses() {
      return {
        'md-has-icon': this.hasIcons,
        'md-has-label': this.hasLabel,
        'md-fixed': this.mdFixed,
        'md-right': !this.mdCentered && this.mdRight,
        'md-centered': this.mdCentered || this.mdFixed
      };
    },
    indicatorClasses() {
      let toLeft = this.lastIndicatorNumber > this.activeBoardNumber;

      this.lastIndicatorNumber = this.activeBoardNumber;

      return {
        'md-transition-off': this.transitionOff,
        'md-to-right': !toLeft,
        'md-to-left': toLeft
      };
    }
  },
  methods: {
    getHeaderClass(header) {
      return {
        'md-active': this.activeBoard === header.id,
        'md-disabled': header.disabled
      };
    },
    registerBoard(boardData) {
      this.boardList[boardData.id] = boardData;
    },
    unregisterBoard(boardData) {
      delete this.boardList[boardData.id];
    },
    updateBoard(boardData) {
      this.registerBoard(boardData);

      if (boardData.active) {
        if (!boardData.disabled) {
          this.setActiveBoard(boardData);
        } else if (Object.keys(this.boardList).length) {
          let boardsIds = Object.keys(this.boardList);
          let targetIndex = boardsIds.indexOf(boardData.id) + 1;
          let target = boardsIds[targetIndex];

          if (target) {
            this.setActiveBoard(this.boardList[target]);
          } else {
            this.setActiveBoard(this.boardList[0]);
          }
        }
      }
    },
    observeElementChanges() {
      this.parentObserver = new MutationObserver(throttle(this.calculateOnWatch, 50));
      this.parentObserver.observe(this.$refs.boardsContent, {
        childList: true,
        attributes: true,
        subtree: true
      });
    },
    getBoardIndex(id) {
      const idList = Object.keys(this.boardList);

      return idList.indexOf(id);
    },
    calculateIndicatorPos() {
      if (this.$refs.boardHeader && this.$refs.boardHeader[this.activeBoardNumber]) {
        const boardsWidth = this.$el.offsetWidth;
        const activeBoard = this.$refs.boardHeader[this.activeBoardNumber];
        const left = activeBoard.offsetLeft;
        const right = boardsWidth - left - activeBoard.offsetWidth;

        this.$refs.indicator.style.left = left + 'px';
        this.$refs.indicator.style.right = right + 'px';
      }
    },
    calculateBoardsWidthAndPosition() {
      const width = this.$el.offsetWidth;
      let index = 0;

      this.contentWidth = width * this.activeBoardNumber + 'px';

      for (const boardId in this.boardList) {
        const board = this.boardList[boardId];

        board.ref.width = width + 'px';
        board.ref.left = width * index + 'px';
        index++;
      }
    },
    calculateContentHeight() {
      this.$nextTick(() => {
        if (Object.keys(this.boardList).length) {
          const el = this.boardList[this.activeBoard].ref.$el;
          let height = el.firstChild.offsetHeight || el.offsetHeight;
          this.contentHeight = height + 'px';
        }
      });
    },
    calculatePosition() {
      window.requestAnimationFrame(() => {
        this.calculateIndicatorPos();
        this.calculateBoardsWidthAndPosition();
        this.calculateContentHeight();
      });
    },
    debounceTransition() {
      window.clearTimeout(this.transitionControl);
      this.transitionControl = window.setTimeout(() => {
        this.calculatePosition();
        this.transitionOff = false;
      }, 200);
    },
    calculateOnWatch() {
      this.calculatePosition();
      this.debounceTransition();
    },
    calculateOnResize() {
      this.transitionOff = true;
      this.calculateOnWatch();
    },
    start() {
      if (this.autoTransition) {
        window.clearInterval(this.autoTransition);
      }
      this.autoTransition = window.setInterval(() => {
        this.moveNextBoard();
      }, this.mdDuration);
    },
    setActiveBoard(boardData, reset) {
      if (this.mdAuto && reset) {
        this.start();
      }
      this.hasIcons = !!boardData.icon;
      this.hasLabel = !!boardData.label;
      this.activeBoard = boardData.id;
      this.activeBoardNumber = this.getBoardIndex(this.activeBoard);
      this.calculatePosition();
      this.$emit('change', this.activeBoardNumber);
    },
    movePrevBoard() {
      let boardsIds = Object.keys(this.boardList);
      let targetIndex = boardsIds.indexOf(this.activeBoard) - 1;
      let target = boardsIds[targetIndex];

      if (target) {
        this.setActiveBoard(this.boardList[target], true);
      } else if (this.mdInfinite) {
        let lastBoard = Object.keys(this.boardList)[Object.keys(this.boardList).length - 1];

        this.setActiveBoard(this.boardList[lastBoard], true);
      }
    },
    moveNextBoard() {
      let boardsIds = Object.keys(this.boardList);
      let targetIndex = boardsIds.indexOf(this.activeBoard) + 1;
      let target = boardsIds[targetIndex];

      if (target) {
        this.setActiveBoard(this.boardList[target], true);
      } else if (this.mdInfinite) {
        let firstBoard = Object.keys(this.boardList)[0];

        this.setActiveBoard(this.boardList[firstBoard], true);
      }
    },
    isHorizontallyInside(positionX) {
      return positionX > this.mountedRect.left && positionX < this.mountedRect.left + this.mountedRect.width;
    },
    isVerticallyInside(positionY) {
      return positionY > this.mountedRect.top && positionY < this.mountedRect.top + this.mountedRect.height;
    },
    handleTouchStart(event) {
      this.mountedRect = this.$refs.boardsContent.getBoundingClientRect();
      const positionX = event.changedTouches[0].clientX;
      const positionY = event.changedTouches[0].clientY;

      if (this.isHorizontallyInside(positionX) && this.isVerticallyInside(positionY)) {
        this.initialTouchPosition = positionX;
        this.canMove = true;
      }
    },
    handleTouchEnd(event) {
      if (this.canMove) {
        const positionX = event.changedTouches[0].clientX;

        const difference = this.initialTouchPosition - positionX;

        const action = difference > 0 ?
          'moveNextBoard' :
          'movePrevBoard';

        if (Math.abs(difference) > this.mdSwipeDistance) {
          this[action]();
        }

        this.canMove = false;
        this.initialTouchPosition = null;
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.observeElementChanges();
      window.addEventListener('resize', this.calculateOnResize);

      if (Object.keys(this.boardList).length && !this.activeBoard) {
        let firstBoard = Object.keys(this.boardList)[0];

        this.setActiveBoard(this.boardList[firstBoard]);
      }

      if (this.mdSwipeable) {
        this.mountedRect = this.$refs.boardsContent.getBoundingClientRect();
        this.initialTouchPosition = null;
        this.canMove = false;

        document.addEventListener('touchstart', this.handleTouchStart);
        document.addEventListener('touchend', this.handleTouchEnd);
      }

      if (this.mdAuto) {
        this.start();
      }
    });
  },
  beforeDestroy() {
    if (this.parentObserver) {
      this.parentObserver.disconnect();
    }

    if (this.autoTransition) {
      window.clearTimeout(this.autoTransition);
    }

    window.removeEventListener('resize', this.calculateOnResize);

    if (this.mdSwipeable) {
      document.removeEventListener('touchstart', this.handleTouchStart);
      document.removeEventListener('touchend', this.handleTouchEnd);
    }

  }
});

</script>
<style lang="scss">
@import "~components/MdAnimation/variables";
@import "~components/MdLayout/mixins";
$board-width: 24px;
$board-max-width: 24px;

.md-boards {
  width: 100%;
  display: flex;
  flex-flow: column;
  position: relative;

  &.md-transition-off * {
    transition: none !important;
  }

  &.md-dynamic-height {
    .md-boards-content {
      transition: height $swift-ease-out-duration $swift-ease-out-timing-function;
    }
  }

  .md-boards-navigation {
    bottom: 0;
    width: 100%;
    height: 48px;
    min-height: 48px;
    position: relative;
    z-index: 1;
    display: flex;
    transition: $swift-ease-out;
    justify-content: space-between;
  }
  .md-boards-arrows {
    position: absolute;
    top: 46%;
    width: 100%;
    >.md-button {
      margin: 0px;
      min-width: 0px;
      height: auto;
    }
    >.next {
      right: 0px;
    }
    >.prev {
      left: 0px;
    }
    .md-icon {
      font-size: 60px;
      height: auto;
      color: rgba(255, 255, 255, .9);
      color: var(--md-theme-default-text-primary-on-primary, rgba(255, 255, 255, .9));
    }
    @include md-layout-xsmall {
      top: auto;
      bottom: 0;
      .md-icon {
        font-size: 40px;
      }
    }
  }
  .md-board-header {
    min-width: $board-width;
    max-width: $board-max-width;
    margin: 0 5px;
    padding: 0 12px;
    display: inline-block;
    position: relative;
    cursor: pointer;
    border: 0;
    background: none;
    transition: $swift-ease-out;
    font-family: inherit;
    font-size: 14px;
    font-weight: 500;
    text-transform: uppercase;

    &.md-disabled {
      cursor: default;
      pointer-events: none;
      user-select: none;
      -webkit-user-drag: none;
    }
  }

  .md-board-header-container {
    display: flex;
    flex-flow: column;
    justify-content: center;
    align-items: center;
    .md-icon {
      margin: 0;
    }
    .md-icon {
      width: 24px;
      min-width: 24px;
      height: 24px;
      min-height: 24px;
      font-size: 24px;
      @include md-layout-xsmall {
        font-size: 16px;
      }
    }
  }

  .md-boards-content {
    width: 100%;
    position: relative;
    overflow: hidden;
  }

  .md-boards-wrapper {
    width: 9999em;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    transform: translate3d(0, 0, 0);
    transition: transform $swift-ease-out-duration $swift-ease-out-timing-function;
  }

  .md-board {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
  }
}

</style>
