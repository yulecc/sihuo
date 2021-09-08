<template>
  <el-table :data="tableData" style="width: 100%">
    <!--    <el-table-column fixed prop="date" label="日期" width="150"></el-table-column>-->
    <el-table-column prop="index" label="序号" width="120" type="index"> </el-table-column>
    <el-table-column prop="type" label="类型" width="120"> </el-table-column>
    <el-table-column prop="code" label="编码" width="120"></el-table-column>
    <el-table-column prop="attrs" label="属性" width="120"> </el-table-column>
    <el-table-column prop="sectionType" label="断面类型" width="120"> </el-table-column>
    <el-table-column prop="watchName" label="监测名称" width="120"> </el-table-column>
    <el-table-column prop="relatedWaterArea" label="关联水域" width="120"> </el-table-column>
    <el-table-column prop="relatedRiverManager" label="关联河长" width="120"> </el-table-column>
    <el-table-column prop="level" label="河湖长等级" width="120"> </el-table-column>
    <el-table-column prop="area" label="水域行政区划" width="120"> </el-table-column>
    <el-table-column prop="watchMethod" label="监测方式" width="120"> </el-table-column>
    <el-table-column prop="location" label="定位" width="120"> </el-table-column>
    <el-table-column prop="frequency" label="监测频率" width="120">
      <template #default="scope">
        <el-link type="primary" @click="handleDelete(scope.row)">查看</el-link>
      </template>
    </el-table-column>
    <el-table-column prop="controlLevel" label="控制级别" width="120"> </el-table-column>
    <el-table-column fixed="right" label="操作" width="100">
      <template #default="scope">
        <el-link type="primary" @click="handleEdit(scope.row)">编辑</el-link>
        <el-link class="ml-1" type="danger" @click="handleDelete(scope.row)">删除</el-link>
      </template>
    </el-table-column>
  </el-table>
</template>

<script setup>
// const props = defineProps({
//   tableData1: {
//     type: 'array',
//     default: () => []
//   }
// })
const item = {
  type: '河道',
  code: 'jb0158',
  attrs: '分段',
  sectionType: '分段',
  watchName: '监测点位',
  relatedWaterArea: '江北大乔',
  relatedRiverManager: '付荣贵',
  level: '县级',
  area: '江北区',
  watchMethod: '手动',
  location: '王小虎',
  frequency: '月/次',
  controlLevel: '人工控制'
}
const list = new Array(40).fill(item)
const tableData = ref(list)

const handleEdit = (row) => {

}

const handleDelete = (row) => {

}
</script>

<style lang='scss' scoped>

</style>
