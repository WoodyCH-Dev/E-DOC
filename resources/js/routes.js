// Page Components
import Home from "./views/pages/Home";
import About from "./views/pages/About";

//User
import Dashboard from "./views/pages/user/Dashboard";
import Inbox from "./views/pages/user/Inbox";

//Sender
import Sender from "./views/pages/sender/Sender";
import SenderList from "./views/pages/sender/SenderList";

//Admin
import UserManager from "./views/pages/admin/UserManager";
import DocumentManager from "./views/pages/admin/DocumentManager";
import DocumentCategoryManager from "./views/pages/admin/DocumentCategoryManager";
import SystemManager from "./views/pages/admin/SystemManager";

//Error
import Error404 from "./views/pages/errors/404";

export default [
    { path: "/", component: Home, name: "Home" },
    { path: "/about", component: About, name: "About" },
    //User
    { path: "/user/dashboard", component: Dashboard, name: "Dashboard" },
    { path: "/user/inbox", component: Inbox, name: "Inbox" },
    //Sender
    { path: "/sender/send", component: Sender, name: "Send" },
    { path: "/sender/send/list", component: SenderList, name: "SendList" },
    //Admin
    { path: "/admin/manage/user", component: UserManager, name: "ManagedUser" },
    {
        path: "/admin/manage/document",
        component: DocumentManager,
        name: "ManagedDocument",
    },
    {
        path: "/admin/manage/category/document",
        component: DocumentCategoryManager,
        name: "ManagedDocumentCategory",
    },
    {
        path: "/admin/manage/system",
        component: SystemManager,
        name: "ManagedSystem",
    },
    //Error
    { path: "/:pathMatch(.*)*", component: Error404 },
];
