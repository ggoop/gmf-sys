const base = {
  height: 500,
  branding: false,
  // statusbar: false,
  skin: "lightgray",
  automatic_uploads: true,
  images_upload_url: '/api/sys/files/tinymce',
  images_upload_base_path: '/',
  language: 'zh_CN',
  templates: "/api/sys/editor/templates",
  toolbar_items_size: false,
  plugins: [
    "advlist colorpicker imagetools template anchor paste textcolor",
    "autolink directionality insertdatetime textpattern",
    "autoresize legacyoutput toc autosave link save visualblocks fullscreen lists",
    "visualchars charmap media code hr tabfocus",
    "image noneditable table",
  ],
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
const full = _.assignIn({}, base, {
  toolbar1: "bold italic blockquote hr numlist bullist table link image media | alignleft aligncenter alignright | formatselect styleselect  forecolor backcolor | template code removeformat",
});

const simple = _.assignIn({}, base, {
  menubar: false,
  toolbar1: "bold italic blockquote hr numlist bullist table link image media | formatselect styleselect  forecolor backcolor | template code removeformat",
});

const small = _.assignIn({}, base, {
  menubar: false,
  statusbar: false,
  toolbar1: "bold italic blockquote hr numlist bullist table link image | formatselect styleselect | template removeformat",
});
export default { small, simple, full };