<template>
  <div>
    <br/>
    <el-form :model="dynamicValidateForm" ref="dynamicValidateForm" label-width="100px" class="demo-dynamic">
      <el-form-item prop="desc" label="短信内容" :rules="[
      { required: true, message: '请输入短信内容', trigger: 'blur' },
    ]">
        <el-input autosize type="textarea" :autosize="{ minRows: 3, maxRows: 6}" v-model="dynamicValidateForm.desc" rows="3" style="width:600px"></el-input>
        <br/>当前已经输入{{dynamicValidateForm.desc.replace(/[\r\n]/g, "").length}}个字，{{parseInt(dynamicValidateForm.desc.replace(/[\r\n]/g, "").length/70)
        <=1?1:parseInt(dynamicValidateForm.desc.replace(/[\r\n]/g, "").length/70)}}条短信 </el-form-item>

          <el-form-item v-for="(domain, index) in dynamicValidateForm.domains" :label="'联系人' + index" :key="domain.key" :prop="'domains.' + index + '.value'" :rules="{
      required: true, message: '联系人不能为空', trigger: 'blur'
    }">
            <el-input v-model="domain.value" suffix-icon="el-icon-phone" style="width:200px"></el-input>
            <el-button @click.prevent="removeDomain(domain)">删除</el-button>
          </el-form-item>

          <el-form-item>

            <el-button @click="addDomain">新增联系人</el-button>
            <!-- <el-button @click="resetForm('dynamicValidateForm')">重置</el-button> -->
            <el-button type="primary" @click="submitForm('dynamicValidateForm')">发送</el-button>
          </el-form-item>
    </el-form>
  </div>
</template>

<script>

export default {
  data() {
    return {
      dynamicValidateForm: {
        domains: [
          {
            value: ""
          }
        ],
        desc: ""
      }
    };
  },

  methods: {
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          var that = this;
          this.dynamicValidateForm.domains.forEach(e => {
            axios
              .post("/sms/xsend", {
                body: this.dynamicValidateForm.desc,
                phone: e.value
              })
              .then(function(response) {
                console.log(response);
                if (response.data.isok == "ok") {
                  that.$notify({
                    title: "发送成功",
                    message: response.data.message + "已下发",
                    type: "success"
                  });
                } else {
                  that.$notify.error({
                    title: "发送失败",
                    message: "原因：" + response.data.message
                  });
                }
              })
              .catch(function(error) {
                that.$message({
                  message: "服务器维护中，稍后再试",
                  type: "warning"
                });
              });
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    },
    removeDomain(item) {
      var index = this.dynamicValidateForm.domains.indexOf(item);
      if (index !== -1) {
        this.dynamicValidateForm.domains.splice(index, 1);
      }
    },
    addDomain() {
      this.dynamicValidateForm.domains.push({
        value: "",
        key: Date.now()
      });
    }
  }
};
</script>

<style>
</style>
