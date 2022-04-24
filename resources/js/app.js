//-------------- IMPORT ALL LIBRARY MODULES ------------------//
import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
//--------------- END ALL LIBRARY MODULES ------------------//

// Routes
import routes from "./routes";

// Main App Component
import App from "./views/layouts/App";

// Router instance
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Init App
const app = createApp(App);

//Import Module
import { VuesticPlugin } from "vuestic-ui";
import "vuestic-ui/dist/vuestic-ui.css";
import axios from "axios";
import VueAxios from "vue-axios";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

if (window.localStorage.getItem("access_token")) {
    axios.interceptors.request.use(
        (config) => {
            const token = window.localStorage.getItem("access_token");

            if (token) {
                config.headers["Authorization"] = `Bearer ${token}`;
            }

            return config;
        },

        (error) => {
            return Promise.reject(error);
        }
    );
}

// Init router into app
app.use(router);
app.use(VueAxios, axios);

//Init Module
app.use(VuesticPlugin);

// Mount the App
app.mount("#app");
