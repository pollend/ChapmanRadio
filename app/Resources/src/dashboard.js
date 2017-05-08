import './vendor'


$(document).ready(function () {
  $(document).foundation()
  new Vue({
    el : '#dashboard',
    components: {
    }
  })
  tinymce.init({
    selector: "textarea",  // change this value according to your HTML
    plugin: 'textpattern,spellchecker',
    textpattern_patterns: [
      {start: '*', end: '*', format: 'italic'},
      {start: '**', end: '**', format: 'bold'},
      {start: '#', format: 'h1'},
      {start: '##', format: 'h2'},
      {start: '###', format: 'h3'},
      {start: '####', format: 'h4'},
      {start: '#####', format: 'h5'},
      {start: '######', format: 'h6'},
      {start: '1. ', cmd: 'InsertOrderedList'},
      {start: '* ', cmd: 'InsertUnorderedList'},
      {start: '- ', cmd: 'InsertUnorderedList'}
    ]
  })
})

