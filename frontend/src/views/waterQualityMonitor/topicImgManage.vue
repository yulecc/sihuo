<template>
  <div class="setting-view">
    <main-content-nav-list :nav-list="navList" @navClick="navClick"></main-content-nav-list>
  </div>
</template>

<script setup>
const activeName = ref('user')
const list = [
  { label: '用户管理', name: 'user' },
  { label: '指令管理', name: 'directive' }
]

const navClick = (nav) => {
  console.log(nav)
  nav.callback()
}

const log1 = () => {
  console.log(999)
}

const navList = [
  { name: '排行榜', callback: log1 },
  { name: '绩效评估', callback: log1 },
  { name: '联防联控', callback: log1 }
]
</script>

<style lang="scss" scoped>
.setting-view {
  margin-bottom: 10px;
}
</style>
