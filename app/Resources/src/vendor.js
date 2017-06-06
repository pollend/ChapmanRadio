
import Vue from 'vue'
import 'script-loader!jquery'
import Config from './config'
import VueRouter from 'vue-router'

import {tinymce} from 'tinymce/tinymce'
import 'tinymce/themes/modern/theme'
import 'tinymce/plugins/spellchecker/index'
import 'tinymce/plugins/textpattern/index'
import VeeValidate from 'vee-validate'

window.Vue = Vue
window.FConfig = Config
window.tinymce = tinymce
window.VueRouter = VueRouter

import 'bootstrap-sass/assets/javascripts/bootstrap'
import 'metisMenu/dist/metisMenu'

Vue.use(VueRouter)
Vue.use(VeeValidate)
