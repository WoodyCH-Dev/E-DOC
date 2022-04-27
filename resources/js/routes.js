// Page Components
import Home from "./views/pages/Home";
import About from "./views/pages/About";

//User
import Dashboard from "./views/pages/user/Dashboard";
import Inbox from "./views/pages/user/Inbox";
import OpenInbox from "./views/pages/user/OpenInbox";

//Sender
import Sender from "./views/pages/sender/Sender";
import SenderList from "./views/pages/sender/SenderList";
import SenderEdit from "./views/pages/sender/SenderEdit";

//Admin
import UserManager from "./views/pages/admin/UserManager";
import UserGroupManager from "./views/pages/admin/UserGroupManager";
import DocumentManager from "./views/pages/admin/DocumentManager";
import DocumentManagerEdit from "./views/pages/admin/DocumentManagerEdit";
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
    {
        path: "/user/view/:document_id",
        component: OpenInbox,
        name: "OpenInbox",
    },
    //Sender
    { path: "/sender/send", component: Sender, name: "Send" },
    { path: "/sender/send/list", component: SenderList, name: "SendList" },
    {
        path: "/sender/send/edit/:document_id",
        component: SenderEdit,
        name: "SenderEdit",
    },
    //Admin
    { path: "/admin/manage/user", component: UserManager, name: "ManagedUser" },
    {
        path: "/admin/manage/user/group",
        component: UserGroupManager,
        name: "ManagedUserGroup",
    },
    {
        path: "/admin/manage/document",
        component: DocumentManager,
        name: "ManagedDocument",
    },
    {
        path: "/admin/manage/editdocument/:document_id",
        component: DocumentManagerEdit,
        name: "DocumentManagerEdit",
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
