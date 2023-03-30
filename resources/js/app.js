import "./bootstrap";
//comment these two when using jes_new_branch or staging
import "../sass/app.scss";
import "../css/app.css";
//
import { createApp } from "vue";
import axios from "axios";
import Router from "@/router";
import store from "@/store";

import moshaToast from "mosha-vue-toastify";
import "mosha-vue-toastify/dist/style.css";

import VueSweetalert2 from "vue-sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

import moment from "moment";
import Particles from "vue3-particles";
import VueProgressBar from "@aacassandra/vue3-progressbar";
import print from "vue3-print-nb";

import Shimmer from "vue3-shimmer";

import { vue3Debounce } from "vue-debounce";

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import "animate.css";

const options = {
    color: "rgb(143, 255, 199)",
    failedColor: "#874b4b",
    thickness: "3px",
    transition: {
        speed: "0.5s",
        opacity: "0.6s",
        termination: 300,
    },
    autoRevert: true,
    location: "top",
    inverse: false,
};

const app = createApp({});
app.config.globalProperties.$axios = axios;
app.config.globalProperties.$moment = moment;
app.config.globalProperties.$eosUrl = "http://eos.test"; //change depending on local/stagin/prod url
app.use(Router);
app.use(store);
app.use(Particles);
app.use(Shimmer);
app.use(print);
app.directive("uppercase", {
    update(el) {
        el.value = el.value.toUpperCase();
    },
});
app.directive("debounce", vue3Debounce({ lock: true }));

app.use(VueSweetalert2);
app.use(moshaToast);
app.use(VueProgressBar, options);
app.component("v-select", vSelect);

window.Swal = app.config.globalProperties.$swal;

app.config.globalProperties.$filters = {
    dateFormat(value) {
        return moment(String(value)).format("MM/DD/YYYY LTS");
    },
    capitalize(value) {
        const words = value.split(" ");
        return words
            .map((word) => {
                return word[0].toUpperCase() + word.substring(1);
            })
            .join(" ");
    },
    removeQuotes(value) {
        return value.replace(/["]+/g, "");
    },
    formatAmount(num, num_decimals, include_comma) {
        return num.toLocaleString("en-US", {
            useGrouping: include_comma,
            minimumFractionDigits: num_decimals,
            maximumFractionDigits: num_decimals,
        });
    },
};

app.mount("#app");
