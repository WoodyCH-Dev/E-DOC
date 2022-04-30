<style>
.events li {
  display: flex;
  color: #999;
}

.events label.left {
  position: relative;
  padding-right: 0.5em;
}

.events label.left::after {
  content: "";
  position: absolute;
  z-index: 2;
  right: 0;
  top: 0;
  transform: translateX(50%);
  border-radius: 50%;
  background: #fff;
  border: 1px #ccc solid;
  width: 0.8em;
  height: 0.8em;
}

.events span {
  padding: 0 1.5em 1.5em 1.5em;
  position: relative;
}

.events span::before {
  content: "";
  position: absolute;
  z-index: 1;
  left: 0;
  height: 100%;
  border-left: 1px #ccc solid;
}

.events strong {
  display: block;
  font-weight: bolder;
}

.events {
  margin: 1em;
  width: 100%;
}

.events,
.events *::before,
.events *::after {
  box-sizing: border-box;
}
</style>

<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>จัดการเอกสาร</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl4 xs12">
              <div class="form-group">
                <b>ปีการศึกษา</b>
                <va-select
                  v-model="data.acd_year_value"
                  :options="data.acd_year_options"
                />
              </div>
            </div>
            <div class="flex xl8 xs12" align="right">
              <router-link to="/sender/send" class="nav-item">
                <va-button icon="add" class="mr-2" color="primary">
                  เพิ่มเอกสาร
                </va-button>
              </router-link>
            </div>
            <div class="flex xl12 xs12">
              <div class="form-group">
                <div class="va-table-responsive" style="overflow-y: auto">
                  <table
                    class="va-table"
                    style="width: 100%"
                    v-if="!data.documents_lists_isLoad"
                  >
                    <thead>
                      <tr>
                        <th>เลขที่เอกสาร</th>
                        <th>หัวข้อเรื่อง</th>
                        <th>หมวดหมู่</th>
                        <th>ระดับความสำคัญ</th>
                        <th>ผู้ส่ง</th>
                        <th>ติดตามเอกสาร</th>
                        <th>จัดการ</th>
                      </tr>
                    </thead>
                    <tbody v-if="data.documents_lists.length == 0">
                      <tr>
                        <td colspan="7" style="text-align: center">
                          -- ยังไม่มีประวัติการส่งเอกสารในปีที่เลือก ในระบบ --
                        </td>
                      </tr>
                    </tbody>
                    <tbody v-if="data.documents_lists.length > 0">
                      <tr
                        v-for="doc_list in data.documents_lists"
                        :key="doc_list.id"
                      >
                        <td>
                          {{ doc_list.document_number }}
                          <label
                            v-if="doc_list.document_status == 1"
                            class="text-danger"
                          >
                            (เอกสารถูกยกเลิก)
                          </label>
                          <label
                            v-if="doc_list.document_status == 2"
                            class="text-danger"
                          >
                            (เอกสารถูกลบโดยผู้ใช้)
                          </label>
                        </td>
                        <td>{{ doc_list.document_title }}</td>
                        <td>{{ doc_list.group_name }}</td>
                        <td>
                          <label v-if="doc_list.document_priority == 0"
                            >ทั่วไป</label
                          >
                          <label v-if="doc_list.document_priority == 1"
                            >ด่วน</label
                          >
                          <label v-if="doc_list.document_priority == 2"
                            >ด่วนมาก</label
                          >
                        </td>
                        <td>{{ doc_list.name + " " + doc_list.lastname }}</td>
                        <td>
                          <va-button
                            icon="approval"
                            class="mr-2"
                            style="background-color: rgb(47, 148, 91)"
                            data-bs-toggle="modal"
                            data-bs-target="#TrackingStatusModal"
                            v-on:click="LoadSenderDocumentInfo(doc_list.doc_id)"
                          >
                            สถานะเอกสาร
                          </va-button>
                        </td>
                        <td>
                          <router-link
                            :to="
                              '/admin/manage/editdocument/' + doc_list.doc_id
                            "
                            class="nav-item"
                          >
                            <va-button icon="edit" class="mr-2" color="warning">
                              แก้ไข
                            </va-button>
                          </router-link>
                          <va-button
                            icon="clear"
                            class="mr-2"
                            color="danger"
                            v-on:click="
                              DeleteSenderDocumentAdmin(doc_list.doc_id)
                            "
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
                  v-if="data.documents_lists_isLoad"
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
    id="TrackingStatusModal"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">สถานะเอกสาร</h5>
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
            <div class="flex xs12 md6">
              <va-card square outlined>
                <va-card-title>ติดตามเอกสาร</va-card-title>
                <va-card-content>
                  <ul class="events">
                    <li>
                      <label class="left"></label>
                      <span>
                        <strong>
                          รับเอกสารเข้าระบบ ({{
                            BuddishDate(
                              data.select_my_sender_documents_data.timestamp
                            )
                          }})
                        </strong>
                        {{ data.username + " " + data.lastname }}</span
                      >
                    </li>

                    <li
                      v-for="documents_stage in data.select_my_sender_documents_stage_array"
                      :key="documents_stage.stage"
                    >
                      <label class="left"></label>
                      <span>
                        <strong> เอกสารถูกส่งไปที่&nbsp; </strong>
                        <p
                          v-for="to_data in documents_stage.stage_data"
                          :key="to_data.id"
                        >
                          <label v-if="to_data.sender_type == 'group'">
                            กลุ่มผู้ใช้:
                            {{ to_data.to_data.group_name }}
                            <va-button
                              color="info"
                              class="mr-0"
                              size="small"
                              v-on:click="ShowFileDialog(to_data.files)"
                            >
                              ไฟล์แนบ
                            </va-button>
                          </label>
                          <label v-if="to_data.sender_type == 'user'">
                            ผู้ใช้:
                            {{
                              to_data.to_data.name +
                              " " +
                              to_data.to_data.lastname
                            }}
                            <va-button
                              color="info"
                              class="mr-0"
                              size="small"
                              v-on:click="ShowFileDialog(to_data.files)"
                            >
                              ไฟล์แนบ
                            </va-button>
                          </label>
                        </p>
                      </span>
                    </li>

                    <li
                      v-if="data.select_my_sender_documents_data.sign_timestamp"
                    >
                      <label class="left"></label>
                      <span
                        ><strong>
                          เสร็จสิ้นกระบวนการ ({{
                            BuddishDate(
                              data.select_my_sender_documents_data
                                .sign_timestamp
                            )
                          }})
                        </strong></span
                      >
                    </li>
                  </ul>
                </va-card-content>
              </va-card>
            </div>
            <div class="flex xs12 md6">
              <va-card square outlined>
                <va-card-title>รายละเอียดเอกสาร</va-card-title>
                <va-card-content>
                  <b>หัวข้อเรื่อง: </b>
                  {{ data.select_my_sender_documents_data.document_title }}
                  <br />
                  <b>เลขที่เอกสาร: </b>
                  {{ data.select_my_sender_documents_data.document_number }}
                  <br />
                  <b>หมวดหมู่: </b>
                  {{ data.select_my_sender_documents_data.group_name }}
                  <br />
                  <b>รายละเอียด: </b>
                  {{
                    data.select_my_sender_documents_data.document_description
                  }}
                  <br />
                  <b>ระดับความสำคัญ: </b>
                  <label
                    v-if="
                      data.select_my_sender_documents_data.document_priority ==
                      0
                    "
                    >ทั่วไป</label
                  >
                  <label
                    v-if="
                      data.select_my_sender_documents_data.document_priority ==
                      1
                    "
                    >ด่วน</label
                  >
                  <label
                    v-if="
                      data.select_my_sender_documents_data.document_priority ==
                      2
                    "
                    >ด่วนที่สุด</label
                  >
                  <br />
                  <b
                    v-if="
                      data.select_my_sender_documents_data.document_status == 1
                    "
                    class="text-danger"
                  >
                    (เอกสารถูกยกเลิก)
                  </b>
                  <b
                    v-if="
                      data.select_my_sender_documents_data.document_status == 2
                    "
                    class="text-danger"
                  >
                    (เอกสารถูกลบโดยผู้ใช้)
                  </b>
                  <br />
                  <br />
                  <va-button
                    icon="check_circle"
                    style="margin-bottom: 3px"
                    color="primary"
                    :disabled="
                      data.select_my_sender_documents_data.document_status == 0
                    "
                    v-on:click="
                      AssignSenderDocument(
                        data.select_my_sender_documents_data.doc_id
                      )
                    "
                  >
                    ยกเลิก การยกเลิกหรือลบเอกสาร
                  </va-button>
                  <br />
                  <va-button
                    icon="cancel"
                    style="margin-bottom: 3px"
                    color="warning"
                    :disabled="
                      data.select_my_sender_documents_data.document_status ==
                        1 ||
                      data.select_my_sender_documents_data.document_status == 2
                    "
                    v-on:click="
                      CancelSenderDocument(
                        data.select_my_sender_documents_data.doc_id
                      )
                    "
                  >
                    ยกเลิกเอกสาร
                  </va-button>
                  <br />
                  <va-button
                    icon="delete_forever"
                    style="margin-bottom: 3px"
                    color="danger"
                    :disabled="
                      data.select_my_sender_documents_data.document_status == 2
                    "
                    v-on:click="
                      DeleteSenderDocument(
                        data.select_my_sender_documents_data.doc_id
                      )
                    "
                  >
                    ลบเอกสาร (ซ่อนจากทุกคน)
                  </va-button>
                </va-card-content>
              </va-card>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
            v-on:click="CloseSenderDocumentInfo()"
            id="CloseTrackingStatusModal"
          >
            ปิด
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
        acd_year_options: new Array(),
        acd_year_value: null,
        documents_lists: new Array(),
        documents_lists_isLoad: true,
        select_my_sender_documents_data: new Array(),
        select_my_sender_documents_stage_array: new Array(),
        select_my_sender_documents_stage: 0,
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
      this.axios.get("api/user/acd_year/lists").then(async (res) => {
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
                this.LoadDocumentLists();
              }
            });
          }
        }
      });
    },

    LoadDocumentLists() {
      this.axios
        .post("api/admin/get/AllSender", {
          year_id: this.data.acd_year_value.id,
          user_id: window.localStorage.getItem("user_id"),
        })
        .then((res) => {
          if (res.data.status == true) {
            this.data.documents_lists = res.data.lists;
            this.data.documents_lists_isLoad = false;
          }
        });
    },

    BuddishDate(date) {
      var date = new Date(date);
      return date.toLocaleDateString("th-TH", {
        day: "numeric",
        month: "short",
        year: "numeric",
      });
    },

    LoadSenderDocumentInfo(doc_id) {
      this.axios.get("api/sender/get/Sender/" + doc_id).then(async (res) => {
        if (res.data.status == true) {
          this.data.select_my_sender_documents_data = res.data.document_info;
          this.data.select_my_sender_documents_stage = res.data.stage;
          for (
            var i = 1;
            i <= this.data.select_my_sender_documents_stage;
            i++
          ) {
            var to_data_array = new Array();
            for await (let to_data of res.data.tracking) {
              if (to_data.stage == i) {
                to_data_array.push(to_data);
              }
            }

            this.data.select_my_sender_documents_stage_array.push({
              stage: i,
              stage_data: to_data_array,
            });
          }
        }
      });
    },

    ShowFileDialog(file_array) {
      console.log(file_array);
      var html_file = "";
      for (let file of file_array) {
        html_file =
          `<a href='public/uploads/sender/${file.file}' target='_blank'>` +
          file.file +
          "</a><br>" +
          html_file;
      }
      this.$swal.fire({
        icon: "info",
        title: "ไฟล์แนบ (คลิกเพื่อดูไฟล์)",
        html: html_file,
      });
    },

    CloseSenderDocumentInfo() {
      this.data.select_my_sender_documents_data = new Array();
      this.data.select_my_sender_documents_stage_array = new Array();
      this.data.select_my_sender_documents_stage = 0;
    },

    CancelSenderDocument(doc_id) {
      this.$swal
        .fire({
          title: "แจ้งเตือน!",
          text: "คุณแน่ใจที่จะยกเลิกเอกสาร",
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
              .post("api/sender/document/cancel", {
                document_id: doc_id,
              })
              .then((res) => {
                if (res.data.status == true) {
                  document.getElementById("CloseTrackingStatusModal").click();
                  this.onLoad();
                }
              });
          }
        });
    },

    DeleteSenderDocument(doc_id) {
      this.$swal
        .fire({
          title: "แจ้งเตือน!",
          text: "คุณแน่ใจที่จะลบเอกสาร",
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
              .post("api/sender/document/delete", {
                document_id: doc_id,
              })
              .then((res) => {
                if (res.data.status == true) {
                  document.getElementById("CloseTrackingStatusModal").click();
                  this.onLoad();
                }
              });
          }
        });
    },

    AssignSenderDocument(doc_id) {
      this.$swal
        .fire({
          title: "แจ้งเตือน!",
          text: "คุณแน่ใจที่จะยกเลิก การยกเลิกเอกสาร (เอกสารจะกลับมาเห็นเป็นปกติและสามารถทำงานต่อได้)",
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
              .post("api/sender/document/assign", {
                document_id: doc_id,
              })
              .then((res) => {
                if (res.data.status == true) {
                  document.getElementById("CloseTrackingStatusModal").click();
                  this.onLoad();
                }
              });
          }
        });
    },

    DeleteSenderDocumentAdmin(doc_id) {
      this.$swal
        .fire({
          title: "แจ้งเตือน!",
          text: "คุณแน่ใจที่จะลบเอกสารนี้ (เอกสารจะถูกลบออกจากฐานข้อมูล)",
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
              .post("api/admin/document/delete", {
                document_id: doc_id,
              })
              .then((res) => {
                if (res.data.status == true) {
                  document.getElementById("CloseTrackingStatusModal").click();
                  this.onLoad();
                }
              });
          }
        });
    },
  },
};
</script>
