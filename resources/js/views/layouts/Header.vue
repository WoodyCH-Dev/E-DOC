<template>
  <div class="data-content">
    <va-navbar color="#003153" shape>
      <template #left>
        <va-navbar-item>
          <router-link to="/">
            <img src="images/logo.png" class="header-logo" />
          </router-link>
        </va-navbar-item>
      </template>

      <template #right>
        <va-navbar-item v-if="data.isLogin == false">
          <router-link to="/"><i class="fas fa-home"></i> หน้าแรก </router-link>
        </va-navbar-item>
        <va-navbar-item v-if="data.isLogin == false">
          <router-link to="/about">
            <i class="far fa-info-circle"></i> เกี่ยวกับ
          </router-link>
        </va-navbar-item>
        <va-navbar-item v-if="data.isLogin == true" class="nav-item">
          <i class="fas fa-user"></i> {{ data.username }}
        </va-navbar-item>
        <va-navbar-item
          v-if="data.isLogin == true"
          v-on:click="Logout()"
          class="nav-item"
        >
          <i class="fas fa-sign-out"></i> ออกจากระบบ
        </va-navbar-item>
      </template>
    </va-navbar>

    <div style="height: 100%; position: relative">
      <va-sidebar
        textColor="dark"
        style="position: absolute"
        v-if="data.isLogin == true"
      >
        <router-link to="/user/dashboard">
          <va-sidebar-item active>
            <va-sidebar-item-content>
              <va-icon name="dashboard" />
              <va-sidebar-item-title> Dashboard </va-sidebar-item-title>
            </va-sidebar-item-content>
          </va-sidebar-item>
        </router-link>
        <va-sidebar-item>
          <va-sidebar-item-content>
            <va-icon name="move_to_inbox" />
            <va-sidebar-item-title> กล่องเอกสารเข้า </va-sidebar-item-title>
          </va-sidebar-item-content>
        </va-sidebar-item>
        <va-sidebar-item>
          <va-sidebar-item-content>
            <va-icon name="list_alt" />
            <va-sidebar-item-title> รายการที่ส่ง </va-sidebar-item-title>
          </va-sidebar-item-content>
        </va-sidebar-item>
        <va-sidebar-item>
          <va-sidebar-item-content>
            <va-icon name="send" />
            <va-sidebar-item-title> ส่งเอกสาร </va-sidebar-item-title>
          </va-sidebar-item-content>
        </va-sidebar-item>

        <va-divider orientation="left" style="padding-top: 30px">
          <span class="px-2">เมนูของแอดมิน</span>
        </va-divider>
        <va-sidebar-item>
          <va-sidebar-item-content>
            <va-icon name="manage_accounts" />
            <va-sidebar-item-title> จัดการผู้ใช้ </va-sidebar-item-title>
          </va-sidebar-item-content>
        </va-sidebar-item>
        <va-sidebar-item>
          <va-sidebar-item-content>
            <va-icon name="settings" />
            <va-sidebar-item-title> จัดการเอกสาร </va-sidebar-item-title>
          </va-sidebar-item-content>
        </va-sidebar-item>
        <va-sidebar-item>
          <va-sidebar-item-content>
            <va-icon name="room_preferences" />
            <va-sidebar-item-title>
              จัดการหมวดหมู่เอกสาร
            </va-sidebar-item-title>
          </va-sidebar-item-content>
        </va-sidebar-item>
      </va-sidebar>

      <div class="content" style="padding-top: 20px; margin-left: 16rem">
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
