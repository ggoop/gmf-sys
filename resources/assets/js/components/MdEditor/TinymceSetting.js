export default {
  height: 500,
  // theme: false,
  // toolbar_items_size: "small",
  skin_url:'/assets/vendor/gmf-sys/tinymce/skins/lightgray',
  statusbar: false,
  automatic_uploads:true,
  images_upload_url: '/api/sys/files/tinymce',
  images_upload_base_path:'/',
  plugins: [
    "advlist colorpicker imagetools pagebreak template anchor contextmenu importcss paste textcolor",
    "autolink directionality insertdatetime preview textpattern",
    "autoresize legacyoutput print toc autosave fullpage link save visualblocks fullscreen lists",
    "searchreplace visualchars charmap media code hr tabfocus",
    "image noneditable table",
  ],
  templates: [
    { title: 'Test template 1', content: 'Test 1' },
    { title: 'Test template 2', content: 'Test 2' }
  ],
  block_formats: "Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;",
  toolbar1: "formatselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | link image",
  external_plugins: {},
  language_url: '/assets/vendor/gmf-sys/tinymce/langs/zh_CN.js',
  content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css'
  ]
};
