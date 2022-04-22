<style>
.sticky-offset {
  top: 56px;
}

#body-row {
  margin-left: 0;
  margin-right: 0;
}

#sidebar-container {
  min-height: 100vh;
  background-color: #333;
  padding: 0;
}

/* Sidebar sizes when expanded and expanded */
.sidebar-expanded {
  width: 230px;
}
.sidebar-collapsed {
  width: 60px;
}

/* Menu item*/
#sidebar-container .list-group a {
  height: 50px;
  color: white;
}

/* Submenu item*/
#sidebar-container .list-group .sidebar-submenu a {
  height: 45px;
  padding-left: 30px;
}
.sidebar-submenu {
  font-size: 0.9rem;
}

/* Separators */
.sidebar-separator-title {
  background-color: #333;
  height: 35px;
  border-radius: 0 !important;
}
.sidebar-separator {
  background-color: #333;
  height: 25px;
}
.logo-separator {
  background-color: #333;
  height: 60px;
}

/* Closed submenu icon */
#sidebar-container
  .list-group
  .list-group-item[aria-expanded="false"]
  .submenu-icon::after {
  content: " \f0d7";
  font-family: FontAwesome;
  display: inline;
  text-align: right;
  padding-left: 10px;
}
/* Opened submenu icon */
#sidebar-container
  .list-group
  .list-group-item[aria-expanded="true"]
  .submenu-icon::after {
  content: " \f0da";
  font-family: FontAwesome;
  display: inline;
  text-align: right;
  padding-left: 10px;
}
</style>
<template>
  <!-- Bootstrap NavBar -->
  <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <button
      class="navbar-toggler navbar-toggler-right"
      type="button"
      data-toggle="collapse"
      data-target="#navbarNavDropdown"
      aria-controls="navbarNavDropdown"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <router-link class="navbar-brand" to="/">
        <img
          src="images/favicon.png"
          height="30"
          class="d-inline-block align-top"
          alt=""
        />
        ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)
      </router-link>
      <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item" v-if="data.isLogin == false">
          <router-link to="/" class="router-link">
            <a class="nav-link"><i class="fas fa-home"></i> หน้าแรก </a>
          </router-link>
        </li>
        <li class="nav-item" v-if="data.isLogin == false">
          <router-link to="/about" class="router-link">
            <a class="nav-link">
              <i class="far fa-info-circle"></i> เกี่ยวกับ
            </a>
          </router-link>
        </li>
      </ul>

      <ul class="navbar-nav my-2 my-lg-0">
        <li class="nav-item" v-if="data.isLogin == true">
          <a class="nav-link">
            <i class="fas fa-user"></i> {{ data.username }}
          </a>
        </li>
        <li class="nav-item" v-if="data.isLogin == true">
          <a class="nav-link" v-on:click="Logout()">
            <i class="fas fa-sign-out"></i> ออกจากระบบ
          </a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- NavBar END -->
  <div class="row" id="body-row">
    <div
      v-if="data.isLogin == true"
      id="sidebar-container"
      class="sidebar-expanded d-none d-md-block col-2"
    >
      <ul class="list-group sticky-top sticky-offset" style="padding-top: 56px">
        <!-- Separator with title -->
        <li
          class="
            list-group-item
            sidebar-separator-title
            text-muted
            d-flex
            align-items-center
            menu-collapsed
          "
        >
          <small>เมนู</small>
        </li>
        <!-- /END Separator -->
        <router-link
          to="/user/dashboard"
          class="bg-dark list-group-item list-group-item-action"
          v-if="
            permission.access_user == true || permission.access_admin == true
          "
        >
          <div class="d-flex w-100 justify-content-start align-items-center">
            <i class="far fa-tachometer mr-3"></i>
            <span class="menu-collapsed">Dashboard</span>
          </div>
        </router-link>
        <router-link
          to="/user/inbox"
          class="bg-dark list-group-item list-group-item-action"
          v-if="
            permission.access_user == true || permission.access_admin == true
          "
        >
          <div class="d-flex w-100 justify-content-start align-items-center">
            <i class="fas fa-inbox-in mr-3"></i>
            <span class="menu-collapsed">กล่องเอกสารเข้า</span>
          </div>
        </router-link>
        <router-link
          to="/sender/send"
          class="bg-dark list-group-item list-group-item-action"
          v-if="
            permission.access_sender == true || permission.access_admin == true
          "
        >
          <div class="d-flex w-100 justify-content-start align-items-center">
            <i class="fas fa-paper-plane mr-3"></i>
            <span class="menu-collapsed">ส่งเอกสาร</span>
          </div>
        </router-link>
        <router-link
          to="/sender/send/list"
          class="bg-dark list-group-item list-group-item-action"
          v-if="
            permission.access_sender == true || permission.access_admin == true
          "
        >
          <div class="d-flex w-100 justify-content-start align-items-center">
            <i class="fas fa-clipboard-list-check mr-3"></i>
            <span class="menu-collapsed">รายการที่ส่ง</span>
          </div>
        </router-link>
        <!-- Separator with title -->
        <li
          class="
            list-group-item
            sidebar-separator-title
            text-muted
            d-flex
            align-items-center
            menu-collapsed
          "
        >
          <small>เมนูของแอดมิน</small>
        </li>
        <!-- /END Separator -->
        <router-link
          to="/admin/manage/user"
          class="bg-dark list-group-item list-group-item-action"
          v-if="permission.access_admin == true"
        >
          <div class="d-flex w-100 justify-content-start align-items-center">
            <i class="fas fa-user-cog mr-3"></i>
            <span class="menu-collapsed">จัดการผู้ใช้</span>
          </div>
        </router-link>
        <router-link
          to="/admin/manage/document"
          class="bg-dark list-group-item list-group-item-action"
          v-if="permission.access_admin == true"
        >
          <div class="d-flex w-100 justify-content-start align-items-center">
            <i class="fad fa-file-alt mr-3"></i>
            <span class="menu-collapsed">จัดการเอกสาร</span>
          </div>
        </router-link>
        <router-link
          to="/admin/manage/category/document"
          class="bg-dark list-group-item list-group-item-action"
          v-if="permission.access_admin == true"
        >
          <div class="d-flex w-100 justify-content-start align-items-center">
            <i class="fad fa-books mr-3"></i>
            <span class="menu-collapsed">จัดการหมวดหมู่เอกสาร</span>
          </div>
        </router-link>
        <router-link
          to="/admin/manage/system"
          class="bg-dark list-group-item list-group-item-action"
          v-if="permission.access_admin == true"
        >
          <div class="d-flex w-100 justify-content-start align-items-center">
            <i class="fas fa-cog mr-3"></i>
            <span class="menu-collapsed">จัดการระบบ</span>
          </div>
        </router-link>
      </ul>
    </div>

    <!-- MAIN -->
    <div class="col py-3">
      <div class="content" style="padding-top: 56px">
        <div class="layout gutter--sm center">
          <router-view></router-view>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    var isLogin = false;
    var username = "";
    var permission = [];

    var access_user = false;
    var access_sender = false;
    var access_admin = false;

    if (window.localStorage.getItem("user_id")) {
      username = window.localStorage.getItem("name");
      isLogin = true;

      permission = window.localStorage.getItem("permission");
      if (permission.includes("admin")) access_user = true;
      if (permission.includes("sender")) access_sender = true;
      if (permission.includes("user")) access_admin = true;
    }

    return {
      data: { isLogin: isLogin, username: username },
      permission: {
        access_user: access_user,
        access_sender: access_sender,
        access_admin: access_admin,
      },
    };
  },
  methods: {
    Logout() {
      window.localStorage.clear();
      window.location.reload();
    },
  },
};
</script>
