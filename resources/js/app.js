import '../css/main.css';

import { createPinia } from 'pinia'
import { useStyleStore } from '@/Stores/style.js'
import { useLayoutStore } from '@/Stores/layout.js'

import { darkModeKey, styleKey } from '@/config.js'

import { createApp, h } from 'vue';

// plugin
import { Vue3Mq } from "vue3-mq";
import VueSignaturePad from 'vue-signature-pad';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import CKEditor from '@ckeditor/ckeditor5-vue';

import moment from 'moment'

import VueJsTour from '@globalhive/vuejs-tour';
import '@globalhive/vuejs-tour/dist/style.css';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

const pinia = createPinia()

createInertiaApp({
    progress: {
      color: '#5afc03',
    },
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
        //set mixins
        .mixin({
          methods: {

            //method "hasAnyPermission"
            hasAnyPermission: function (permissions) {

              //get permissions from props
              let allPermissions = this.$page.props.auth.permissions;

              let hasPermission = false;

              let myPermission = permissions[0];
              let myPermissionArray = myPermission.split("|");

              myPermissionArray.forEach((item) => {
                if(allPermissions[item]){
                  hasPermission = true;     
                }
              })

              return hasPermission;
            },

            //method dropdownCheck
            // dropdownCheck: function (value) {

            //   if(value != undefined){
            //     return value.split("|").includes(location.pathname.substring(1));
            //   } else {
            //     return false;
            //   }

            //   // //get permissions from props
            //   // let allPermissions = this.$page.props.auth.permissions;

            //   // let hasPermission = false;

            //   // let myPermission = permissions[0];
            //   // let myPermissionArray = myPermission.split("|");

            //   // myPermissionArray.forEach((item) => {
            //   //   if(allPermissions[item]){
            //   //     hasPermission = true;     
            //   //   }
            //   // })
            // },

            //method "numberBomItemFormat"
            numberFormat: function (value) {

              return (value != '' && value != undefined && value != null) ? parseFloat(value).toLocaleString('en-US') : 0;
              
            },

            dateTimeFormat: function (value){
              return moment(value).format('DD/MM/YYYY HH:mm:ss')
            },

            dateFormat: function (value){
              return moment(value).format('DD/MM/YYYY')
            },

            thisRoute: function (url) {
              return $page.url.startsWith(url)
            }

            //format price
            // formatPrice(value) {
            //   let val = (value/1).toFixed(0).replace('.', ',')
            //   return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            // },

          },
        })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue, Ziggy)
            .use(Vue3Mq)
            .use(VueSignaturePad)
            .use(VueSweetalert2)
            .use(CKEditor)
            .use(VueJsTour)
            .mount(el);
    },
});

const styleStore = useStyleStore(pinia)
const layoutStore = useLayoutStore(pinia)

/* App style */
styleStore.setStyle(localStorage[styleKey] ?? 'basic')

/* Dark mode */
if ((!localStorage[darkModeKey] && window.matchMedia('(prefers-color-scheme: dark)').matches) || localStorage[darkModeKey] === '1') {
  styleStore.setDarkMode(true)
}

/* Collapse mobile aside menu on route change */
router.on('navigate', (event) => {
  layoutStore.isAsideMobileExpanded = false
  layoutStore.isAsideLgActive = false
})
