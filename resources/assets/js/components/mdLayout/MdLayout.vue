<script>
  export default {
    name:'MdLayout',
    props: {
      mdTag: {
        type: String,
        default: 'div'
      },
      mdWrap:{
        type:Boolean,
        default:true
      },
      mdNowrap: Boolean,
      //Row
      mdRow:Boolean,
      mdXsRow:Boolean,
      mdGtXsRow:Boolean,
      mdSmRow:Boolean,
      mdGtSmRow:Boolean,
      mdMdRow:Boolean,
      mdGtMdRow:Boolean,
      mdLgRow:Boolean,
      mdGtLgRow:Boolean,
      mdXlRow:Boolean,
      //Column
      mdColumn:Boolean,
      mdXsColumn:Boolean,
      mdGtXsColumn:Boolean,
      mdSmColumn:Boolean,
      mdGtSmColumn:Boolean,
      mdMdColumn:Boolean,
      mdGtMdColumn:Boolean,
      mdLgColumn:Boolean,
      mdGtLgColumn:Boolean,
      mdXlColumn:Boolean,
      //Align
      mdAlign:String,
      mdXsAlign:String,
      mdGtXsAlign:String,
      mdSmAlign:String,
      mdGtSmAlign:String,
      mdMdAlign:String,
      mdGtMdAlign:String,
      mdLgAlign:String,
      mdGtLgAlign:String,
      mdXlAlign:String,
      //Flex
      mdFlex:String,
      mdFlexXs:String,
      mdFlexGtXs:String,
      mdFlexSm:String,
      mdFlexGtSm:String,
      mdFlexMd:String,
      mdFlexGtMd:String,
      mdFlexLg:String,
      mdFlexGtLg:String,
      mdFlexXl:String,
      //Order
      mdOrder:String,
      mdOrderXs:String,
      mdOrderGtXs:String,
      mdOrderSm:String,
      mdOrderGtSm:String,
      mdOrderMd:String,
      mdOrderGtMd:String,
      mdOrderLg:String,
      mdOrderGtLg:String,
      mdOrderXl:String,
      //Hide
      mdHide:Boolean,
      mdHideXs:Boolean,
      mdHideGtXs:Boolean,
      mdHideSm:Boolean,
      mdHideGtSm:Boolean,
      mdHideMd:Boolean,
      mdHideGtMd:Boolean,
      mdHideLg:Boolean,
      mdHideGtLg:Boolean,
      mdHideXl:Boolean,

      //Show
      mdShow:Boolean,
      mdShowXs:Boolean,
      mdShowGtXs:Boolean,
      mdShowSm:Boolean,
      mdShowGtSm:Boolean,
      mdShowMd:Boolean,
      mdShowGtMd:Boolean,
      mdShowLg:Boolean,
      mdShowGtLg:Boolean,
      mdShowXl:Boolean,
    },
    data: () => ({
      layout: ['Xs','GtXs','Sm','GtSm','Md','GtMd','Lg','GtLg','Xl'],
    }),
    computed: {
      classes() {
        let classes = {
          'layout-wrap': this.mdWrap,
          'layout-nowrap': this.mdNowrap,
        };

        if (this.mdGutter) {
          classes['layout-gutter'] = true;
        }
        /*layout-row*/
        this.generateLayoutClasses(classes,'Row','layout-');
        this.layout.forEach(item=>{
          this.generateLayoutClasses(classes,item+'Row','layout-');
        });
        /*layout-column*/
        this.generateLayoutClasses(classes,'Column','layout-');
        this.layout.forEach(item=>{
          this.generateLayoutClasses(classes,item+'Column','layout-');
        });

        /*align   layout-align-center-center,*/
        this.generateAlignClasses(classes,'Align','layout-');
        this.layout.forEach(item=>{
          this.generateAlignClasses(classes,item+'Align','layout-');
        });

        /*flex*/
        this.generateFlexClasses(classes,'Flex','');
        this.layout.forEach(item=>{
          this.generateFlexClasses(classes,'Flex'+item,'');
        });

        /*order*/
        this.generateFlexClasses(classes,'Order','flex-');
        this.layout.forEach(item=>{
          this.generateFlexClasses(classes,'Order'+item,'flex-');
        });

        /*hide*/
        this.generateLayoutClasses(classes,'Hide','');
        this.layout.forEach(item=>{
          this.generateLayoutClasses(classes,'Hide'+item,'');
        });

        /*Show*/
        this.generateLayoutClasses(classes,'Show','');
        this.layout.forEach(item=>{
          this.generateLayoutClasses(classes,'Show'+item,'');
        });
        return classes;
      }
    },
    methods: {
      generateLayoutClasses(object,prop,pre) {
        if (this['md'+prop]) {
         object[pre+this.snakeCase(prop)]=true;
        }
      },
      generateFlexClasses(object,prop,pre) {
        var v=this['md'+prop];
        if(!v)return;
        //v=none,grow,nogrow,initial,auto,noshrink,0~100
        object[pre+this.snakeCase(prop)+'-'+v]=true;
      },
      generateAlignClasses(object,prop,pre) {
        var v=this['md'+prop];
        if(!v)return;
        if(v=='none'){
          v='none none';
        }
        const align=v.split(' ');
        if(align.length<2){
          align.push('none');
        }
        if(align[0]==''||align[0]=='none')align[0]='start';
        if(align[1]==''||align[1]=='none')align[1]='stretch';
        object[pre+this.snakeCase(prop)+'-'+align[0]+'-'+align[1]]=true;
      },
      snakeCase(name) {
          name = name.replace(/[A-Z.]/g, function(letter, pos) {
              return (pos ? '-' : '') + letter.toLowerCase();
          });
          return name.replace(/\./g, '');
      }
    },
    render(createElement) {
      return createElement(this.mdTag, {
        staticClass: 'layout',
        class: this.classes
      }, this.$slots.default);
    }
  };
</script>

<style lang="scss">
  @import "./layout";
</style>