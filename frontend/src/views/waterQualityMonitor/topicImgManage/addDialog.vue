<template>
  <el-dialog v-model="dialogVisible" title="新增">
    <el-form ref="formRef" :model="formData" :rules="rules" label-width="80px">
      <el-form-item prop="mon_source" label="监测来源" required>
        <el-select v-model="formData.mon_source" placeholder="请选择" clearable>
          <el-option
            v-for="item in options.source"
            :key="item.id"
            :label="item.mon_source"
            :value="item.id"
          >
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item prop="mon_category" label="监测类型" required>
        <el-select v-model="formData.mon_category" placeholder="请选择" clearable>
          <el-option
            v-for="item in options.category"
            :key="item.id"
            :label="item.value"
            :value="item.id"
          >
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item prop="mon_time" label="监测时间" required>
        <el-date-picker
          v-model="formData.mon_time"
          type="date"
          value-format="YYYY-MM-DD HH:mm:ss"
          range-separator="至"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
        >
        </el-date-picker>
      </el-form-item>
      <el-form-item prop="mon_images" label="图片">
        <el-upload
          action="/api/whole/imgUpload"
          list-type="picture-card"
          :on-success="handleUploadSuccess"
        >
          <i class="el-icon-plus"></i>
        </el-upload>
      </el-form-item>
    </el-form>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" :disabled="formLoading" @click="onSubmit">确 定</el-button>
      </span>
    </template>
  </el-dialog>
</template>

<script setup>
import topicApi from '@/api/topic'
import { ElMessage } from 'element-plus'
const props = defineProps({
  options: {
    type: Object,
    default: () => {}
  }
})
const emits = defineEmits(['close'])

const dialogVisible = true
const close = () => {
  emits('close')
}
const rules = {
  mon_source: [
    { required: true, message: '请选择监测来源', trigger: 'blur' }
  ],
  mon_category: [
    { required: true, message: '请选择监测类型', trigger: 'blur' }
  ],
  mon_time: [
    { required: true, message: '请选择监测时间', trigger: 'blur' }
  ]
}

const formData = reactive({
  mon_source: '',
  mon_category: '',
  mon_time: '',
  mon_images: []
})

const handleUploadSuccess = (response, file, fileList) => {
  const data = response.data
  const imgUrl = 'http://' + data.domain + data.filepath
  formData.mon_images.push(imgUrl)
}

const formRef = ref(null)
const onSubmit = () => {
  formRef.value.validate((valid) => {
    if (valid) {
      const data = { ...formData }
      data.mon_images = JSON.stringify(data.mon_images)
      // data.mon_time = Number(data.mon_time.substr(0, 10))
      topicApi.add(data).then(res => {
        console.log(res)
        ElMessage.success('添加成功')
        close()
      })
      console.log('error submit!!')
    }
  })
}
</script>

<style lang='scss' scoped>

</style>
