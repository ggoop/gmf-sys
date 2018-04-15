<template>
  <md-dialog :md-active.sync="isActive" :md-click-outside-to-close="false" @md-opened="onRefOpen" @md-closed="onRefClose" class="md-refs-dialog">
    <md-toolbar md-elevation="0" class="md-primary">
      <div class="md-toolbar-row">
        <div class="md-toolbar-section-start">
          <h1 class="md-title">{{refInfo.query_comment}}</h1>
        </div>
        <md-fetch class="search" :fetch="autoFetch"></md-fetch>
        <div class="md-toolbar-section-end">
          <md-button class="md-icon-button md-dialog-button-close" @click.native="onCancel()">
            <md-icon>close</md-icon>
          </md-button>
        </div>
      </div>
    </md-toolbar>
    <md-dialog-content class="no-padding layout flex">
      <template v-if="componentName">
        <md-wrap :name="componentName" :md-active.sync="isRefLoaded" :md-init="mdInit" :multiple="multiple" :md-q="currentQ" :value="value" :md-ref-id="mdRefId" :md-ref-type="mdRefType" :options="options" @md-cancel="onCancel" @md-confirm="onConfirm"></md-wrap>
      </template>
    </md-dialog-content>
    <md-loading :loading="loading"></md-loading>
  </md-dialog>
</template>
<script>
import common from 'gmf/core/utils/common';

export default {
  name: 'MdRef',
  props: {
    value: {
      type: Object,
    },
    multiple: {
      type: Boolean,
      default: true
    },
    mdRefId: String,
    mdRefType: {
      type: String,
      default: 'entity'
    },
    options: {
      type: Object,
      default () {
        return {
          wheres: {},
          orders: []
        }
      }
    },
    mdActive: Boolean,
    mdInit: { type: Function },
  },
  data() {
    return {
      isActive: false,
      isRefLoaded: false,
      currentQ: '',
      refInfo: {},
      loading: 0,
    };
  },
  watch: {
    async mdActive(isActive) {
      this.isActive = isActive;
      await this.$nextTick();
    },
  },
  computed: {
    componentName() {
      if (this.refInfo && this.refInfo.entity_id)
        return 'md-ref-body-entity';
      else if (this.refInfo && this.refInfo.component && this.refInfo.component.code)
        return this.refInfo.component.code;
      return '';
    },
  },
  methods: {
    onRefOpen() {
      this.refInfo = {};
      this.isRefLoaded = false;
      if (this.mdInit) {
        Promise.all([this.mdInit(this.options)]).then(v => {
          this.fetchQueryInfo();
        });
      } else {
        this.fetchQueryInfo();
      }
    },
    fetchQueryInfo() {
      if (!this.mdRefId) return;
      this.$http.get('sys/queries/' + this.mdRefId).then(res => {
        this.refInfo = res.data.data;
        this.$nextTick().then(r => {
          this.isRefLoaded = true;
        });
      });
    },
    onRefClose() {
      if (!this.canFireEvents) return;
      this.$emit('close');
    },
    autoFetch(q) {
      this.currentQ = q;
    },
    onCancel() {
      if (!this.canFireEvents) return;
      this.$emit('cancel', false);
      this.closeDialog();
    },
    onConfirm(datas) {
      if (!this.canFireEvents) return;
      this.$emit('confirm', datas);
      this.closeDialog();
    },
    closeDialog() {
      this.isActive = false;
      this.$emit('update:mdActive', false);
    },
    open() {
      this.isActive = true;
      this.$emit('update:mdActive', true);
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.canFireEvents = true;
    });
  },
};

</script>
<style lang="scss">
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
.md-refs-dialog {
  min-width: 50%;
  min-height: 70%;
  @include md-layout-xsmall {
    min-width: 100%;
    min-height: 100%;

    .md-pagination {
      display: none;
    }
  }
  .md-dialog-content {
    background-attachment: inherit;
  }
  .md-toolbar {
    .search {
      margin: 0 10px;
    }
  }
}

</style>
