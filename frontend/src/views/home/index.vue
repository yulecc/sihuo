<template>
  <div class="home-view">
    <!--    <main-content-nav-list :nav-list="navList" @navClick="navClick"></main-content-nav-list>-->
    <CesiumContainer></CesiumContainer>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import store from '@/store'
import CesiumContainer from '../CesiumContainer/CesiumContainer.vue'

const log1 = () => {
  console.log(999)
}

const navList = [
  { name: '排行榜1', callback: log1 },
  { name: '绩效评估1', callback: log1 },
  { name: '联防联控2', callback: log1 }
]

const navClick = (nav) => {
  console.log(nav)
  nav.callback()
}

onMounted(() => {
})

const msg = ref('Welcome to the management system')

const h2Style = reactive({
  color: '#435448'
})

setInterval(() => {
  h2Style.color = '#' + Math.random().toString(16).slice(-6)
}, 1000)
</script>

<style lang="scss" scoped>
.home-view {
  .title {
    color: v-bind('h2Style.color');
    text-align: center;
  }
}
</style>
