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
                v-on:click="clearArray()"
              >
                เพิ่มผู้ใช้
              </va-button>
              <va-button
                icon="description"
                class="mr-0"
                style="background-color: rgb(47, 148, 91)"
                data-bs-toggle="modal"
                data-bs-target="#AddUserViaExcelModal"
                v-on:click="clearArray()"
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
                          <va-button
                            icon="manage_accounts"
                            class="mr-2"
                            style="background-color: rgb(50, 168, 82)"
                            data-bs-toggle="modal"
                            data-bs-target="#EditUserPermissionModal"
                            v-if="user.user.id != data.user_id"
                            v-on:click="EditUserPermission(user.user.id)"
                          >
                            สิทธิ์
                          </va-button>
                          <va-button
                            icon="delete"
                            class="mr-2"
                            color="danger"
                            v-if="user.user.id != data.user_id"
                            @Click="
                              RemoveUser(
                                user.user.id,
                                user.user.name + ' ' + user.user.lastname
                              )
                            "
                          >
                            ลบ
                          </va-button>
                          <b v-if="user.user.id == data.user_id">
                            (ผู้ใช้งานปัจจุบัน)
                          </b>
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
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            เพิ่มผู้ใช้งาน (รองรับการเพิ่มหลายผู้ใช้งานในครั้งเดียว)
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
          <va-form ref="form" @validation="addUserForm.validation = $event">
            <div class="row">
              <div class="flex xl6 xs12">
                <div class="form-group">
                  <b>ชื่อ</b>
                  <va-input
                    class="mb-2"
                    v-model="addUserForm.name"
                    :rules="[(value) => value != '' || 'กรุณาใส่ชื่อ']"
                    required
                  />
                </div>
              </div>
              <div class="flex xl6 xs12">
                <div class="form-group">
                  <b>นามสกุล</b>
                  <va-input
                    class="mb-2"
                    v-model="addUserForm.lastname"
                    :rules="[(value) => value != '' || 'กรุณาใส่นามสกุล']"
                    required
                  />
                </div>
              </div>
              <div class="flex xl8 xs12">
                <div class="form-group">
                  <b>E-mail</b>
                  <va-input
                    class="mb-2"
                    type="email"
                    v-model="addUserForm.email"
                    :rules="[(value) => value != '' || 'กรุณาใส่ E-mail']"
                    required
                  />
                </div>
              </div>
              <div class="flex xl4 xs12">
                <div class="form-group">
                  <b>Password (หากเว้นไว้ Password คือ password)</b>
                  <va-input
                    class="mb-2"
                    type="password"
                    v-model="addUserForm.password"
                    required
                  />
                </div>
              </div>
              <div class="flex xl12 xs12" align="right">
                <va-button
                  icon="add"
                  class="mr-1"
                  color="primary"
                  type="submit"
                  @click="$refs.form.validate() && AddUserToArray()"
                >
                  เพิ่ม
                </va-button>
              </div>
            </div>
          </va-form>
          <div class="row">
            <div class="flex xl12 xs12">
              <div class="va-table-responsive" style="overflow-y: auto">
                <table class="va-table" style="width: 100%">
                  <thead>
                    <tr>
                      <th>ชื่อ</th>
                      <th>นามสกุล</th>
                      <th>E-mail</th>
                      <th>Password</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(newUser, index) in data.newUser_Array"
                      :key="newUser"
                    >
                      <td>{{ newUser.name }}</td>
                      <td>{{ newUser.lastname }}</td>
                      <td>{{ newUser.email }}</td>
                      <td>{{ newUser.password }}</td>
                      <td width="10%">
                        <va-button
                          icon="close"
                          class="mr-1"
                          color="danger"
                          v-on:click="RemoveUserFromArray(index)"
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
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
            v-on:click="CloseAddDialog()"
            id="CloseAddUserModal"
          >
            ปิด
          </va-button>
          <va-button
            icon="save"
            class="mr-1"
            color="primary"
            v-on:click="AddUserSubmit()"
          >
            บันทึก
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
          <h5 class="modal-title">
            เพิ่มผู้ใช้งานด้วย Excel (ห้ามใช้ E-mail ซ้ำกัน , หาก password ว่าง
            password คือ password)
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
          <a
            href="uploads/UserImportEDOC.xlsx"
            class="nav-item"
            target="_blank"
          >
            <va-button class="mr-2" color="warning" icon="file_download">
              Download ไฟล์ Excel ต้นแบบสำหรับนำเข้า
            </va-button>
          </a>
          <va-button
            class="mr-2"
            color="primary"
            icon="open_in_browser"
            v-on:click="file_selectBtn()"
          >
            เปิดไฟล์
          </va-button>
          <input
            type="file"
            hidden
            id="SelectFileUploadBtn"
            @change="file_selectChange"
            multiple
            accept=".xls,.xlsx"
          />
          <hr />
          <table class="va-table" style="width: 100%">
            <thead>
              <tr>
                <th>ชื่อ</th>
                <th>นามสกุล</th>
                <th>E-mail</th>
                <th>Password</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(newUser, index) in data.newUser_Array" :key="newUser">
                <td>{{ newUser.name }}</td>
                <td>{{ newUser.lastname }}</td>
                <td>{{ newUser.email }}</td>
                <td>{{ newUser.password }}</td>
                <td width="10%">
                  <va-button
                    icon="close"
                    class="mr-1"
                    color="danger"
                    v-on:click="RemoveUserFromArray(index)"
                  >
                    ลบ
                  </va-button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
            id="CloseAddUserExcelModal"
          >
            ปิด
          </va-button>
          <va-button
            icon="save"
            class="mr-1"
            style="background-color: rgb(47, 148, 91)"
            :disabled="data.newUser_Array == 0"
            v-on:click="AddUserSubmit()"
          >
            บันทึกข้อมูล
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
        <div class="modal-body">
          <div class="row">
            <div class="flex xl6 xs12">
              <div class="form-group">
                <b>ชื่อ</b>
                <va-input class="mb-2" v-model="editUserForm.name" required />
              </div>
            </div>
            <div class="flex xl6 xs12">
              <div class="form-group">
                <b>นามสกุล</b>
                <va-input
                  class="mb-2"
                  v-model="editUserForm.lastname"
                  required
                />
              </div>
            </div>
            <div class="flex xl8 xs12">
              <div class="form-group">
                <b>E-mail</b>
                <va-input
                  class="mb-2"
                  type="email"
                  v-model="editUserForm.email"
                  required
                />
              </div>
            </div>
            <div class="flex xl4 xs12">
              <div class="form-group">
                <b>Password (หากเว้นไว้จะไม่ทำการเปลี่ยน Password)</b>
                <va-input
                  class="mb-2"
                  type="password"
                  v-model="editUserForm.password"
                  required
                />
              </div>
            </div>
            <div class="flex xl6 xs12">
              <b class="mr-1">สถานะการเชื่อมต่อบัญชี Google</b>
              <va-chip color="success" v-if="editUserForm.google_uid"
                >เชื่อมต่อแล้ว</va-chip
              >
              <va-chip color="danger" v-if="!editUserForm.google_uid"
                >ยังไม่เชื่อมต่อ</va-chip
              >
            </div>
            <div class="flex xl6 xs12" align="right">
              <va-button
                class="mr-1"
                color="danger"
                v-if="editUserForm.google_uid"
                @click="EditUserGoogleResetSubmit()"
              >
                <i class="fab fa-google mr-2"></i>
                ยกเลิกการ Sync Google
              </va-button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
            id="CloseEditUserModal"
          >
            ปิด
          </va-button>
          <va-button
            icon="save"
            class="mr-1"
            color="warning"
            v-on:click="EditUserSubmit()"
          >
            บันทึก
          </va-button>
        </div>
      </div>
    </div>
  </div>
  <div
    class="modal fade"
    id="EditUserPermissionModal"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">สิทธิ์การเข้าถึง</h5>
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
          <div class="row">
            <div class="flex xl4 xs12" align="center">
              <va-switch
                color="primary"
                class="mr-4"
                v-model="editUserPermissionForm.user"
                v-on:click="
                  EditUserPermissionSubmit(0) ||
                    $vaToast.init({
                      message: 'แก้ไขสิทธิ์การเข้าถึงของผู้ใช้แล้ว!',
                      color: 'primary',
                    })
                "
              >
                ผู้ใช้งานทั่วไป
              </va-switch>
            </div>
            <div class="flex xl4 xs12" align="center">
              <va-switch
                color="warning"
                class="mr-4"
                v-model="editUserPermissionForm.sender"
                v-on:click="
                  EditUserPermissionSubmit(1) ||
                    $vaToast.init({
                      message: 'แก้ไขสิทธิ์การเข้าถึงของผู้ใช้แล้ว!',
                      color: 'primary',
                    })
                "
              >
                ผู้ส่ง
              </va-switch>
            </div>
            <div class="flex xl4 xs12" align="center">
              <va-switch
                color="danger"
                class="mr-4"
                v-model="editUserPermissionForm.admin"
                v-on:click="
                  EditUserPermissionSubmit(2) ||
                    $vaToast.init({
                      message: 'แก้ไขสิทธิ์การเข้าถึงของผู้ใช้แล้ว!',
                      color: 'primary',
                    })
                "
              >
                แอดมิน
              </va-switch>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
            v-on:click="EditUserPermissionClose()"
          >
            ปิด
          </va-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as XLSX from "xlsx";

export default {
  data() {
    var user_id = 0;
    var username = "";
    var lastname = "";
    var permission = [];

    var access_user = false;
    var access_sender = false;
    var access_admin = false;

    var acd_year = "0";

    if (window.localStorage.getItem("user_id")) {
      user_id = Number(window.localStorage.getItem("user_id"));
      username = window.localStorage.getItem("name");
      lastname = window.localStorage.getItem("lastname");
      permission = window.localStorage.getItem("permission");
      if (permission.includes("admin")) access_user = true;
      if (permission.includes("sender")) access_sender = true;
      if (permission.includes("user")) access_admin = true;
      this.onLoad();
    } else {
      this.$router.push("/");
    }

    return {
      data: {
        user_id: user_id,
        username: username,
        lastname: lastname,
        acd_year: acd_year,
        AllUser: new Array(),
        AllUser_isLoad: true,
        Edit_user_select: 0,
        newUser_Array: new Array(),
      },
      addUserForm: {
        name: "",
        lastname: "",
        email: "",
        password: "",
        validation: null,
        file: null,
      },
      editUserForm: {
        id: 0,
        name: "",
        lastname: "",
        email: "",
        password: "",
        google_uid: "",
        validation: null,
      },
      editUserPermissionForm: {
        id: 0,
        user: false,
        sender: false,
        admin: false,
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

    clearArray() {
      this.data.newUser_Array = new Array();
      this.addUserForm.file = null;
    },

    file_selectBtn() {
      document.getElementById("SelectFileUploadBtn").click();
    },

    file_selectChange(event) {
      this.addUserForm.file = event.target.files ? event.target.files[0] : null;
      this.data.newUser_Array = new Array();
      if (this.addUserForm.file) {
        const reader = new FileReader();

        reader.onload = (e) => {
          /* Parse data */
          const bstr = e.target.result;
          const wb = XLSX.read(bstr, { type: "binary" });
          /* Get first worksheet */
          const wsname = wb.SheetNames[0];
          const ws = wb.Sheets[wsname];
          /* Convert array of arrays */
          const data = XLSX.utils.sheet_to_json(ws, { header: 2 });

          for (let user_add of data) {
            var password = user_add.password;
            if (password == "" || password == null) password = "password";
            this.data.newUser_Array.push({
              name: user_add.name,
              lastname: user_add.lastname,
              email: user_add.email,
              password: password,
            });
          }
        };

        reader.readAsBinaryString(this.addUserForm.file);
      }
    },

    AddUserToArray() {
      if (
        this.addUserForm.name != "" &&
        this.addUserForm.lastname != "" &&
        this.addUserForm.email != ""
      ) {
        var password = this.addUserForm.password;
        if (
          this.addUserForm.password == null ||
          this.addUserForm.password == ""
        )
          password = "password";

        this.data.newUser_Array.push({
          name: this.addUserForm.name,
          lastname: this.addUserForm.lastname,
          email: this.addUserForm.email,
          password: password,
        });
        this.addUserForm.name = "";
        this.addUserForm.lastname = "";
        this.addUserForm.email = "";
        this.addUserForm.password = "";
        this.addUserForm.validation = null;
      }
    },

    RemoveUserFromArray(array_id) {
      this.data.newUser_Array.splice(array_id, 1);
    },

    async AddUserSubmit() {
      if (this.data.newUser_Array.length > 0) {
        var counter = 0;
        this.$swal.fire({
          title: "กำลังเพิ่มข้อมูล กรุณารอสักครู่",
          allowOutsideClick: false,
          showConfirmButton: false,
        });
        for await (let newUser of this.data.newUser_Array) {
          await this.axios
            .post("api/admin/create/User", {
              name: newUser.name,
              lastname: newUser.lastname,
              email: newUser.email,
              password: newUser.password,
            })
            .then((res) => {
              if (res.data.status == true) {
                counter++;
                if (counter == this.data.newUser_Array.length) {
                  this.$swal.fire(
                    "Success!",
                    "เพิ่มข้อมูลสำเร็จแล้ว!",
                    "success"
                  );
                  this.onLoad();
                  document.getElementById("CloseAddUserModal").click();
                  document.getElementById("CloseAddUserExcelModal").click();
                }
              }
            });
        }
      }
    },

    CloseAddDialog() {
      this.addUserForm.validation = null;
      this.data.newUser_Array = new Array();
      this.addUserForm.name = "";
      this.addUserForm.lastname = "";
      this.addUserForm.email = "";
      this.addUserForm.password = "";
    },

    EditUser(user_id) {
      this.editUserForm.id = 0;
      this.editUserForm.name = "";
      this.editUserForm.lastname = "";
      this.editUserForm.email = "";
      this.editUserForm.password = "";
      this.editUserForm.google_uid = "";
      this.axios.get("api/admin/get/user/" + user_id).then((res) => {
        console.log(res.data);
        if (res.data.status == true) {
          this.editUserForm.id = res.data.userdata.id;
          this.editUserForm.name = res.data.userdata.name;
          this.editUserForm.lastname = res.data.userdata.lastname;
          this.editUserForm.email = res.data.userdata.email;
          this.editUserForm.google_uid = res.data.userdata.google_uid;
        }
      });
    },

    EditUserSubmit() {
      this.axios
        .post("api/admin/edit/User", {
          id: this.editUserForm.id,
          name: this.editUserForm.name,
          lastname: this.editUserForm.lastname,
          email: this.editUserForm.email,
          password: this.editUserForm.password,
        })
        .then((res) => {
          if (res.data.status == true) {
            this.$swal.fire("Success!", "แก้ไขข้อมูลสำเร็จแล้ว!", "success");
            this.editUserForm.id = 0;
            this.editUserForm.name = "";
            this.editUserForm.lastname = "";
            this.editUserForm.email = "";
            this.editUserForm.password = "";
            this.editUserForm.google_uid = "";
            this.onLoad();
            document.getElementById("CloseEditUserModal").click();
          }
        });
    },

    EditUserGoogleResetSubmit() {
      this.$swal
        .fire({
          title: "แจ้งเตือน!",
          text: "ยืนยันการยกเลิกการเชื่อมต่อบัญชี Google ",
          icon: "warning",
          showCancelButton: true,
          cancelButtonColor: "#3085d6",
          cancelButtonText: "ยกเลิก",
          confirmButtonColor: "rgb(235, 64, 52)",
          confirmButtonText: "ยืนยัน",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.axios
              .post("api/admin/edit/User/ResetSyncGoogle", {
                id: this.editUserForm.id,
              })
              .then((res) => {
                if (res.data.status == true) {
                  this.$swal.fire(
                    "Success!",
                    "แก้ไขข้อมูลสำเร็จแล้ว!",
                    "success"
                  );
                  this.editUserForm.google_uid = "";
                  this.onLoad();
                  this.EditUser(this.editUserForm.id);
                }
              });
          }
        });
    },

    RemoveUser(user_id, username) {
      this.$swal
        .fire({
          title: "แจ้งเตือน!",
          text: "ยืนยันการลบผู้ใช้ " + username,
          icon: "warning",
          showCancelButton: true,
          cancelButtonColor: "#3085d6",
          cancelButtonText: "ยกเลิก",
          confirmButtonColor: "rgb(235, 64, 52)",
          confirmButtonText: "ยืนยัน",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.axios
              .post("api/admin/remove/User", {
                id: user_id,
              })
              .then((res) => {
                if (res.data.status == true) {
                  this.$swal.fire(
                    "Success!",
                    "ลบผู้ใช้!" + username + "แล้ว",
                    "success"
                  );
                  this.onLoad();
                }
              });
          }
        });
    },

    EditUserPermission(user_id) {
      this.editUserPermissionForm.id = user_id;
      this.axios
        .post("api/admin/get/User/permission", {
          user_id: this.editUserPermissionForm.id,
        })
        .then((res) => {
          if (res.data.status == true) {
            this.editUserPermissionForm.user = res.data.permission_user;
            this.editUserPermissionForm.sender = res.data.permission_sender;
            this.editUserPermissionForm.admin = res.data.permission_admin;
          }
        });
    },

    EditUserPermissionSubmit(permission_id) {
      var type = false;
      if (permission_id == 0) {
        //User
        type = !!this.editUserPermissionForm.user;
      } else if (permission_id == 1) {
        //Sender
        type = !!this.editUserPermissionForm.sender;
      } else if (permission_id == 2) {
        //Admin
        type = !!this.editUserPermissionForm.admin;
      }
      this.axios
        .post("api/admin/edit/User/permission", {
          user_id: this.editUserPermissionForm.id,
          permission_id: permission_id,
          type: type,
        })
        .then((res) => {
          this.EditUserPermission(this.editUserPermissionForm.id);
        });
    },

    EditUserPermissionClose() {
      this.editUserPermissionForm.id = 0;
      this.editUserPermissionForm.user = false;
      this.editUserPermissionForm.sender = false;
      this.editUserPermissionForm.admin = false;
    },
  },
};
</script>
