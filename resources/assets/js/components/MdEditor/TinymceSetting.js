const base = {
  height: 500,
  branding: false,
  skin_url: '/assets/vendor/gmf-sys/tinymce/skins/lightgray',
  statusbar: false,
  automatic_uploads: true,
  images_upload_url: '/api/sys/files/tinymce',
  images_upload_base_path: '/',
  language_url: '/assets/vendor/gmf-sys/tinymce/langs/zh_CN.js',
  templates: "/api/sys/editor/templates",
  content_css: ['/css/app.css'],
  block_formats: "Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;",
  style_formats: [
    { title: 'Body 1', selector: 'p,span,div', inline: 'span', classes: 'md-body-1' },
    { title: 'Body 2', selector: 'p,span,div', inline: 'span', classes: 'md-body-2' },
    { title: 'Caption', selector: 'p,h1,h2,h3,h4,h5,h6,span,div,ol,li', inline: 'span', classes: "md-caption" },
    { title: 'Title', selector: 'p,h1,h2,h3,h4,h5,h6,span,div,ol,li', inline: 'span', classes: "md-title" },
    { title: 'Headline', selector: 'p,h1,h2,h3,h4,h5,h6,span,div,ol,li', inline: 'span', classes: 'md-headline' },
    { title: 'Display 1', selector: 'p,h1,h2,h3,h4,h5,h6,span,div,ol,li', inline: 'span', classes: 'md-display-1' },
    { title: 'Display 2', selector: 'p,h1,h2,h3,h4,h5,h6,span,div,ol,li', inline: 'span', classes: 'md-display-2' },
    { title: 'Primary', selector: 'p,h1,h2,h3,h4,h5,h6,span,div,ol,li', inline: 'span', classes: 'md-primary md-theme-default' },
    { title: 'Accent', selector: 'p,h1,h2,h3,h4,h5,h6,span,div,ol,li', inline: 'span', classes: 'md-accent md-theme-default' },
    { title: 'Chip', inline: 'span', classes: 'md-chip md-theme-default' },
  ],
};
const full = _.assignIn({},base, {
  plugins: [
    "advlist colorpicker imagetools pagebreak template anchor contextmenu paste textcolor",
    "autolink directionality insertdatetime preview textpattern",
    "autoresize legacyoutput print toc autosave fullpage link save visualblocks fullscreen lists",
    "searchreplace visualchars charmap media code hr tabfocus",
    "image noneditable table",
  ],
  toolbar1: "formatselect | styleselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | link image",
});

const simple = _.assignIn({},base, {
  menubar: 'edit insert view format table tools',
  plugins: [
    "advlist colorpicker imagetools template anchor paste textcolor",
    "autolink directionality insertdatetime textpattern",
    "autoresize legacyoutput toc autosave link save visualblocks fullscreen lists",
    "visualchars charmap media code hr tabfocus",
    "image noneditable table",
  ],
  toolbar1: "formatselect | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright | numlist bullist outdent indent blockquote| removeformat | template link image",
});
export default { full, simple };