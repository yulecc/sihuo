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
  store.dispatch('ui/setSliderMenuState', false)
})

</script>

<style lang="scss" scoped>
.home-view {
  position: absolute;
  top:0;
  width: 100%;
  height: 100%;
  z-index: 1;
  .title {
    text-align: center;
  }
}
</style>
