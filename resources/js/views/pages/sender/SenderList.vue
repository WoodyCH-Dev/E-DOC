<style>
.events li {
  display: flex;
  color: #999;
}

.events time {
  position: relative;
  padding: 0 1.5em;
}

.events time::after {
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
  font-family: arial;
}
</style>

<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>รายการที่ส่ง</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl12 xs12">
              <div class="flex xl4 xs12">
                <div class="form-group">
                  <b>ปีการศึกษา</b>
                  <va-select
                    v-model="data.acd_year_value"
                    :options="data.acd_year_options"
                    v-on:change="LoadSenderDocumentLists()"
                    track-by="id"
                  />
                </div>
              </div>
              <div class="flex xl12 xs12">
                <div class="form-group">
                  <div class="va-table-responsive" style="overflow-y: auto">
                    <table
                      class="va-table"
                      style="width: 100%"
                      v-if="!data.my_sender_documents_lists_isLoad"
                    >
                      <thead>
                        <tr>
                          <th>เลขที่เอกสาร</th>
                          <th>วันที่ส่ง</th>
                          <th>เอกสารลงวันที่</th>
                          <th>หัวข้อเรื่อง</th>
                          <th>หมวดหมู่เอกสาร</th>
                          <th>รายการผู้รับเอกสาร</th>
                        </tr>
                      </thead>
                      <tbody v-if="data.my_sender_documents_lists.length == 0">
                        <tr>
                          <td colspan="6" style="text-align: center">
                            -- ยังไม่มีประวัติการส่งของคุณในปีที่เลือก --
                          </td>
                        </tr>
                      </tbody>
                      <tbody v-if="data.my_sender_documents_lists.length > 0">
                        <tr
                          v-for="my_send_doc in data.my_sender_documents_lists"
                          :key="my_send_doc.id"
                        >
                          <td>{{ my_send_doc.document_number }}</td>
                          <td>{{ BuddishDate(my_send_doc.timestamp) }}</td>
                          <td>
                            {{
                              my_send_doc.sign_timestamp ||
                              "ยังไม่มีการลงวันที่"
                            }}
                          </td>
                          <td>{{ my_send_doc.document_title }}</td>
                          <td>{{ my_send_doc.group_name }}</td>
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
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div
                    align="center"
                    style="padding-top: 30px"
                    v-if="data.my_sender_documents_lists_isLoad"
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
          <ul class="events">
            <li>
              <time datetime="10:03">10:03</time>
              <span><strong>Bat &amp; Ball</strong> On time</span>
            </li>

            <li>
              <time datetime="10:03">10:03</time>
              <span><strong>Bat &amp; Ball</strong> On time</span>
            </li>

            <li>
              <time datetime="10:03">10:03</time>
              <span>
                <strong> Bat &amp; Ball</strong>
                On time and other text that may span over 2 lines
              </span>
            </li>

            <li>
              <time datetime="10:03">10:03</time>
              <span><strong>Bat &amp; Ball</strong> On time</span>
            </li>

            <li>
              <time datetime="10:03">10:03</time>
              <span><strong>Bat &amp; Ball</strong> On time</span>
            </li>

            <li>
              <time datetime="10:03">10:03</time>
              <span><strong>Bat &amp; Ball</strong> On time</span>
            </li>
          </ul>

          <va-button icon="cancel" class="mr-1" color="warning">
            ยกเลิกเอกสาร
          </va-button>
          <va-button icon="delete_forever" class="mr-1" color="danger">
            ลบเอกสาร
          </va-button>
        </div>
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
        my_sender_documents_lists: new Array(),
        my_sender_documents_lists_isLoad: true,
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
                this.LoadSenderDocumentLists();
              }
            });
          }
        }
      });
    },

    LoadSenderDocumentLists() {
      this.axios
        .post("api/sender/get/MySender", {
          year_id: this.data.acd_year_value.id,
          user_id: window.localStorage.getItem("user_id"),
        })
        .then((res) => {
          if (res.data.status == true) {
            this.data.my_sender_documents_lists = res.data.lists;
            this.data.my_sender_documents_lists_isLoad = false;
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
