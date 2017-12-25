<template>
  <div class="md-editor-tinymce">
      <textarea :id="id">{{ content }}</textarea>
  </div>
</template>

<script>
   // Import TinyMCE
    import tinymce from 'tinymce/tinymce';
    // A theme is also required
    import 'tinymce/themes/modern/theme';
    // Any plugins you want to use has to be imported
    import 'tinymce/plugins/advlist';
    import 'tinymce/plugins/anchor';
    import 'tinymce/plugins/autolink';
    import 'tinymce/plugins/autoresize';
    import 'tinymce/plugins/autosave';
    import 'tinymce/plugins/bbcode';
    import 'tinymce/plugins/charmap';
    import 'tinymce/plugins/code';
    import 'tinymce/plugins/colorpicker';
    import 'tinymce/plugins/contextmenu';
    import 'tinymce/plugins/directionality';
    import 'tinymce/plugins/emoticons';
    import 'tinymce/plugins/fullpage';
    import 'tinymce/plugins/fullscreen';
    import 'tinymce/plugins/help';
    import 'tinymce/plugins/hr';
    import 'tinymce/plugins/image';
    import 'tinymce/plugins/imagetools';
    import 'tinymce/plugins/importcss';
    import 'tinymce/plugins/insertdatetime';
    import 'tinymce/plugins/legacyoutput';
    import 'tinymce/plugins/link';
    import 'tinymce/plugins/lists';
    import 'tinymce/plugins/media';
    import 'tinymce/plugins/nonbreaking';
    import 'tinymce/plugins/noneditable';
    import 'tinymce/plugins/pagebreak';
    import 'tinymce/plugins/paste';
    import 'tinymce/plugins/preview';
    import 'tinymce/plugins/print';
    import 'tinymce/plugins/save';
    import 'tinymce/plugins/searchreplace';
    import 'tinymce/plugins/spellchecker';
    import 'tinymce/plugins/tabfocus';
    import 'tinymce/plugins/table';
    import 'tinymce/plugins/template';
    import 'tinymce/plugins/textcolor';
    import 'tinymce/plugins/textpattern';
    import 'tinymce/plugins/toc';
    import 'tinymce/plugins/visualblocks';
    import 'tinymce/plugins/visualchars';
    import 'tinymce/plugins/wordcount';
    
    import 'tinymce/skins/lightgray/skin.min.css'
    import 'tinymce/skins/lightgray/content.min.css'
   
    import MdUuid from 'core/utils/MdUuid';
    import TinymceSetting from './TinymceSetting';
    export default {
        name: 'mdEditorTinymce',
        props: { 
                id: {
                    type : String,
                    default: () => 'md-input-' + MdUuid()
                },
                value : { default : '' },
                options:Object
              
                
        },
        data(){
            return {
                content : '',
                editor : null,
                cTinyMce : null,
                checkerTimeout: null,
                isTyping : false
            }; 
        },
        mounted(){
            this.content = this.value;
            this.init();  
        },
        beforeDestroy () {
            this.editor.destroy();
            this.editor.remove();
        },
        watch: {
            value : function (newValue){
                if(!this.isTyping){
                    if(this.editor !== null)
                        this.editor.setContent(newValue);
                    else
                        this.content = newValue;
                }
            }
        },
        methods: {
            init(){
                let options = {
                    selector: '#' + this.id,
                    init_instance_callback : (editor) => {
                        this.editor = editor;
                        editor.on('KeyUp', (e) => {
                           this.submitNewContent();
                        });
                        editor.on('input change undo redo execCommand', (e) => {
                            if(this.editor.getContent() !== this.value){
                               this.submitNewContent();
                            }
                        });
                        editor.on('init', (e) => {
                            editor.setContent(this.content);
                            this.$emit('input', this.content);
                        });
                    }
                };
                
                tinymce.init(this._.assignIn(TinymceSetting,options, this.options));
            },
            submitNewContent(){
                this.isTyping = true;
                if(this.checkerTimeout !== null)
                    clearTimeout(this.checkerTimeout);
                    this.checkerTimeout = setTimeout(()=>{
                        this.isTyping = false;
                    }, 300);
                this.$emit('input', this.editor.getContent());
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
.md-editor-tinymce{
    min-width: 100%;
    max-width: 100%;
}
</style>