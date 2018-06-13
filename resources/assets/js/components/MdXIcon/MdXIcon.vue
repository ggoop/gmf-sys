<template>
  <i :class="[b(), `md-x-icon-${name}`]" :style="{ color }" v-on="$listeners">
    <slot />
    <div v-if="isDef(info)" :class="b('info')">{{ info }}</div>
  </i>
</template>

<script>
import create from 'gmf/core/MdComponent';

export default new create({
  name: 'MdXIcon',
  props: {
    name: String,
    info: [String, Number],
    color: String
  }
});
</script>
<style lang="scss">
@font-face {
  font-style: normal;
  font-weight: normal;
  font-family: 'md-x-icon';
  src:url('./icon.ttf') format('truetype');
}
.md-x-icon {
  position: relative;
  display: inline-block;
  font: normal normal normal 14px/1 "md-x-icon";
  font-size: inherit;
  text-rendering: auto;

  &__info {
    color: #fff;
    left: 100%;
    top: -.5em;
    font-size: .5em;
    padding: 0 .3em;
    text-align: center;
    min-width: 1.2em;
    line-height: 1.2;
    position: absolute;
    border-radius: .6em;
    box-sizing: border-box;
    background-color:#f44;
    transform: translateX(-50%);
    font-family: PingFang SC, Helvetica Neue, Arial, sans-serif;
  }

  &:before {
    display: inline-block;
  }
}

.md-x-icon-add-o:before {
  content: "\F000";
}
.md-x-icon-add:before {
  content: "\F001";
}
.md-x-icon-add2:before {
  content: "\F002";
}
.md-x-icon-after-sale:before {
  content: "\F003";
}
.md-x-icon-alipay:before {
  content: "\F004";
}
.md-x-icon-arrow-left:before {
  content: "\F005";
}
.md-x-icon-arrow:before {
  content: "\F006";
}
.md-x-icon-balance-details:before {
  content: "\F007";
}
.md-x-icon-balance-pay:before {
  content: "\F008";
}
.md-x-icon-birthday-privilege:before {
  content: "\F009";
}
.md-x-icon-browsing-history:before {
  content: "\F00A";
}
.md-x-icon-card:before {
  content: "\F00B";
}
.md-x-icon-cart:before {
  content: "\F00C";
}
.md-x-icon-cash-back-record:before {
  content: "\F00D";
}
.md-x-icon-cash-on-deliver:before {
  content: "\F00E";
}
.md-x-icon-certificate:before {
  content: "\F00F";
}
.md-x-icon-chat:before {
  content: "\F010";
}
.md-x-icon-check:before {
  content: "\F011";
}
.md-x-icon-checked:before {
  content: "\F012";
}
.md-x-icon-clear:before {
  content: "\F013";
}
.md-x-icon-clock:before {
  content: "\F014";
}
.md-x-icon-close:before {
  content: "\F015";
}
.md-x-icon-completed:before {
  content: "\F016";
}
.md-x-icon-contact:before {
  content: "\F017";
}
.md-x-icon-coupon:before {
  content: "\F018";
}
.md-x-icon-credit-pay:before {
  content: "\F019";
}
.md-x-icon-debit-pay:before {
  content: "\F01A";
}
.md-x-icon-delete:before {
  content: "\F01B";
}
.md-x-icon-description:before {
  content: "\F01C";
}
.md-x-icon-discount:before {
  content: "\F01D";
}
.md-x-icon-ecard-pay:before {
  content: "\F01E";
}
.md-x-icon-edit-data:before {
  content: "\F01F";
}
.md-x-icon-edit:before {
  content: "\F020";
}
.md-x-icon-exchange-record:before {
  content: "\F021";
}
.md-x-icon-exchange:before {
  content: "\F022";
}
.md-x-icon-fail:before {
  content: "\F023";
}
.md-x-icon-free-postage:before {
  content: "\F024";
}
.md-x-icon-gift-card-pay:before {
  content: "\F025";
}
.md-x-icon-gift-card:before {
  content: "\F026";
}
.md-x-icon-gift:before {
  content: "\F027";
}
.md-x-icon-gold-coin:before {
  content: "\F028";
}
.md-x-icon-goods-collect:before {
  content: "\F029";
}
.md-x-icon-home:before {
  content: "\F02A";
}
.md-x-icon-hot-sale:before {
  content: "\F02B";
}
.md-x-icon-hot:before {
  content: "\F02C";
}
.md-x-icon-info-o:before {
  content: "\F02D";
}
.md-x-icon-like-o:before {
  content: "\F02E";
}
.md-x-icon-like:before {
  content: "\F02F";
}
.md-x-icon-location:before {
  content: "\F030";
}
.md-x-icon-logistics:before {
  content: "\F031";
}
.md-x-icon-member-day-privilege:before {
  content: "\F032";
}
.md-x-icon-more-o:before {
  content: "\F033";
}
.md-x-icon-more:before {
  content: "\F034";
}
.md-x-icon-new-arrival:before {
  content: "\F035";
}
.md-x-icon-new:before {
  content: "\F036";
}
.md-x-icon-other-pay:before {
  content: "\F037";
}
.md-x-icon-passed:before {
  content: "\F038";
}
.md-x-icon-password-not-view:before {
  content: "\F039";
}
.md-x-icon-password-view:before {
  content: "\F03A";
}
.md-x-icon-pause:before {
  content: "\F03B";
}
.md-x-icon-peer-pay:before {
  content: "\F03C";
}
.md-x-icon-pending-deliver:before {
  content: "\F03D";
}
.md-x-icon-pending-evaluate:before {
  content: "\F03E";
}
.md-x-icon-pending-orders:before {
  content: "\F03F";
}
.md-x-icon-pending-payment:before {
  content: "\F040";
}
.md-x-icon-phone:before {
  content: "\F041";
}
.md-x-icon-photo:before {
  content: "\F042";
}
.md-x-icon-photograph:before {
  content: "\F043";
}
.md-x-icon-play:before {
  content: "\F044";
}
.md-x-icon-point-gift:before {
  content: "\F045";
}
.md-x-icon-points-mall:before {
  content: "\F046";
}
.md-x-icon-points:before {
  content: "\F047";
}
.md-x-icon-qr-invalid:before {
  content: "\F048";
}
.md-x-icon-qr:before {
  content: "\F049";
}
.md-x-icon-question:before {
  content: "\F04A";
}
.md-x-icon-receive-gift:before {
  content: "\F04B";
}
.md-x-icon-records:before {
  content: "\F04C";
}
.md-x-icon-search:before {
  content: "\F04D";
}
.md-x-icon-send-gift:before {
  content: "\F04E";
}
.md-x-icon-setting:before {
  content: "\F04F";
}
.md-x-icon-share:before {
  content: "\F050";
}
.md-x-icon-shop-collect:before {
  content: "\F051";
}
.md-x-icon-shop:before {
  content: "\F052";
}
.md-x-icon-shopping-cart:before {
  content: "\F053";
}
.md-x-icon-sign:before {
  content: "\F054";
}
.md-x-icon-stop:before {
  content: "\F055";
}
.md-x-icon-success:before {
  content: "\F056";
}
.md-x-icon-tosend:before {
  content: "\F057";
}
.md-x-icon-underway:before {
  content: "\F058";
}
.md-x-icon-upgrade:before {
  content: "\F059";
}
.md-x-icon-value-card:before {
  content: "\F05A";
}
.md-x-icon-wap-home:before {
  content: "\F05B";
}
.md-x-icon-wap-nav:before {
  content: "\F05C";
}
.md-x-icon-warn:before {
  content: "\F05D";
}
.md-x-icon-wechat:before {
  content: "\F05E";
}

</style>
