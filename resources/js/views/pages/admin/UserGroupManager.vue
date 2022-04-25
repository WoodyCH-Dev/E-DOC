<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>จัดการกลุ่มผู้ใช้งาน</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl4 xs12">
              <div class="flex xl12 xs12" align="right">
                <va-button
                  icon="add"
                  class="mr-2"
                  style="background-color: rgb(47, 148, 91)"
                  v-on:click="CreateGroup()"
                  >เพิ่มกลุ่มผู้ใช้งาน</va-button
                >
              </div>
              <div class="flex xl12 xs12">
                <div class="form-group">
                  <va-sidebar textColor="dark" style="width: 100%">
                    <va-sidebar-item
                      class="sidebar-item"
                      v-for="user_group in user_group_lists"
                      :key="user_group.id"
                    >
                      <va-sidebar-item-content
                        @click="
                          GetUserInGroup(user_group.id, user_group.group_name)
                        "
                      >
                        <i class="fad fa-users"></i>
                        <va-sidebar-item-title>
                          {{ user_group.group_name }}
                        </va-sidebar-item-title>
                        <va-button
                          icon="edit"
                          size="medium"
                          color="warning"
                          v-on:click="
                            EditGroup(user_group.id, user_group.group_name)
                          "
                        />
                        <va-button
                          icon="delete"
                          size="medium"
                          color="danger"
                          v-on:click="RemoveGroup(user_group.id)"
                        />
                      </va-sidebar-item-content>
                    </va-sidebar-item>
                  </va-sidebar>
                </div>
              </div>
            </div>
            <div class="flex xl8 xs12">
              <div class="flex xl12 xs12" align="right">
                <va-button
                  icon="edit"
                  class="mr-2"
                  color="warning"
                  data-bs-toggle="modal"
                  data-bs-target="#AddUserInGroupModal"
                  v-if="select_group"
                >
                  แก้ไขผู้ใช้ในกลุ่ม
                </va-button>
              </div>
              <div class="flex xl12 xs12" align="center">
                <b v-if="!select_group"> -- ยังไม่ได้เลือกกลุ่มผู้ใช้ -- </b>
                <div class="form-group">
                  <div class="va-table-responsive" style="overflow-y: auto">
                    <table
                      class="va-table"
                      style="width: 100%"
                      v-if="select_group && !user_ingroup_lists_load"
                    >
                      <thead>
                        <tr>
                          <th>ลำดับ</th>
                          <th>ชื่อ</th>
                          <th>นามสกุล</th>
                          <th>E-mail</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(user, index) in user_ingroup_lists"
                          :key="user.id"
                        >
                          <td>{{ index + 1 }}</td>
                          <td>{{ user.name }}</td>
                          <td>{{ user.lastname }}</td>
                          <td>
                            {{ user.email }}
                            <i
                              class="fas fa-check-circle"
                              v-if="user.google_uid"
                            ></i>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div
                    align="center"
                    style="padding-top: 30px"
                    v-if="select_group && user_ingroup_lists_load"
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
          </div>
        </va-card-content>
      </va-card>
    </div>
  </div>

  <!-- Modal -->
  <div
    class="modal fade"
    id="AddUserInGroupModal"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            เพิ่มผู้ใช้งานลงในกลุ่ม {{ select_group_name }}
          </h5>
          <button
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            class="btn"
          >
            <i class="far fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <b>รายชื่อผู้ใช้ทั้งหมด</b>
          <va-input
            class="xs12 md12"
            placeholder="Filter... ชื่อ นามสกุล E-mail"
            v-model="add_user_ingroup_lists_filter"
          />
          <div class="va-table-responsive" style="overflow-y: auto">
            <table
              class="va-table va-table--hoverable"
              style="width: 100%"
              v-if="select_group"
            >
              <thead>
                <tr>
                  <th>ลำดับ</th>
                  <th>ชื่อ</th>
                  <th>นามสกุล</th>
                  <th>E-mail</th>
                  <th style="text-align: right"></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(user, index) in add_user_ingroup_lists_filteredRows"
                  :key="user.id"
                >
                  <td>{{ index + 1 }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.lastname }}</td>
                  <td>{{ user.email }}</td>
                  <td><va-checkbox v-model="user.select" /></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
            id="AddUserInGrouClosepModal"
          >
            ปิด
          </va-button>
          <va-button
            icon="save"
            class="mr-1"
            style="background-color: rgb(47, 148, 91)"
            v-on:click="SubmitAddUserInGroup()"
          >
            บันทึกข้อมูล
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
        Allgroup_isLoad: true,
      },
      permission: {
        access_user: access_user,
        access_sender: access_sender,
        access_admin: access_admin,
      },
      user_group_lists: new Array(),
      select_group: 0,
      select_group_name: "",
      user_ingroup_lists: new Array(),
      user_ingroup_lists_load: true,
      userid_ingroup_lists: [],
      add_user_ingroup_lists: new Array(),
      add_user_ingroup_lists_table: new Array(),
      add_user_ingroup_lists_filter: "",
    };
  },
  methods: {
    onLoad() {
      this.axios.get("api/user/acd_year").then((res) => {
        if (res.data.status == true) {
          this.data.acd_year = String(Number(res.data.acd_year) + 543);
        }
      });

      this.axios.get("api/admin/get/AllGroup").then((res) => {
        if (res.data.status == true) {
          this.data.Allgroup_isLoad = false;
          this.user_group_lists = res.data.group;
        } else {
          this.$swal.fire(
            "Error!",
            "Permission ของคุณไม่สามารถเข้าถึงได้",
            "error"
          );
        }
      });

      if (this.select_group && this.select_group_name) {
        this.GetUserInGroup(this.select_group, this.select_group_name);
      }
    },

    CreateGroup() {
      this.$swal
        .fire({
          title: "ชื่อ Group ที่ต้องการสร้าง",
          input: "text",
          showCancelButton: true,
          confirmButtonText: "สร้าง",
          reverseButtons: true,
          showLoaderOnConfirm: true,
          cancelButtonColor: "#3085d6",
          cancelButtonText: "ยกเลิก",
          confirmButtonColor: "rgb(47, 148, 91)",
        })
        .then((result) => {
          if (result.isConfirmed) {
            if (result.value != "" && result.value != null) {
              this.axios
                .post("api/admin/create/Group", {
                  group_name: result.value,
                })
                .then((res) => {
                  this.$swal.fire(
                    "Success!",
                    "ทำการสร้างกลุ่มผู้ใช้ " + result.value + " แล้ว",
                    "success"
                  );
                  this.onLoad();
                });
            } else {
              this.$swal.fire(
                "Error!",
                "ไม่สารมารถเว้นการใส่ชื่อกลุ่มได้",
                "error"
              );
            }
          }
        });
    },

    EditGroup(group_id, group_name) {
      this.$swal
        .fire({
          title: "ชื่อ Group ที่ต้องเปลี่ยนชื่อ",
          html: "Group ที่เลือก: " + group_name,
          input: "text",
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonText: "แก้ไข",
          showLoaderOnConfirm: true,
          cancelButtonText: "ยกเลิก",
          cancelButtonColor: "#3085d6",
          confirmButtonColor: "rgb(255, 184, 61)",
        })
        .then((result) => {
          if (result.isConfirmed) {
            if (result.value != "" && result.value != null) {
              this.axios
                .post("api/admin/edit/Group", {
                  id: group_id,
                  group_name: result.value,
                })
                .then((res) => {
                  this.$swal.fire(
                    "Success!",
                    "ทำการสร้างกลุ่มผู้ใช้ " + result.value + " แล้ว",
                    "success"
                  );
                  this.onLoad();
                });
            } else {
              this.$swal.fire(
                "Error!",
                "ไม่สารมารถเว้นการใส่ชื่อกลุ่มได้",
                "error"
              );
            }
          }
        });
    },

    RemoveGroup(group_id) {
      this.$swal
        .fire({
          title: "แจ้งเตือน!",
          html: "คุณแน่ใจหรือไม่ที่จะลบ Group นี้",
          reverseButtons: true,
          showCancelButton: true,
          confirmButtonText: "ลบ",
          showLoaderOnConfirm: true,
          cancelButtonText: "ยกเลิก",
          cancelButtonColor: "#3085d6",
          confirmButtonColor: "rgb(235, 64, 52)",
        })
        .then((result) => {
          if (result.isConfirmed) {
            if (result.value != "" && result.value != null) {
              this.axios
                .post("api/admin/remove/Group", {
                  id: group_id,
                })
                .then((res) => {
                  this.$swal.fire(
                    "Success!",
                    "ทำการสร้างกลุ่มผู้ใช้ " + result.value + " แล้ว",
                    "success"
                  );
                  this.onLoad();
                });
            } else {
              this.$swal.fire(
                "Error!",
                "ไม่สารมารถเว้นการใส่ชื่อกลุ่มได้",
                "error"
              );
            }
          }
        });
    },

    GetUserInGroup(group_id, group_name) {
      this.user_ingroup_lists_load = true;
      this.select_group = group_id;
      this.select_group_name = group_name;
      this.user_ingroup_lists = new Array();
      this.userid_ingroup_lists = new Array();
      this.add_user_ingroup_lists_table = new Array();
      this.axios
        .get("api/admin/get/Group/AllUser/" + this.select_group)
        .then(async (res) => {
          if (res.data.status == true) {
            this.user_ingroup_lists = res.data.users;
            for await (let user of res.data.users) {
              this.userid_ingroup_lists.push(user.user_id);
            }

            this.axios.get("api/admin/get/AllUser").then((res) => {
              if (res.data.status == true) {
                for (let user of res.data.users) {
                  this.add_user_ingroup_lists_table.push({
                    id: user.user.id,
                    name: user.user.name,
                    lastname: user.user.lastname,
                    email: user.user.email,
                    select: this.userid_ingroup_lists.includes(user.user.id),
                  });
                }
                this.user_ingroup_lists_load = false;
              } else {
                this.$swal.fire(
                  "Error!",
                  "Permission ของคุณไม่สามารถเข้าถึงได้",
                  "error"
                );
              }
            });
          }
        });
    },

    SubmitAddUserInGroup() {
      if (this.add_user_ingroup_lists_table.length > 0) {
        var counter = 0;
        this.$swal.fire({
          title: "กำลังบันทึกข้อมูล กรุณารอสักครู่",
          allowOutsideClick: false,
          showConfirmButton: false,
        });
        for (let user of this.add_user_ingroup_lists_table) {
          this.axios
            .post("api/admin/edit/Group/User", {
              user_id: user.id,
              group_id: this.select_group,
              type: user.select,
            })
            .then((res) => {
              if (res.data.status == true) {
                counter++;
                if (counter == this.add_user_ingroup_lists_table.length) {
                  this.$swal.fire(
                    "Success!",
                    "แก้ไขข้อมูลสำเร็จแล้ว!",
                    "success"
                  );
                  this.onLoad();
                  document.getElementById("AddUserInGrouClosepModal").click();
                }
              }
            });
        }
      }
    },
  },
  computed: {
    add_user_ingroup_lists_filteredRows() {
      return this.add_user_ingroup_lists_table.filter((element) => {
        const name = element.name.toLowerCase();
        const lastname = element.lastname.toLowerCase();
        const email = element.email.toLowerCase();
        const search = this.add_user_ingroup_lists_filter.toLowerCase();

        return (
          name.includes(search) ||
          lastname.includes(search) ||
          email.includes(search)
        );
      });
    },
  },
};
</script>
