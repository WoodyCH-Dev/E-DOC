<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>กล่องเอกสารเข้า</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl12 xs12">
              <div class="flex xl4 xs12">
                <div class="form-group">
                  <b>ปีการศึกษา</b>
                  <va-select
                    v-model="data.acd_year_value"
                    :options="data.acd_year_options"
                  />
                </div>
              </div>
              <div class="flex xl12 xs12">
                <div class="form-group">
                  <div class="va-table-responsive" style="overflow-y: auto">
                    <table
                      class="va-table"
                      style="width: 100%"
                      v-if="!data.inbox_isLoad"
                    >
                      <thead>
                        <tr>
                          <th>เลขที่เอกสาร</th>
                          <th>วันที่ส่ง</th>
                          <th>เอกสารลงวันที่</th>
                          <th>หัวข้อเรื่อง</th>
                          <th>ชื่อผู้ส่ง</th>
                          <th>สถานะ</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="inbox in data.inbox_array"
                          :key="inbox.document_id"
                        >
                          <td>
                            {{ inbox.document_number }}
                            <label v-if="inbox.sender_type == 'group'">
                              (กลุ่ม)
                            </label>
                          </td>
                          <td>{{ BuddishDate(inbox.timestamp) }}</td>
                          <td>
                            {{
                              BuddishDate(inbox.sign_timestamp) ||
                              "ยังไม่ลงวันที่"
                            }}
                          </td>
                          <td>{{ inbox.document_title }}</td>
                          <td>{{ inbox.name + " " + inbox.lastname }}</td>
                          <td>
                            <label v-if="inbox.status == 1">
                              <va-badge
                                text="เปิดแล้ว"
                                color="success"
                                class="mr-0"
                              />
                            </label>
                            <label v-if="inbox.status == 0">
                              <va-badge
                                text="ยังไม่เปิด"
                                color="warning"
                                class="mr-0"
                              />
                            </label>
                          </td>
                          <td>
                            <router-link
                              :to="'/user/view/' + inbox.stage_id"
                              class="nav-item"
                            >
                              <va-button
                                icon="ads_click"
                                style="background-color: rgb(47, 148, 91)"
                              >
                                เปิด
                              </va-button>
                            </router-link>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div
                    align="center"
                    style="padding-top: 30px"
                    v-if="data.inbox_isLoad"
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
        inbox_array: new Array(),
        inbox_isLoad: true,
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
              }
            });

            this.axios.get("api/user/get/inbox").then(async (res) => {
              if (res.data.status == true) {
                for await (let to_user of res.data.sendto_user) {
                  this.data.inbox_array.push(to_user);
                }
                for await (let group of res.data.sendto_group) {
                  for await (let to_group of group)
                    this.data.inbox_array.push(to_group);
                }
                this.data.inbox_isLoad = false;
              }
            });
          }
        }
      });
    },

    BuddishDate(date) {
      if (date) {
        var date = new Date(date);
        return date.toLocaleDateString("th-TH", {
          day: "numeric",
          month: "short",
          year: "numeric",
        });
      } else {
        return false;
      }
    },
  },
};
</script>
