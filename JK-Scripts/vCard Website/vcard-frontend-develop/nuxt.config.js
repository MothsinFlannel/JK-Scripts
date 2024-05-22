import webpack from 'webpack'

const env = process.env.NODE_ENV
const config = {
  local: {
    apiUrl: 'https://dev.voodoo.pub/vcard-backend',
  },
  development: {
    apiUrl: 'https://vcard.voodoo.work/backend',
  },
  staging: {
    apiUrl: 'https://vcard.voodoo.services/backend',
  },
  production: {
    apiUrl: 'https://app.vcardsys.com/api',
  },
}

export default {
  /*
   ** Nuxt rendering mode
   ** See https://nuxtjs.org/api/configuration-mode
   */
  mode: 'spa',
  /*
   ** Nuxt target
   ** See https://nuxtjs.org/api/configuration-target
   */
  target: 'server',
  /*
   ** Headers of the page
   ** See https://nuxtjs.org/api/configuration-head
   */
  head: {
    title: 'vCard Route Management',
    meta: [
      { charset: 'utf-8' },
      {
        name: 'viewport',
        content: 'width=device-width, initial-scale=1.0',
      },
      {
        hid: 'description',
        name: 'description',
        content: '',
      },
    ],
    link: [
      {
        rel: 'icon',
        type: 'image/x-icon',
        href: '/favicon.ico',
      },
    ],
  },
  router: {
    base: '/',
  },
  loading: '~/components/Loading',
  loadingIndicator: {
    name: 'circle',
    color: '#5969ff',
  },
  /*
   ** Global CSS
   */
  bootstrapVue: {
    bootstrapCSS: false,
    bootstrapVueCSS: false,
    componentPlugins: ['BVModalPlugin', 'BVToastPlugin'],
    components: [
      'BNavbar',
      'BNavbarBrand',
      'BNavbarToggle',
      'BNavbarNav',
      'BDropdown',
      'BDropdownDivider',
      'BDropdownGroup',
      'BDropdownText',
      'BDropdownItem',
      'BCollapse',
      'BNavText',
      'BNavItem',
      'BCard',
      'BCardBody',
      'BListGroup',
      'BListGroupItem',
      'BButton',
      'BInputGroup',
      'BInputGroupText',
      'BFormInput',
      'BFormFile',
      'BFormDatepicker',
      'BInputGroupAppend',
      'BAlert',
      'BTable',
      'BTableSimple',
      'BThead',
      'BTbody',
      'BTfoot',
      'BTr',
      'BTh',
      'BTd',
      'BPagination',
      'BSpinner',
      'BAvatar',
      'BFormSelect',
      'BFormGroup',
      'BFormInvalidFeedback',
      'BForm',
      'BFormCheckbox',
      'BModal',
      'BIconSearch',
      'BIconEye',
      'BIconClock',
      'BIconPencilFill',
      'BIconPlusCircleFill',
      'BIconCheckCircleFill',
    ],
    directives: ['VBTooltip'],
  },
  css: ['~/assets/scss/index.scss'],
  /*
   ** Plugins to load before mounting the App
   ** https://nuxtjs.org/guide/plugins
   */
  plugins: [
    '~/plugins/dayjs.js',
    '~/plugins/axios.js',
    '~/plugins/vuelidate.js',
    '~/plugins/price.js',
    '~/plugins/sort.js',
    '~/plugins/localStorage.js',
    '~/plugins/getErrorMessage.js',
    '~/plugins/getErrorData.js',
    '~/plugins/clipboard.js',
    '~/plugins/vSelect.js',
    '~/plugins/isAllowed.js',
    '~/plugins/tabsPlugin.js',
    '~/plugins/bFormTextarea.js',
    '~/plugins/locationFormatter.js',
    '~/plugins/checkFilters.js',
  ],
  /*
   ** Auto import components
   ** See https://nuxtjs.org/api/configuration-components
   */
  components: true,
  /*
   ** Nuxt.js dev-modules
   */
  buildModules: [
    // Doc: https://github.com/nuxt-community/eslint-module
    '@nuxtjs/eslint-module',
    // Doc: https://github.com/nuxt-community/stylelint-module
    '@nuxtjs/stylelint-module',
  ],
  /*
   ** Nuxt.js modules
   */
  modules: [
    // Doc: https://bootstrap-vue.js.org
    'bootstrap-vue/nuxt',
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
  ],
  /*
   ** Axios module configuration
   ** See https://axios.nuxtjs.org/options
   */
  axios: {
    baseURL: config[env].apiUrl,
  },
  /*
   ** Build configuration
   ** See https://nuxtjs.org/api/configuration-build/
   */
  build: {
    extractCSS: true,
    plugins: [new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)],
    extend(config, ctx) {
      if (!ctx.isDev && ctx.isClient) {
        config.output.publicPath = '/_nuxt/'
      }
    },
  },
}
