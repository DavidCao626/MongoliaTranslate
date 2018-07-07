<template>
  <div>
    <van-nav-bar title="人工翻译" left-text="返回" left-arrow @click-left="onClickLeft" />

    <h2 class="van-doc-demo-block__title">填写您的翻译信息</h2>
    <van-cell-group>
      <van-cell title="翻译类别" :value="typeValue" is-link @click="show=true" />
    </van-cell-group>

    <h2 class="van-doc-demo-block__title">翻译类型</h2>
    <van-radio-group v-model="radio">
      <van-cell-group>
        <van-cell title="汉语<->传统蒙文" clickable @click="radio = '1'">
          <van-radio name="1" />
        </van-cell>
        <van-cell title="汉语<->西里尔蒙文" clickable @click="radio = '2'">
          <van-radio name="2" />
        </van-cell>
        <van-cell title="传统蒙文<->西里尔蒙文" clickable @click="radio = '3'">
          <van-radio name="3" />
        </van-cell>
      </van-cell-group>
    </van-radio-group>
    <h2 class="van-doc-demo-block__title">翻译内容</h2>
    <van-cell-group>
      <van-field v-model="centen" autosize type="textarea" clearable placeholder="请输入需要人工翻译的内容" :error-message="centen.length>0?'已输入'+this.temCenten.length+'字':''">
      </van-field>
    </van-cell-group>
     <br/>
      <br/>
       <br/>
        <br/>

    <van-popup v-model="show" position="bottom">
      <van-picker show-toolbar :columns="columns" ref="picker" @cancel="onCancel" @confirm="onChange" />
    </van-popup>

    <van-submit-bar :price="pric" label="翻译价格：" button-text="发起订单" @submit="showDialog=true" />

    <van-dialog v-model="showDialog" show-cancel-button :before-close="beforeClose">
      <br/>
      <van-field v-model="phone" type="Number" label="联系方式:" placeholder="请输入你的联系方式" />
      <br/>
    </van-dialog>

  </div>

</template>

<script>
import Vant from "vant";
//import traslate from "./components/TraslateHome.vue";
Vue.use(Vant);
import "vant/lib/vant-css/index.css";
import { Dialog } from "vant";
import { Toast } from "vant";
export default {
  data() {
    return {
      showDialog: false,
      phone: "",
      pric: 0,
      centen: "",
      temCenten: "",
      typeValue: "请选择",
      radio: "",
      show: false,
      columns: [
        "牌匾印章",
        "证照公函",
        "字词短句",
        "人名地名",
        "学术论文",
        "法律法规",
        "标书文件",
        "说明书"
      ]
    };
  },
  watch: {
    centen() {
      this.temCenten = this.centen.replace(/[\r\n]/g, "").trim();
      this.pric = this.temCenten.length / 1000 * 150 * 100;
    }
  },
  methods: {
    onClickLeft() {
      window.history.go(-1);
    },
    onCancel() {
      this.show = false;
    },
    onChange(value, index) {
      this.typeValue = value;
      this.show = false;
    },
    onSubmit() {
      this.getDataAjax(this);
      /*Dialog.confirm({
        title: "支付确认",
        message:
          "您正在进行人工翻译操作，总共需要支付" +
          (this.pric / 100).toFixed(2) +
          "元,（每千字150元，当前输入" +
          this.temCenten.length +
          "个字）。确定支付吗？"
      })
        .then(() => {
          this.getDataAjax(this);
        })
        .catch(() => {
          // on cancel
        });*/
    },
    beforeClose(action, done) {
      if (action === "confirm") {
        if (this.phone != "" && this.phone.length >= 7) {
          if (this.temCenten == "" && this.temCenten.length == 0) {
            done();
            Toast.fail("翻译内容不能为空");
          } else {
            done();
            this.onSubmit();
          }
        } else {
          done();
          Toast.fail("手机号码不正确");
        }
      } else {
        done();
      }
    },
    getDataAjax(that) {
      let picker = this.$refs["picker"].getIndexes();
      //debugger;
      Toast.loading({
        mask: true,
        message: "提交中..."
      });
      axios
        .post("/CA/OrderCreate", {
          categories: picker[0],
          type: that.radio,
          body: that.centen,
          text_count: this.temCenten.length,
          unit_price: 150,
          count_price: this.pric,
          phone: this.phone
        })
        .then(function(response) {
          
          Toast.clear();
          if(response.data.state==1){
            window.location.href='/pay/?orderID='+response.data.body;
          }else{
             Toast.fail(response.data.message);
          }
        })
        .catch(function(error) {
         
          Toast.fail("支付失败，请稍后再试。".error);
          that.showDialog = false;
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.van-doc-demo-block__title {
  margin: 0;
  font-weight: 400;
  font-size: 14px;
  color: rgba(69, 90, 100, 0.6);
  padding: 20px 15px 15px;
}
h2 {
  display: block;
}
body {
  background-color: #f6f8f9;
}
</style>
