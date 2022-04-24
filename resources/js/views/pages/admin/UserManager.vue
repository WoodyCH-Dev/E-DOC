<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>จัดการผู้ใช้งาน</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl12 xs12" align="right">
              <va-button
                icon="add"
                class="mr-2"
                color="primary"
                data-bs-toggle="modal"
                data-bs-target="#AddUserModal"
              >
                เพิ่มผู้ใช้
              </va-button>
              <va-button
                icon="description"
                class="mr-2"
                style="background-color: rgb(47, 148, 91)"
                data-bs-toggle="modal"
                data-bs-target="#AddUserViaExcelModal"
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
                    <tbody v-if="!data.AllUser_isLoad">
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
                          <va-button
                            icon="edit"
                            class="mr-2"
                            color="warning"
                            data-bs-toggle="modal"
                            data-bs-target="#EditUserModal"
                            v-on:click="EditUser(user.user.id)"
                          >
                            แก้ไข
                          </va-button>
                          <va-button icon="delete" class="mr-2" color="danger">
                            ลบ
                          </va-button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div
                  align="center"
                  style="padding-top: 30px"
                  v-if="data.AllUser_isLoad"
                >
                  <va-progress-circle
                    size="large"
                    :thickness="0.4"
                    color="primary"
                    indeterminate
                  />
                  กำลังโหลดข้อมูล
                </div>
              </div>
            </div>
          </div>
        </va-card-content>
      </va-card>
    </div>
  </div>

  <!-- Modal -->
  <div
    class="modal fade"
    id="AddUserModal"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">เพิ่มผู้ใช้งาน</h5>
          <button
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            class="btn"
          >
            <i class="far fa-times"></i>
          </button>
        </div>
        <div class="modal-body">...</div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
          >
            ปิด
          </va-button>
          <va-button icon="add" class="mr-1" color="primary">
            เพิ่มข้อมูล
          </va-button>
        </div>
      </div>
    </div>
  </div>
  <div
    class="modal fade"
    id="AddUserViaExcelModal"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">เพิ่มผู้ใช้งานด้วย Excel</h5>
          <button
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            class="btn"
          >
            <i class="far fa-times"></i>
          </button>
        </div>
        <div class="modal-body">...</div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
          >
            ปิด
          </va-button>
          <va-button
            icon="add"
            class="mr-1"
            style="background-color: rgb(47, 148, 91)"
          >
            เพิ่มข้อมูล
          </va-button>
        </div>
      </div>
    </div>
  </div>
  <div
    class="modal fade"
    id="EditUserModal"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">แก้ไขผู้ใช้</h5>
          <button
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            class="btn"
          >
            <i class="far fa-times"></i>
          </button>
        </div>
        <div class="modal-body">...</div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
          >
            ปิด
          </va-button>
          <va-button icon="save" class="mr-1" color="warning">
            บันทึก
          </va-button>
        </div>
      </div>
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
        AllUser_isLoad: true,
        Edit_user_select: 0,
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
          this.data.AllUser_isLoad = false;
          this.data.AllUser = res.data.users;
        } else {
          this.$swal.fire(
            "Error!",
            "Permission ของคุณไม่สามารถเข้าถึงได้",
            "error"
          );
        }
      });
    },

    EditUser(user_id) {
      this.data.Edit_user_select = user_id;
    },
  },
};
</script>
