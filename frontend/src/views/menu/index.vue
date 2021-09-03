<template>
  <div>
    <main-content-nav-list :nav-list="navList" @navClick="navClick"></main-content-nav-list>
    <h1>{{ title }}</h1>
    <router-view v-slot="{ Component }">
      <component :is="Component" />
    </router-view>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const title = ref('菜单')

const navClick = (nav) => {
  console.log(nav)
  nav.callback()
}

const log1 = () => {
  console.log(999)
}

const navList = [
  { name: '排行榜1', callback: log1 },
  { name: '绩效评估1', callback: log1 },
  { name: '联防联控2', callback: log1 }
]
</script>
