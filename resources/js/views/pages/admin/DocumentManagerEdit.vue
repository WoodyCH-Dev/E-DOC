<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>แก้ไขเอกสาร</va-card-title>
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
              <div class="flex xl6 xs12">
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
              <div class="flex xl6 xs12">
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
              <div class="flex xl12 xs12">
                <div class="form-group">
                  <b>รายละเอียด (ถ้ามี)</b>
                  <va-input
                    placeholder="รายละเอียด (ถ้ามี)..."
                    required
                    v-model="form.document_description"
                  />
                </div>
              </div>
              <div class="flex xl12 xs12" align="center">
                <div class="form-group">
                  <router-link to="/admin/manage/document" class="nav-item">
                    <va-button class="primary mr-2">
                      <i class="fas fa-angle-left mr-2"></i> ย้อนกลับ
                    </va-button>
                  </router-link>
                  <va-button
                    style="background-color: rgb(47, 148, 91)"
                    @click="
                      $refs.form_data.validate() && DocumentSendEditSubmit()
                    "
                  >
                    <i class="fas fa-check-circle mr-2"></i> แก้ไขเอกสาร
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
        piority_select_value: null,
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
          this.axios.get("api/user/get/AllDocumentGroup").then(async (res) => {
            if (res.data.status == true) {
              for await (let doc_category of res.data.document_category) {
                this.form.category_select_options.push({
                  text: doc_category.group_name,
                  id: doc_category.id,
                });
              }
              this.LoadSenderDocumentInfo(this.$route.params.document_id);
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

    DocumentSendEditSubmit() {
      this.axios
        .post("api/admin/edit/sender/document", {
          document_id: this.$route.params.document_id,
          document_title: this.form.document_title,
          document_number: this.form.document_number,
          document_category_id: this.form.category_select_value.id,
          document_description: this.form.document_description,
          document_priority: this.form.piority_select_value.id,
          year_id: this.data.acd_year_id,
        })
        .then((res) => {
          if (res.data.status == true) {
            this.$swal
              .fire("Success!", "แก้ไขเอกสารแล้ว!", "success")
              .then(() => {
                this.$router.push("/admin/manage/document").then(() => {
                  location.reload();
                });
              });
          }
        });
    },

    async LoadSenderDocumentInfo(doc_id) {
      this.axios.get("api/sender/get/Sender/" + doc_id).then(async (res) => {
        if (res.data.status == true) {
          this.form.document_title = res.data.document_info.document_title;
          this.form.document_number = res.data.document_info.document_number;
          this.form.document_description =
            res.data.document_info.document_description;
          this.form.category_select_value =
            this.form.category_select_options.find((element) => {
              return element.id == res.data.document_info.document_category_id;
            });
          this.form.piority_select_value =
            this.form.piority_select_options.find((element) => {
              return element.id == res.data.document_info.document_priority;
            });
          var user_stage_1_lists = res.data.tracking.filter(async (element) => {
            return element.stage == 1;
          });
          this.form.user_select_value = new Array();
          for await (let user_stage_1 of user_stage_1_lists) {
            if (user_stage_1.sender_type == "user") {
              var user_data = this.form.user_select_options.find((element) => {
                return element.id == user_stage_1.to && element.type == "user";
              });
              this.form.user_select_value.push(user_data);
            } else if (user_stage_1.sender_type == "group") {
              var group_data = this.form.user_select_options.find((element) => {
                return element.id == user_stage_1.to && element.type == "group";
              });
              this.form.user_select_value.push(group_data);
            }
          }

          for (let file of user_stage_1_lists[0].files) {
            this.form.document_file.push({
              file: file.file,
            });
          }
        }
      });
    },
  },
};
</script>
