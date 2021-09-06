<template>
  <div class="home-view">
    <CesiumContainer></CesiumContainer>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import store from '@/store'
import CesiumContainer from '../CesiumContainer/CesiumContainer.vue'

const navList = [
  { name: '排行榜', callback: '' },
  { name: '绩效评估', callback: '' },
  { name: '联防联控', callback: '' }
]

onMounted(() => {
  store.dispatch('app/setMainHeaderNavList', navList)
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
