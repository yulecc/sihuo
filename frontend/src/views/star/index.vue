<template>
  <main-content-nav-list :nav-list="navList" @navClick="navClick"></main-content-nav-list>
  <Chart :e-data="edataObj" />
</template>

<script setup>
import Chart from '@/components/Charts/index.vue'

const list = []
for (let i = 0; i <= 100; i++) {
  const theta = (i / 100) * 360
  const r = 5 * (1 + Math.sin((theta / 180) * Math.PI))
  list.push([r, theta])
}
const edataObj = {
  title: {
    text: '极坐标双数值轴'
  },
  legend: {
    data: ['line']
  },
  polar: {},
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'cross'
    }
  },
  angleAxis: {
    type: 'value',
    startAngle: 0
  },
  radiusAxis: {},
  series: [
    {
      coordinateSystem: 'polar',
      name: 'line',
      type: 'line',
      data: list
    }
  ]
}

const navClick = (nav) => {
  console.log(nav)
  nav.callback()
}

const log1 = () => {
  console.log(999)
}

const navList = [
  { name: '排行榜2', callback: log1 },
  { name: '绩效评估2', callback: log1 },
  { name: '联防联控2', callback: log1 }
]
</script>
