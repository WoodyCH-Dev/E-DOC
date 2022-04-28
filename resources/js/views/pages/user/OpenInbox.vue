<style>
.fastest {
  background-color: crimson;
  padding: 5px;
  border-radius: 15px;
  color: white;
}

.fast {
  background-color: rgb(233, 77, 108);
  padding: 5px;
  border-radius: 15px;
  color: white;
}

.normal {
  background-color: rgb(20, 163, 220);
  padding: 5px;
  border-radius: 15px;
  color: white;
}
</style>

<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>ดูเอกสาร</va-card-title>
        <va-progress-bar indeterminate v-if="!data.isLoad" />
        <va-card-content v-if="data.isLoad">
          <div class="row">
            <div class="flex xl12 xs12">
              <h2>
                <label
                  class="fastest"
                  v-if="form.piority_select_value == 'ด่วนมาก'"
                  >{{ form.piority_select_value }}</label
                >
                <label
                  class="fast"
                  v-if="form.piority_select_value == 'ด่วน'"
                  >{{ form.piority_select_value }}</label
                >
                <label
                  class="normal"
                  v-if="form.piority_select_value == 'ทั่วไป'"
                  >{{ form.piority_select_value }}</label
                >
                เลขที่เอกสาร {{ form.document_number }}
                <i class="fas fa-angle-double-right"></i>
                {{ form.category_select_value }}
              </h2>
            </div>
            <div class="flex xl12 xs12">
              <h4>
                <b>เรื่อง:</b> {{ form.document_title }}
                <b v-if="form.document_status == 1" class="text-danger">
                  (เอกสารถูกยกเลิก)
                </b>
              </h4>
            </div>
            <div class="flex xl12 xs12">
              <b>รายะละเอียด:</b>
              {{ form.document_description || "ไม่มีรายละเอียด" }}
            </div>
            <br />
            <br />
            <br />
            <div
              class="flex xl12 xs12"
              v-if="form.finished_check == true || form.document_status != 0"
            >
              <div class="form-group">
                <b>ไฟล์ที่แนบมา (คลิกเพื่อเปิดไฟล์)</b>
                <div class="va-table-responsive">
                  <table class="va-table" style="width: 100%">
                    <thead>
                      <tr>
                        <th>ลำดับที่</th>
                        <th>ไฟล์ที่แนบ</th>
                      </tr>
                    </thead>
                    <tbody v-if="form.document_file.length > 0">
                      <tr
                        v-for="(file, index) in form.document_file"
                        :key="file"
                      >
                        <td>{{ index + 1 }}</td>
                        <td>
                          <a
                            :href="'public/uploads/sender/' + file.file"
                            target="_blank"
                            >{{ file.file }}</a
                          >
                        </td>
                      </tr>
                    </tbody>
                    <tbody v-if="form.document_file.length == 0">
                      <tr>
                        <td colspan="2" style="text-align: center">
                          -- ยังไม่มีการ Upload ไฟล์เอกสาร --
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div
              class="flex xl6 xs12"
              v-if="form.finished_check == false && form.document_status == 0"
            >
              <div class="form-group">
                <b>ไฟล์ที่แนบมา (คลิกเพื่อเปิดไฟล์)</b>
                <div class="va-table-responsive">
                  <table class="va-table" style="width: 100%">
                    <thead>
                      <tr>
                        <th>ลำดับที่</th>
                        <th>ไฟล์ที่แนบ</th>
                      </tr>
                    </thead>
                    <tbody v-if="form.document_file.length > 0">
                      <tr
                        v-for="(file, index) in form.document_file"
                        :key="file"
                      >
                        <td>{{ index + 1 }}</td>
                        <td>
                          <a
                            :href="'public/uploads/sender/' + file.file"
                            target="_blank"
                            >{{ file.file }}</a
                          >
                        </td>
                      </tr>
                    </tbody>
                    <tbody v-if="form.document_file.length == 0">
                      <tr>
                        <td colspan="2" style="text-align: center">
                          -- ยังไม่มีการ Upload ไฟล์เอกสาร --
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div
              class="flex xl6 xs12"
              v-if="
                form.finished_check == false &&
                form.finished_check == false &&
                form.document_status == 0
              "
            >
              <div class="form-group">
                <b>ไฟล์ที่ส่งต่อ (กรุณา Upload)</b>
                <div class="va-table-responsive">
                  <table class="va-table" style="width: 100%">
                    <thead>
                      <tr>
                        <th>ลำดับที่</th>
                        <th>ไฟล์ที่แนบ</th>
                        <th v-if="form.stage_status != 1"></th>
                      </tr>
                    </thead>
                    <tbody v-if="form.document_file_reupload.length > 0">
                      <tr
                        v-for="(file, index) in form.document_file_reupload"
                        :key="file"
                      >
                        <td>{{ index + 1 }}</td>
                        <td>
                          <a
                            :href="'public/uploads/sender/' + file.file"
                            target="_blank"
                            >{{ file.file }}</a
                          >
                        </td>
                        <td v-if="form.stage_status != 1">
                          <va-button
                            icon="delete"
                            class="mr-2"
                            color="danger"
                            v-on:click="Documentfile_RemoveFromArray(index)"
                          >
                          </va-button>
                        </td>
                      </tr>
                    </tbody>
                    <tbody v-if="form.document_file_reupload.length == 0">
                      <tr>
                        <td colspan="3" style="text-align: center">
                          -- ยังไม่มีการ Upload ไฟล์เอกสาร --
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="form-group" v-if="form.stage_status != 1">
                  <va-button v-on:click="Documentfile_selectBtn()">
                    <i class="fas fa-upload mr-2"></i> เลือกไฟล์
                  </va-button>
                  <input
                    type="file"
                    hidden
                    id="SelectFileUploadBtn"
                    @change="Documentfile_upload"
                    multiple
                  />
                </div>
              </div>
            </div>

            <div
              class="flex xl12 xs12"
              v-if="
                form.stage_status != 1 &&
                form.finished_check == false &&
                form.document_status == 0
              "
            >
              <div class="form-group">
                <va-checkbox
                  color="primary"
                  v-model="form.sign_check"
                  label="ลงวันที่ (เมื่อลงวันที่แล้ว เอกสารจะมีสถานะจบกระบวนการ และถูกส่งกลับไปที่ผู้ส่ง)"
                />
              </div>
            </div>
            <div
              class="flex xl12 xs12"
              v-if="
                !form.sign_check &&
                form.stage_status != 1 &&
                form.finished_check == false &&
                form.document_status == 0
              "
            >
              <div class="form-group">
                <b>เลือกผู้ที่จะส่งต่อถึง (*)</b>
                <va-select
                  class="mb-4"
                  placeholder="เลือกผู้ที่จะส่งต่อถึง"
                  :options="form.user_select_options"
                  v-model="form.user_select_value"
                  track-by="id_key"
                  multiple
                  required
                  searchable
                />
              </div>
            </div>
            <div class="flex xl12 xs12" align="center">
              <div class="form-group">
                <router-link to="/user/inbox" class="nav-item">
                  <va-button class="primary mr-2">
                    <i class="fas fa-angle-left mr-2"></i> ย้อนกลับ
                  </va-button>
                </router-link>
                <va-button
                  v-if="
                    !form.sign_check &&
                    form.stage_status != 1 &&
                    form.finished_check == false
                  "
                  style="background-color: rgb(47, 148, 91)"
                  :disabled="form.document_file_reupload.length == 0"
                  v-on:click="DocumentSendEditSubmit()"
                >
                  <i class="fas fa-paper-plane mr-2"></i> ส่งต่อเอกสาร
                </va-button>

                <va-button
                  v-if="
                    form.sign_check &&
                    form.stage_status != 1 &&
                    form.finished_check == false
                  "
                  style="background-color: rgb(47, 148, 91)"
                  :disabled="form.document_file_reupload.length == 0"
                  v-on:click="DocumentSendEditSubmit()"
                >
                  <i class="fas fa-badge-check mr-2"></i> จบกระบวนการ
                </va-button>
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
        acd_year_id: 0,
        isLoad: false,
      },
      permission: {
        access_user: access_user,
        access_sender: access_sender,
        access_admin: access_admin,
      },
      form: {
        finished_check: false,
        document_title: "",
        document_number: "",
        document_id: 0,
        document_status: 0,
        category_select_options: new Array(),
        category_select_value: null,
        document_description: "",
        document_file: new Array(),
        document_file_reupload: new Array(),
        user_select_options: new Array(),
        user_select_value: null,
        piority_select_value: null,
        validation: null,
        document_stage: 0,
        sign_check: false,
        stage_status: 1,
      },
    };
  },
  methods: {
    onLoad() {
      this.axios.get("api/user/acd_year").then((res) => {
        if (res.data.status == true) {
          this.data.acd_year = String(Number(res.data.acd_year) + 543);
          this.data.acd_year_id = res.data.acd_year_id;
        }
      });

      this.axios.get("api/user/get/AllUserAndGroup").then(async (res) => {
        if (res.data.status == true) {
          var i = 0;
          for await (let group of res.data.groups) {
            this.form.user_select_options.push({
              text: "กลุ่มผู้ใช้: " + group.group_name,
              type: "group",
              id: group.id,
              id_key: i++,
            });
          }
          for await (let user of res.data.users) {
            this.form.user_select_options.push({
              text: user.name + " " + user.lastname,
              type: "user",
              id: user.id,
              id_key: i++,
            });
          }
          this.axios.get("api/user/get/AllDocumentGroup").then(async (res) => {
            if (res.data.status == true) {
              for await (let doc_category of res.data.document_category) {
                this.form.category_select_options.push({
                  text: doc_category.group_name,
                  id: doc_category.id,
                });
              }
              this.LoadSenderDocumentInfo(this.$route.params.stage_id);
            } else {
              this.$swal.fire(
                "Error!",
                "Permission ของคุณไม่สามารถเข้าถึงได้",
                "error"
              );
            }
          });
        } else {
          this.$swal.fire(
            "Error!",
            "Permission ของคุณไม่สามารถเข้าถึงได้",
            "error"
          );
        }
      });
    },

    Documentfile_selectBtn() {
      document.getElementById("SelectFileUploadBtn").click();
    },

    Documentfile_upload(event) {
      let files = event.target.files;
      if (files.length > 0) {
        for (let file of files) {
          var formData = new FormData();
          formData.append("file", file);
          this.axios.post("api/sender/upload/files", formData).then((res) => {
            if (res.data.status == true) {
              this.form.document_file_reupload.push({
                file: res.data.file,
              });
            }
          });
        }
      }
    },

    Documentfile_RemoveFromArray(array_id) {
      this.form.document_file_reupload.splice(array_id, 1);
    },

    async LoadSenderDocumentInfo(stage_id) {
      this.axios.get("api/user/get/Sender/" + stage_id).then(async (res) => {
        if (res.data.status == true) {
          if (res.data.document_info.document_priority == 0) {
            this.form.piority_select_value = "ทั่วไป";
          } else if (res.data.document_info.document_priority == 1) {
            this.form.piority_select_value = "ด่วน";
          } else if (res.data.document_info.document_priority == 2) {
            this.form.piority_select_value = "ด่วนมาก";
          }
          this.form.document_number = res.data.document_info.document_number;
          this.form.document_title = res.data.document_info.document_title;
          this.form.document_id = res.data.document_info.doc_id;
          this.form.document_stage = res.data.document_info.stage;
          this.form.stage_status = res.data.document_info.status;
          this.form.document_status = res.data.document_info.document_status;
          this.form.finished_check = !!res.data.document_info.sign_timestamp;
          this.form.document_description =
            res.data.document_info.document_description;
          this.form.category_select_value =
            this.form.category_select_options.find((element) => {
              return element.id == res.data.document_info.document_category_id;
            }).text;

          this.form.document_file = res.data.document_files;
          this.form.document_file_reupload =
            res.data.document_reupload_file_array;
          this.data.isLoad = true;
        }
      });
    },

    DocumentSendEditSubmit() {
      if (
        (this.form.user_select_value && this.form.sign_check == false) ||
        this.form.sign_check == true
      ) {
        this.axios
          .post("api/user/submit/Sender", {
            document_stage_id: this.$route.params.stage_id,
            document_id: this.form.document_id,
            sign_check: this.form.sign_check,
            user_id: Number(window.localStorage.getItem("user_id")),
            files: this.form.document_file_reupload,
            send_to: this.form.user_select_value,
            stage: this.form.document_stage,
          })
          .then((res) => {
            if (res.data.status == true) {
              this.$swal
                .fire("Success!", "ส่งต่อเอกสารแล้ว!", "success")
                .then(() => {
                  this.$router.push("/user/inbox").then(() => {
                    location.reload();
                  });
                });
            }
          });
      } else {
        this.$swal.fire("Error!", "ไม่มีการเลือกผู้รับ", "error");
      }
    },
  },
};
</script>
