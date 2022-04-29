<template>
  <div class="row">
    <div class="flex xs12 xl12 center">
      <va-card tag="b" outlined>
        <va-card-content>
          <div class="row">
            <div class="flex xl12 xs12">
              <va-card stripe stripe-color="danger">
                <va-card-title class="card-title-dashboard">
                  <i class="far fa-tachometer"></i>&nbsp; จัดการระบบ
                </va-card-title>
                <va-card-content class="card-subtitle-dashboard">
                  แอดมิน {{ data.username + " " + data.lastname }}
                </va-card-content>
              </va-card>
            </div>

            <div class="flex lg4 xs12">
              <va-card color="primary" gradient>
                <va-card-title class="card-title-dashboard">
                  ปีการศึกษา ปัจจุบัน
                </va-card-title>
                <va-card-content class="card-subtitle-dashboard">
                  {{ data.acd_year }}
                </va-card-content>
              </va-card>
            </div>
            <div class="flex lg4 xs12">
              <va-card color="warning" gradient>
                <va-card-title class="card-title-dashboard">
                  ยอดเอกสารทั้งหมดของปี
                </va-card-title>
                <va-card-content class="card-subtitle-dashboard">
                  {{ data.AllDoc_count }}
                </va-card-content>
              </va-card>
            </div>
            <div class="flex lg4 xs12">
              <va-card color="danger" gradient>
                <va-card-title class="text-white card-title-dashboard">
                  จำนวนผู้ใช้งานทั้งหมด
                </va-card-title>
                <va-card-content class="text-white card-subtitle-dashboard">
                  {{ data.user_count }}
                </va-card-content>
              </va-card>
            </div>

            <div class="flex lg12 xs12">
              <hr />
            </div>

            <div class="flex lg4 xs12">
              <div class="form-group">
                <div class="row">
                  <b>ตั้งค่าปีการศึกษา ปัจจุบัน</b>
                  <div class="flex lg10 xs10">
                    <va-select
                      v-model="data.acd_year_value"
                      :options="data.acd_year_options"
                      track-by="id"
                    />
                  </div>
                  <div class="flex lg2 xs2">
                    <va-button
                      icon="save"
                      color="warning"
                      v-on:click="changeMainEduYear()"
                    />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <b>เพิ่มปีการศึกษา (พ.ศ.)</b>
                <div class="row">
                  <div class="flex lg10 xs10">
                    <va-input class="mb-0" v-model="edu_manage.new_year" />
                  </div>
                  <div class="flex lg2 xs2">
                    <va-button icon="add" v-on:click="createEdu_year()" />
                  </div>
                  <div class="flex lg12 xs12">
                    รายการปีการศึกษาในระบบ
                    <ul>
                      <li v-for="year in data.acd_year_options" :key="year.id">
                        <va-alert
                          color="danger"
                          border="top"
                          border-color="success"
                        >
                          <div class="row">
                            <div
                              class="flex lg6 xs6"
                              v-if="data.acd_year != year.text"
                            >
                              <va-button
                                icon="delete"
                                color="danger"
                                v-on:click="removeEduYear(year.id)"
                              />
                            </div>
                            <div
                              class="flex lg6 xs6"
                              v-if="data.acd_year == year.text"
                            >
                              (ปีการศึกษาปัจจุบัน)
                            </div>
                            <div class="flex lg6 xs6" style="text-align: right">
                              พ.ศ. {{ year.text }}
                            </div>
                          </div>
                        </va-alert>
                      </li>
                    </ul>
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
        acd_year: 0,
        acd_year_options: new Array(),
        acd_year_value: null,
        user_count: 0,
        AllDoc_count: 0,
      },
      permission: {
        access_user: access_user,
        access_sender: access_sender,
        access_admin: access_admin,
      },
      edu_manage: {
        new_year: null,
      },
    };
  },
  methods: {
    onLoad() {
      this.axios.get("api/user/acd_year/lists").then(async (res) => {
        this.data.acd_year_options = new Array();
        this.data.acd_year_value = null;

        if (res.data.status == true) {
          for await (let year_data of res.data.acd_year) {
            this.data.acd_year_options.push({
              text: Number(year_data.year) + 543,
              id: year_data.id,
            });

            this.axios.get("api/user/acd_year").then((res) => {
              if (res.data.status == true) {
                this.data.acd_year_value = this.data.acd_year_options.find(
                  (year_lists) =>
                    year_lists.text == Number(res.data.acd_year) + 543
                );
                this.LoadSenderDocumentLists();
              }
            });
          }
        }
      });

      this.axios.get("api/user/acd_year").then((res) => {
        if (res.data.status == true) {
          this.data.acd_year = String(Number(res.data.acd_year) + 543);

          this.axios
            .get("api/user/dashboard/getdocumentcount/" + res.data.acd_year_id)
            .then((res) => {
              if (res.data.status == true) {
                this.data.AllDoc_count = res.data.count;
              }
            });
        }
      });

      this.axios.get("api/user/dashboard/getusercount").then((res) => {
        if (res.data.status == true) {
          this.data.user_count = res.data.count;
        }
      });
    },

    createEdu_year() {
      if (this.edu_manage.new_year != "" && this.edu_manage.new_year != null) {
        this.axios
          .post("api/admin/create/acd_year", {
            year: Number(this.edu_manage.new_year) - 543,
          })
          .then((res) => {
            if (res.data.status == true) {
              this.edu_manage.new_year = null;
              this.onLoad();
            } else {
              this.$swal.fire(
                "Error!",
                "ระบบพบการสร้างปีการศึกษานี้แล้ว",
                "error"
              );
            }
          });
      }
    },

    removeEduYear(year_id) {
      this.$swal
        .fire({
          title: "แจ้งเตือน!",
          text: "ยืนยันการลบปีการศึกษา ",
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
              .post("api/admin/remove/acd_year", {
                year_id: year_id,
              })
              .then((res) => {
                if (res.data.status == true) {
                  this.onLoad();
                } else {
                  this.$swal.fire(
                    "Error!",
                    "มีการใช้งานข้อมูลปีการศึกษานี้อยู่ ไม่สามารถลบได้",
                    "error"
                  );
                }
              });
          }
        });
    },

    changeMainEduYear() {
      this.axios
        .post("api/admin/edit/main_edu_year", {
          year_id: this.data.acd_year_value.id,
        })
        .then((res) => {
          if (res.data.status == true) {
            this.$swal
              .fire("Success", "แก้ไขปีการศึกษาปัจจุบันแล้ว", "success")
              .then(() => {
                location.reload();
              });
          }
        });
    },
  },
};
</script>
