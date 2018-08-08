<template>
  <div class="md-app md-app-internal-drawer layout-column" :class="[appClasses, $mdActiveTheme]">
    <slot name="md-app-background"></slot>
    <slot name="md-app-toolbar"></slot>
    <main class="md-app-container layout-flex layout-row" :style="[containerStyles, contentStyles]" :class="[$mdActiveTheme, scrollerClasses]">
      <slot name="md-app-drawer"></slot>
      <div class="md-app-scroller layout-column layout-flex" :class="[$mdActiveTheme, scrollerClasses]">
        <slot name="md-app-content"></slot>
      </div>
    </main>
    <slot name="md-drawer"></slot>
    <slot name="md-app-bottom-bar"></slot>
  </div>
</template>

<script>
  import MdComponent from 'gmf/core/MdComponent'
  import MdAppMixin from './MdAppMixin'

  export default new MdComponent({
    name: 'MdAppInternalDrawer',
    mixins: [MdAppMixin]
  })
</script>

<style lang="scss">
  @import "~gmf/components/MdAnimation/variables";

  .md-app-internal-drawer {
    flex-direction: column;

    .md-app-scroller {
      overflow: auto;
    }
  }
</style>
