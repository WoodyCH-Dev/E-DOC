// Page Components
import Home from "./views/pages/Home";
import About from "./views/pages/About";
import Error404 from "./views/pages/errors/404";
import Dashboard from "./views/pages/user/Dashboard";

export default [
    { path: "/", component: Home, name: "Home" },
    { path: "/about", component: About, name: "About" },
    { path: "/user/dashboard", component: Dashboard, name: "Dashboard" },
    { path: "/:pathMatch(.*)*", component: Error404 },
];
