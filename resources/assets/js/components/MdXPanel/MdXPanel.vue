<template>
  <md-x-cell-group :class="b()">
    <slot name="header">
      <md-x-cell :class="b('header')" :title="title" :label="desc" :value="status" />
    </slot>
    <div :class="b('content')">
      <slot />
    </div>
    <div v-if="$slots.footer" :class="b('footer')" class="van-hairline--top">
      <slot name="footer" />
    </div>
  </md-x-cell-group>
</template>

<script>
import create from 'gmf/core/MdComponent';

export default create({
  name: 'MdXPanel',
  props: {
    desc: String,
    title: String,
    status: String
  }
});
</script>
<style lang="scss">
.md-x-panel {
  background: #fff;

  &__header {
    .md-x-cell__value {
      color: #f44;
    }
  }

  &__footer {
    padding: 10px 15px;
  }
}

</style>
