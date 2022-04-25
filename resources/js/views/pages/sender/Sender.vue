<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>ส่งเอกสาร</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl8 xs12">
              <div class="form-group">
                <b>หัวข้อเรื่อง (*)</b>
                <va-input placeholder="เรื่อง..." required />
              </div>
            </div>
            <div class="flex xl4 xs12">
              <div class="form-group">
                <b>เลขที่เอกสาร (*)</b>
                <va-input placeholder="0" required />
              </div>
            </div>
            <div class="flex xl4 xs12">
              <div class="form-group">
                <b>หมวดหมู่ (*)</b>
                <va-select
                  required
                  v-model="form.category_select_value"
                  :options="form.category_select_options"
                  placeholder="กรุณาเลือกหมวดหมู"
                />
              </div>
            </div>
            <div class="flex xl8 xs12">
              <div class="form-group">
                <b>รายละเอียด (ถ้ามี)</b>
                <va-input placeholder="รายละเอียด (ถ้ามี)..." required />
              </div>
            </div>
            <div class="flex xl12 xs12">
              <div class="form-group">
                <b>ไฟล์ที่ Upload</b>
                <div class="va-table-responsive">
                  <table class="va-table" style="width: 100%">
                    <thead>
                      <tr>
                        <th>ลำดับที่</th>
                        <th>ไฟล์ที่แนบ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>ไฟล์.pdf</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="flex xl12 xs12">
              <div class="form-group">
                <va-button>
                  <i class="fas fa-upload mr-2"></i> เลือกไฟล์
                </va-button>
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
                  multiple
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
                  placeholder="ระดับความสำคัญ"
                />
              </div>
            </div>
            <div class="flex xl12 xs12" align="center">
              <div class="form-group">
                <va-button style="background-color: rgb(47, 148, 91)">
                  <i class="fas fa-paper-plane mr-2"></i> ส่งเอกสาร
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

    var category_select_options = [
      "วิชาการ",
      "ทั่วไป",
      "การเงิน",
      "กิจการนักเรียน",
    ];
    var user_select_options = ["user1", "user2"];
    var piority_select_options = ["ทั่วไป", "ด่วน", "ด่วนที่สุด"];

    return {
      data: {
        username: username,
        lastname: lastname,
        acd_year: acd_year,
      },
      permission: {
        access_user: access_user,
        access_sender: access_sender,
        access_admin: access_admin,
      },
      form: {
        category_select_value: "",
        user_select_value: "",
        piority_select_value: piority_select_options[0],
        category_select_options: category_select_options,
        user_select_options: user_select_options,
        piority_select_options: piority_select_options,
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
    },
  },
};
</script>
