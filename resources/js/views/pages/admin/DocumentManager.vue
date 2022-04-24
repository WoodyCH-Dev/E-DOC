<template>
  <div class="row">
    <div class="flex xl12 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>จัดการเอกสาร</va-card-title>
        <va-card-content>
          <div class="row">
            <div class="flex xl12 xs12" align="right">
              <va-button icon="add" class="mr-2" color="primary">
                เพิ่มเอกสาร
              </va-button>
            </div>
            <div class="flex xl12 xs12">
              <div class="form-group">
                <div class="va-table-responsive" style="overflow-y: auto">
                  <table class="va-table" style="width: 100%">
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
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>X</td>
                        <td>Y</td>
                        <td>T</td>
                        <td>T</td>
                        <td>
                          <va-button
                            icon="approval"
                            class="mr-2"
                            style="background-color: rgb(47, 148, 91)"
                          >
                            ติดตามเอกสาร
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
    },
  },
};
</script>
