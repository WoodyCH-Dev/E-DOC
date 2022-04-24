<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>จัดการผู้ใช้งาน</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl12 xs12">
              <div class="flex xl12 xs12" align="right">
                <va-button icon="add" class="mr-2" color="primary">
                  เพิ่มผู้ใช้
                </va-button>
                <va-button
                  icon="description"
                  class="mr-2"
                  style="background-color: rgb(47, 148, 91)"
                >
                  Import ด้วย Excel
                </va-button>
              </div>
              <div class="flex xl12 xs12">
                <div class="form-group">
                  <div class="va-table-responsive" style="overflow-y: auto">
                    <table class="va-table" style="width: 100%">
                      <thead>
                        <tr>
                          <th>ลำดับ</th>
                          <th>ชื่อ</th>
                          <th>นามสกุล</th>
                          <th>E-mail</th>
                          <th>Group</th>
                          <th>จัดการ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(user, index) in data.AllUser"
                          :key="user.user.id"
                        >
                          <td>{{ index + 1 }}</td>
                          <td>{{ user.user.name }}</td>
                          <td>{{ user.user.lastname }}</td>
                          <td>
                            {{ user.user.email }}
                            <i
                              class="fas fa-check-circle"
                              v-if="user.user.google_uid"
                            ></i>
                          </td>
                          <td>
                            <label
                              v-for="(usergroup, index) in user.user_group"
                              :key="usergroup.id"
                            >
                              <label v-if="index > 0">, </label>
                              {{ usergroup.group_name }}
                            </label>
                          </td>
                          <td>
                            <va-button icon="edit" class="mr-2" color="warning">
                              แก้ไข
                            </va-button>
                            <va-button
                              icon="delete"
                              class="mr-2"
                              color="danger"
                            >
                              ลบ
                            </va-button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </va-card-content>
      </va-card>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    var username = "";
    var lastname = "";
    var permission = [];

    var access_user = false;
    var access_sender = false;
    var access_admin = false;

    var acd_year = "0";

    if (window.localStorage.getItem("user_id")) {
      username = window.localStorage.getItem("name");
      lastname = window.localStorage.getItem("lastname");
      permission = window.localStorage.getItem("permission");
      if (permission.includes("admin")) access_user = true;
      if (permission.includes("sender")) access_sender = true;
      if (permission.includes("user")) access_admin = true;
    } else {
      this.$router.push("/");
    }

    this.onLoad();

    return {
      data: {
        username: username,
        lastname: lastname,
        acd_year: acd_year,
        AllUser: new Array(),
      },
      permission: {
        access_user: access_user,
        access_sender: access_sender,
        access_admin: access_admin,
      },
    };
  },
  methods: {
    onLoad() {
      this.axios.get("api/user/acd_year").then((res) => {
        if (res.data.status == true) {
          this.data.acd_year = String(Number(res.data.acd_year) + 543);
        }
      });

      this.axios.get("api/admin/get/AllUser").then((res) => {
        if (res.data.status == true) {
          this.data.AllUser = res.data.users;
        }
      });
    },
  },
};
</script>
