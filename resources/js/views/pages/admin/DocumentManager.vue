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
              <va-button icon="add" class="mr-2" color="primary">
                เพิ่มเอกสาร
              </va-button>
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
                          -- ยังไม่มีประวัติการของปีที่เลือกส่งในระบบ --
                        </td>
                      </tr>
                    </tbody>
                    <tbody v-if="data.documents_lists.length > 0">
                      <tr
                        v-for="doc_list in data.documents_lists"
                        :key="doc_list.id"
                      >
                        <td>{{ doc_list.document_number }}</td>
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
                          >
                            สถานะเอกสาร
                          </va-button>
                        </td>
                        <td>
                          <va-button icon="edit" class="mr-2" color="warning">
                            แก้ไข
                          </va-button>
                          <va-button icon="clear" class="mr-2" color="danger">
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
        <div class="modal-body">ยังไม่รองรับ Feature นี้</div>
        <div class="modal-footer">
          <va-button
            icon="close"
            class="mr-1"
            color="danger"
            data-bs-dismiss="modal"
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
  },
};
</script>
