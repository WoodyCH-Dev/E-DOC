<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>ส่งเอกสาร</va-card-title>
        <va-card-content>
          <va-form ref="form_data" @validation="form.validation = $event">
            <div class="row">
              <div class="flex xl8 xs12">
                <div class="form-group">
                  <b>หัวข้อเรื่อง (*)</b>
                  <va-input
                    placeholder="เรื่อง..."
                    required
                    v-model="form.document_title"
                    :rules="[form.document_title != '' || 'กรุณาใส่หัวข้อ']"
                  />
                </div>
              </div>
              <div class="flex xl4 xs12">
                <div class="form-group">
                  <b>เลขที่เอกสาร (*)</b>
                  <va-input
                    placeholder="0"
                    required
                    v-model="form.document_number"
                    :rules="[
                      form.document_number != '' || 'กรุณาใส่เลขที่เอกสาร',
                    ]"
                  />
                </div>
              </div>
              <div class="flex xl4 xs12">
                <div class="form-group">
                  <b>หมวดหมู่ (*)</b>
                  <va-select
                    required
                    :options="form.category_select_options"
                    v-model="form.category_select_value"
                    :rules="[
                      form.category_select_value != null ||
                        'กรุณาเลือกหมวดหมู่',
                    ]"
                    track-by="id"
                    placeholder="กรุณาเลือกหมวดหมู"
                  />
                </div>
              </div>
              <div class="flex xl8 xs12">
                <div class="form-group">
                  <b>รายละเอียด (ถ้ามี)</b>
                  <va-input
                    placeholder="รายละเอียด (ถ้ามี)..."
                    required
                    v-model="form.document_description"
                  />
                </div>
              </div>
              <div class="flex xl12 xs12">
                <div class="form-group">
                  <b>ไฟล์ที่ Upload (*)</b>
                  <div class="va-table-responsive">
                    <table class="va-table" style="width: 100%">
                      <thead>
                        <tr>
                          <th>ลำดับที่</th>
                          <th>ไฟล์ที่แนบ</th>
                          <th></th>
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
                          <td>
                            <va-button
                              icon="delete"
                              class="mr-2"
                              color="danger"
                              v-on:click="Documentfile_RemoveFromArray(index)"
                            >
                              ลบ
                            </va-button>
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
              <div class="flex xl12 xs12">
                <div class="form-group">
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
              <div class="flex xl8 xs12">
                <div class="form-group">
                  <b>เลือกผู้รับ (*)</b>
                  <va-select
                    class="mb-4"
                    placeholder="เลือกผู้รับ"
                    :options="form.user_select_options"
                    v-model="form.user_select_value"
                    :rules="[
                      (form.user_select_value &&
                        form.user_select_value.length > 0) ||
                        'กรุณาเลือกผู้รับ',
                    ]"
                    track-by="id_key"
                    multiple
                    searchable
                    required
                  />
                </div>
              </div>
              <div class="flex xl4 xs12">
                <div class="form-group">
                  <b>ระดับความสำคัญ (*)</b>
                  <va-select
                    required
                    v-model="form.piority_select_value"
                    :options="form.piority_select_options"
                    track-by="id"
                    placeholder="ระดับความสำคัญ"
                  />
                </div>
              </div>
              <div class="flex xl12 xs12" align="center">
                <div class="form-group">
                  <va-button
                    style="background-color: rgb(47, 148, 91)"
                    @click="$refs.form_data.validate() && DocumentSendSubmit()"
                    :disabled="form.document_file.length == 0"
                  >
                    <i class="fas fa-paper-plane mr-2"></i> ส่งเอกสาร
                  </va-button>
                </div>
              </div>
            </div>
          </va-form>
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
    var piority_select_options = new Array(
      {
        text: "ทั่วไป",
        id: 0,
      },
      {
        text: "ด่วน",
        id: 1,
      },
      {
        text: "ด่วนที่สุด",
        id: 2,
      }
    );

    return {
      data: {
        username: username,
        lastname: lastname,
        acd_year: acd_year,
        acd_year_id: 0,
      },
      permission: {
        access_user: access_user,
        access_sender: access_sender,
        access_admin: access_admin,
      },
      form: {
        document_title: "",
        document_number: "",
        category_select_options: new Array(),
        category_select_value: null,
        document_description: "",
        document_file: new Array(),
        user_select_options: new Array(),
        user_select_value: null,
        piority_select_options: piority_select_options,
        piority_select_value: piority_select_options[0],
        validation: null,
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
        } else {
          this.$swal.fire(
            "Error!",
            "Permission ของคุณไม่สามารถเข้าถึงได้",
            "error"
          );
        }
      });

      this.axios.get("api/user/get/AllDocumentGroup").then(async (res) => {
        if (res.data.status == true) {
          for await (let doc_category of res.data.document_category) {
            this.form.category_select_options.push({
              text: doc_category.group_name,
              id: doc_category.id,
            });
          }
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
              this.form.document_file.push({
                file: res.data.file,
              });
            }
          });
        }
      }
    },

    Documentfile_RemoveFromArray(array_id) {
      this.form.document_file.splice(array_id, 1);
    },

    DocumentSendSubmit() {
      if (this.form.document_file.length > 0) {
        this.$swal.fire({
          title: "กำลังส่งข้อมูล กรุณารอสักครู่",
          allowOutsideClick: false,
          showConfirmButton: false,
        });
        this.axios
          .post("api/sender/send/document", {
            document_title: this.form.document_title,
            document_number: this.form.document_number,
            document_category_id: this.form.category_select_value.id,
            document_description: this.form.document_description,
            document_priority: this.form.piority_select_value.id,
            user_id: Number(window.localStorage.getItem("user_id")),
            year_id: this.data.acd_year_id,
            files: this.form.document_file,
            send_to: this.form.user_select_value,
          })
          .then((res) => {
            if (res.data.status == true) {
              this.$swal
                .fire("Success!", "ส่งเอกสารแล้ว!", "success")
                .then(() => {
                  this.$router.push("/sender/send/list");
                });
            }
          });
      } else {
        this.$swal.fire("Error!", "ไม่มีการ Upload ไฟล์ กรุณาตรวจสอบ", "error");
      }
    },
  },
};
</script>
