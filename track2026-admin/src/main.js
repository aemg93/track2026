import { createApp } from 'vue'
import App from './App.vue'

import router from './router'
import { createPinia } from 'pinia'

import './style.css'

import { useAuthStore } from './stores/auth'

/*
|--------------------------------------------------------------------------
| ECHARTS
|--------------------------------------------------------------------------
*/

import ECharts from 'vue-echarts'

import { use } from 'echarts/core'

import {
    CanvasRenderer,
} from 'echarts/renderers'

import {
    BarChart,
    LineChart,
    PieChart,
} from 'echarts/charts'

import {
    GridComponent,
    TooltipComponent,
    LegendComponent,
    TitleComponent,
} from 'echarts/components'

use([
    CanvasRenderer,
    BarChart,
    LineChart,
    PieChart,
    GridComponent,
    TooltipComponent,
    LegendComponent,
    TitleComponent,
])

/*
|--------------------------------------------------------------------------
| APP
|--------------------------------------------------------------------------
*/

const app = createApp(App)

const pinia = createPinia()

app.use(pinia)
app.use(router)

/*
|--------------------------------------------------------------------------
| GLOBAL COMPONENTS
|--------------------------------------------------------------------------
*/

app.component('v-chart', ECharts)

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

const auth = useAuthStore()

auth.me()

/*
|--------------------------------------------------------------------------
| MOUNT
|--------------------------------------------------------------------------
*/

app.mount('#app')