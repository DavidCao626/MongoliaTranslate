/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Vant from "vant";
import "vant/lib/vant-css/index.css";
//import traslate from "./components/TraslateHome.vue";
Vue.use(Vant);

Vue.component(
  "example-component",
  require("./components/ExampleComponent.vue")
);
Vue.component("traslate", require("./components/TraslateHome.vue"));

const app = new Vue({
  el: "#app"
});
