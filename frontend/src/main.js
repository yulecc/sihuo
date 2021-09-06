import { createApp } from 'vue'

import 'vue-global-api'

import ElementPlus from 'element-plus'
import 'element-plus/packages/theme-chalk/src/base.scss'
import 'element-plus/lib/theme-chalk/index.css'
import './styles/index.scss' // global css

import VueCesium from 'vue-cesium'
import 'vue-cesium/lib/theme-default/index.css'

import App from './App.vue'
import router from './router'
import store from './store'
import directives from './directive'

import './permission' // permission control
import { DateFormat } from '@/utils/util'

import MainContentNavList from '@/components/MainContentNavList.vue'

window.global = window

const app = createApp(App)
app.use(VueCesium, {
  cesiumPath: './Cesium/Cesium.js',
  accessToken: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiIyYTkxMWEyMi0wM2M0LTRhYWQtYTc3OS0zM2Y5NDA5NDZkZmMiLCJpZCI6NjI2ODcsImlhdCI6MTYyNzM2Nzc5N30.4LIbSsJlfe-1XVRBQSv1j8bmfM4BazvlLE0F3HK9xzs'
}).use(router).use(store).use(ElementPlus).use(directives).mount('#app')

app.config.globalProperties.$DateFormat = DateFormat

app.component('MainContentNavList', MainContentNavList)

export default app
