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
            <van-field v-model="centen" type="textarea" clearable placeholder="请输入需要人工翻译的内容" :error-message="centen.length>0?'已输入'+centen.length+'字':''" >
            </van-field>
        </van-cell-group>

        <van-popup v-model="show" position="bottom">
            <van-picker show-toolbar :columns="columns" @cancel="onCancel" @confirm="onChange" />
        </van-popup>

        <van-submit-bar :price="pric" label="翻译价格：" button-text="发起支付" @submit="onSubmit" />
    </div>
</template>

<script>
import { Dialog } from 'vant';
export default {
  data() {
    return {
      pric: 0,
      centen: "",
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
    
      this.pric=this.centen.length / 1000 * 150*100
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
      Dialog.confirm({
        title: "支付确认",
        message: "您正在进行人工翻译操作，总共需要支付"+(this.pric/100).toFixed(2)+"元,（每千字150元，当前输入"+this.centen.length+"个字）。确定支付吗？"
      })
        .then(() => {
          // on confirm
        })
        .catch(() => {
          // on cancel
        });
    }
  }
};
</script>

<style>
.van-doc-demo-block__title {
  margin: 0;
  font-weight: 400;
  font-size: 14px;
  color: rgba(69, 90, 100, 0.6);
  padding: 40px 15px 15px;
}
h2 {
  display: block;
}
body {
  background-color: #f6f8f9;
}
</style>
