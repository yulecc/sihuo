import { createApp } from 'vue'

import 'vue-global-api'

import ElementPlus from 'element-plus'
import 'element-plus/packages/theme-chalk/src/base.scss'
import 'element-plus/lib/theme-chalk/index.css'
import './styles/index.scss' // global css

import App from './App.vue'
import router from './router'
import store from './store'
import directives from './directive'

import './permission' // permission control
import { DateFormat } from '@/utils/util'

import MainContentNavList from '@/components/MainContentNavList.vue'

const app = createApp(App)
app.use(router).use(store).use(ElementPlus).use(directives).mount('#app')

app.config.globalProperties.$DateFormat = DateFormat

app.component('MainContentNavList', MainContentNavList)

export default app
