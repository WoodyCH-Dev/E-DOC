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
                    v-model="data.acd_year"
                    :options="data.acd_year_options"
                  />
                </div>
              </div>
              <div class="flex xl12 xs12">
                <div class="form-group">
                  <div class="va-table-responsive" style="overflow-y: auto">
                    <table class="va-table" style="width: 100%">
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
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>ไฟล์.pdf</td>
                          <td>ไฟล์.pdf</td>
                          <td>ไฟล์.pdf</td>
                          <td>ไฟล์.pdf</td>
                          <td>ติดตามเอกสาร</td>
                        </tr>
                      </tbody>
                    </table>
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
        acd_year: null,
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
        }
      });

      this.axios.get("api/user/acd_year/lists").then((res) => {
        if (res.data.status == true) {
          for (let year_data of res.data.acd_year) {
            this.data.acd_year_options.push({
              text: Number(year_data.year) + 543,
              id: year_data.id,
            });
          }
        }
      });
    },
  },
};
</script>
