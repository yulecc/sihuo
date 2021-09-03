<template>
  <div class="app-main-header layout-items-center">
    <div
      v-for="item in navList"
      :key="item.name"
      class="header-nav-item layout-center"
      :class="{checked:item.name === checked }"
      @click="handleChange(item)"
    >{{ item.name }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  navList: {
    type: Array,
    default: () => []
  }
})

const emits = defineEmits(['navClick'])

const checked = ref('')

const handleChange = (nav) => {
  checked.value = nav.name
  emits('navClick', nav)
}

onMounted(() => {
  if (props.navList.length) {
    checked.value = props.navList[0].name
  }
})

</script>

<style lang='scss' scoped>

</style>
