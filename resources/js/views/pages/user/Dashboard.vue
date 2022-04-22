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
                  0
                </va-card-content>
              </va-card>
            </div>
            <div class="flex lg3 xs12">
              <va-card color="warning" gradient>
                <va-card-title class="card-title-dashboard">
                  ยอดเอกสารทั้งหมดของปี
                </va-card-title>
                <va-card-content class="card-subtitle-dashboard">
                  0
                </va-card-content>
              </va-card>
            </div>
            <div class="flex lg3 xs12">
              <va-card color="danger" gradient>
                <va-card-title class="text-white card-title-dashboard">
                  จำนวนผู้ใช้งานทั้งหมด
                </va-card-title>
                <va-card-content class="text-white card-subtitle-dashboard">
                  0
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
    // onSubmit() {
    //   this.axios.post("api/login", this.form).then((res) => {
    //     if (res.data.status == true) {
    //       this.form_validate.error = false;
    //       window.localStorage.setItem("user_id", res.data.id);
    //       window.localStorage.setItem("name", res.data.name);
    //       window.localStorage.setItem("lastname", res.data.lastname);
    //       window.localStorage.setItem("email", res.data.email);
    //       window.localStorage.setItem("google_uid", res.data.google_uid);
    //       if (this.form.remember_me == true) {
    //         window.sessionStorage.setItem("email", this.form.email);
    //         window.sessionStorage.setItem("password", this.form.password);
    //         window.sessionStorage.setItem("remember_me", true);
    //       } else {
    //         window.sessionStorage.removeItem("email");
    //         window.sessionStorage.removeItem("password");
    //         window.sessionStorage.removeItem("remember_me");
    //       }
    //       window.location.reload();
    //     } else {
    //       this.form_validate.error = true;
    //     }
    //   });
    // },
  },
};
</script>
