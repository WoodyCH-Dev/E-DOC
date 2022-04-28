<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>จัดการหมวดหมู่เอกสาร</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl12 xs12">
              <div class="form-group">
                <div class="va-table-responsive" style="overflow-y: auto">
                  <table
                    class="va-table"
                    style="width: 100%"
                    v-if="!data.AllDocumentgroup_isLoad"
                  >
                    <thead>
                      <tr>
                        <th>ลำดับ</th>
                        <th style="width: 50%">ชื่อหมวดหมู่</th>
                        <th style="text-align: right">
                          <va-button
                            icon="add"
                            class="mr-0"
                            style="background-color: rgb(47, 148, 91)"
                            v-on:click="CreateDocumentGroup()"
                          >
                            เพิ่มหมวดหมู่
                          </va-button>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(doc_category, index) in document_group_lists"
                        :key="doc_category.id"
                      >
                        <td>{{ index + 1 }}</td>
                        <td>{{ doc_category.group_name }}</td>
                        <td style="text-align: right">
                          <va-button
                            icon="edit"
                            class="mr-2"
                            color="warning"
                            v-on:click="
                              EditDocumentGroup(
                                doc_category.id,
                                doc_category.group_name
                              )
                            "
                          >
                            แก้ไข
                          </va-button>
                          <va-button
                            icon="clear"
                            class="mr-2"
                            color="danger"
                            v-on:click="RemoveDocumentGroup(doc_category.id)"
                          >
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
                  v-if="data.AllDocumentgroup_isLoad"
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
        AllDocumentgroup_isLoad: true,
      },
      permission: {
        access_user: access_user,
        access_sender: access_sender,
        access_admin: access_admin,
      },
      document_group_lists: new Array(),
    };
  },
  methods: {
    onLoad() {
      this.axios.get("api/user/acd_year").then((res) => {
        if (res.data.status == true) {
          this.data.acd_year = String(Number(res.data.acd_year) + 543);
        }
      });

      this.axios.get("api/user/get/AllDocumentGroup").then((res) => {
        if (res.data.status == true) {
          this.data.AllDocumentgroup_isLoad = false;
          this.document_group_lists = res.data.document_category;
        } else {
          this.$swal.fire(
            "Error!",
            "Permission ของคุณไม่สามารถเข้าถึงได้",
            "error"
          );
        }
      });
    },

    CreateDocumentGroup() {
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
                .post("api/admin/create/DocumentGroup", {
                  group_name: result.value,
                })
                .then((res) => {
                  this.$swal.fire(
                    "Success!",
                    "ทำการสร้างหมวดหมู่เอกสาร " + result.value + " แล้ว",
                    "success"
                  );
                  this.onLoad();
                });
            } else {
              this.$swal.fire(
                "Error!",
                "ไม่สารมารถเว้นการใส่ชื่อหมวดหมู่เอกสารได้",
                "error"
              );
            }
          }
        });
    },

    EditDocumentGroup(group_id, group_name) {
      this.$swal
        .fire({
          title: "ชื่อหมวดหมู่เอกสาร ที่ต้องเปลี่ยนชื่อ",
          html: "หมวดหมู่เอกสารที่เลือก: " + group_name,
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
                .post("api/admin/edit/DocumentGroup", {
                  id: group_id,
                  group_name: result.value,
                })
                .then((res) => {
                  this.$swal.fire(
                    "Success!",
                    "ทำการแก้ไขชื่อหมวดหมู่เอกสารเป็น " +
                      result.value +
                      " แล้ว",
                    "success"
                  );
                  this.onLoad();
                });
            } else {
              this.$swal.fire(
                "Error!",
                "ไม่สารมารถเว้นการใส่ชื่อหมวดหมู่เอกสารได้",
                "error"
              );
            }
          }
        });
    },

    RemoveDocumentGroup(group_id) {
      this.$swal
        .fire({
          icon: "warning",
          title: "แจ้งเตือน!",
          html: "คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่เอกสารนี้",
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
            this.axios
              .post("api/admin/remove/DocumentGroup", {
                id: group_id,
              })
              .then((res) => {
                this.$swal.fire(
                  "Success!",
                  "ทำการลบหมวดหมู่เอกสารนี้แล้ว",
                  "success"
                );
                this.onLoad();
              });
          }
        });
    },
  },
};
</script>
