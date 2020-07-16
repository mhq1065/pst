import Vue from "vue";
import axios from "axios";
import Home from "../components/Home.vue";

Vue.prototype.$ajax = axios;

new Vue({
    render: h => h(Home)
}).$mount("#app");
