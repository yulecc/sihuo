<template>
  <el-table :data="tableData" style="width: 100%">
    <el-table-column prop="index" label="序号" width="120" type="index"> </el-table-column>
    <el-table-column prop="river_type" label="类型" width="120"> </el-table-column>
    <el-table-column prop="point_number" label="编码" width="120"></el-table-column>
    <el-table-column prop="river_attribute" label="属性" width="120"> </el-table-column>
    <el-table-column prop="section_type" label="断面类型" width="120"> </el-table-column>
    <el-table-column prop="test_name" label="监测名称" width="120"> </el-table-column>
    <el-table-column prop="relatedWaterArea" label="关联水域" width="120"> </el-table-column>
    <el-table-column prop="relatedRiverManager" label="关联河长" width="120"> </el-table-column>
    <el-table-column prop="people" label="河湖长等级" width="120"> </el-table-column>
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
        <el-link type="primary" @click="handleView(scope.row)">查看</el-link>
      </template>
    </el-table-column>
  </el-table>
  <div v-if="pagination.total" class="text-center">
    <el-pagination
      v-model:currentPage="pagination.page"
      :page-sizes="[20, 50, 100]"
      :page-size="pagination.limit"
      layout="sizes, prev, pager, next"
      :total="pagination.total"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
    >
    </el-pagination>
  </div>
</template>

<script setup>

defineProps({
  tableData: {
    type: Array,
    default: () => []
  },
  pagination: {
    type: Object
  }
})

const handleView = (row) => {

}

const handleDelete = (row) => {

}

const handleSizeChange = (limit) => {
  pagination.limit = limit
}
const handleCurrentChange = (page) => {
  pagination.page = page
}
</script>

<style lang='scss' scoped>

</style>
