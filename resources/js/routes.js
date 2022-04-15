// Page Components
import Home from "./views/pages/Home";
import About from "./views/pages/About";
import Error404 from "./views/pages/errors/404";

export default [
    { path: "/", component: Home, name: 'Home' },
    { path: "/about", component: About, name: 'About' },
    { path: "/:pathMatch(.*)*", component: Error404 },
];
