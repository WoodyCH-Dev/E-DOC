<template>
  <div class="row">
    <div class="flex xs12 xl12 center">
      <va-card tag="b" outlined>
        <va-card-content>
          <div class="row">
            <div class="flex xl12 xs12">
              <va-card stripe stripe-color="danger">
                <va-card-title class="card-title-dashboard">
                  <i class="far fa-tachometer"></i>&nbsp; Dashboard
                </va-card-title>
                <va-card-content class="card-subtitle-dashboard">
                  ยินดีต้อนรับ {{ data.username + " " + data.lastname }}
                </va-card-content>
              </va-card>
            </div>

            <div class="flex lg3 xs12">
              <va-card color="primary" gradient>
                <va-card-title class="card-title-dashboard">
                  ปีการศึกษา
                </va-card-title>
                <va-card-content class="card-subtitle-dashboard">
                  {{ data.acd_year }}
                </va-card-content>
              </va-card>
            </div>
            <div class="flex lg3 xs12">
              <va-card color="success" gradient>
                <va-card-title class="card-title-dashboard">
                  เอกสารที่จำเป็นต้องตรวจสอบ
                </va-card-title>
                <va-card-content class="card-subtitle-dashboard">
                  {{ data.AllInbox_Unread_count }}
                </va-card-content>
              </va-card>
            </div>
            <div class="flex lg3 xs12">
              <va-card color="warning" gradient>
                <va-card-title class="card-title-dashboard">
                  ยอดเอกสารทั้งหมดของปี
                </va-card-title>
                <va-card-content class="card-subtitle-dashboard">
                  {{ data.AllDoc_count }}
                </va-card-content>
              </va-card>
            </div>
            <div class="flex lg3 xs12">
              <va-card color="danger" gradient>
                <va-card-title class="text-white card-title-dashboard">
                  จำนวนผู้ใช้งานทั้งหมด
                </va-card-title>
                <va-card-content class="text-white card-subtitle-dashboard">
                  {{ data.user_count }}
                </va-card-content>
              </va-card>
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
        user_count: 0,
        AllInbox_Unread_count: 0,
        AllDoc_count: 0,
        inbox_array: new Array(),
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
          this.data.acd_year_id = res.data.acd_year_id;

          this.axios
            .get("api/user/dashboard/getdocumentcount/" + this.data.acd_year_id)
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

      this.axios.get("api/user/get/inbox").then(async (res) => {
        if (res.data.status == true) {
          for await (let to_user of res.data.sendto_user) {
            this.data.inbox_array.push(to_user);
          }
          for await (let group of res.data.sendto_group) {
            for await (let to_group of group)
              this.data.inbox_array.push(to_group);
          }
          this.data.AllInbox_Unread_count = this.data.inbox_array.filter(
            (element) => {
              return element.status == 0;
            }
          ).length;
        }
      });
    },
  },
};
</script>
