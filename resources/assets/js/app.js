require("./bootstrap");
import Vue from "vue";
import VueRouter from "vue-router";
Vue.use(VueRouter);
//window.Vue = require("vue");
import ElementUI from "element-ui";

import "element-ui/lib/theme-chalk/index.css";
Vue.use(ElementUI, { size: "small", zIndex: 3000 });


import sendSimple from "./module/send/index.vue";
import taskSimple from "./module/task/index.vue";
import layout from "./module/_layout/index.vue";
// 2. 定义路由
// 每个路由应该映射一个组件。 其中"component" 可以是
// 通过 Vue.extend() 创建的组件构造器，
// 或者，只是一个组件配置对象。
// 我们晚点再讨论嵌套路由。
const routes = [
  {
    path: "/",
    name: "home",
    redirect: "/sendSimple",
    component: layout,
    children: [
      {
        path: "/sendSimple",
        name: "sendSimple",
        component: sendSimple
      },
      {
        path: "/task",
        name: "taskSimple",
        component: taskSimple
      }
    ]
  }
];

// 3. 创建 router 实例，然后传 `routes` 配置
// 你还可以传别的配置参数, 不过先这么简单着吧。
const router = new VueRouter({
  routes // (缩写) 相当于 routes: routes
});

const app = new Vue(Vue.util.extend({ router })).$mount("#app");
