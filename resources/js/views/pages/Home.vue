<template>
  <div class="row">
    <div class="flex md6 lg4 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>กรุณาเข้าสู่ระบบ | Please Login.</va-card-title>
        <va-card-content>
          <form v-on:submit.prevent="onSubmit">
            <div class="form-group">
              <b>E-mail</b>
              <va-input v-model="form.email" placeholder="E-mail" />
            </div>
            <div class="form-group">
              <b>รหัสผ่าน</b>
              <va-input
                v-model="form.password"
                placeholder="รหัสผ่าน"
                type="password"
              />
            </div>
            <div class="form-group">
              <va-checkbox v-model="form.remember_me" label="จดจำฉัน" />
            </div>
            <div class="form-group" v-if="form_validate.error == true">
              <b style="color: rgb(255, 79, 79)">
                <i class="fal fa-exclamation-circle"></i>
                ไม่สามารถเข้าสู่ระบบได้ กรุณาตรวจสอบ
              </b>
            </div>
            <div class="form-group">
              <va-button
                color="info"
                gradient
                class="full-width"
                style="margin-bottom: 10px"
                type="submit"
              >
                <i class="far fa-sign-in"></i>&nbsp;เข้าสู่ระบบ
              </va-button>
              <va-button
                color="danger"
                gradient
                class="full-width"
                @click="onSubmitWithGoogle"
              >
                <i class="fab fa-google"></i>&nbsp;เข้าสู่ระบบด้วย Google
              </va-button>
            </div>
          </form>
        </va-card-content>
      </va-card>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    var email = "";
    var password = "";
    var remember_me = false;

    if (window.localStorage.getItem("user_id")) {
      this.$router.push("/user/dashboard");
    }

    if (window.sessionStorage.getItem("remember_me")) {
      email = window.sessionStorage.getItem("email");
      password = window.sessionStorage.getItem("password");
      remember_me = true;
    }

    return {
      form: { email: email, password: password, remember_me: remember_me },
      form_validate: { error: false },
    };
  },
  methods: {
    onSubmit() {
      this.axios.post("api/login", this.form).then((res) => {
        if (res.data.status == true) {
          this.form_validate.error = false;
          window.localStorage.setItem("user_id", res.data.userdata.id);
          window.localStorage.setItem("name", res.data.userdata.name);
          window.localStorage.setItem("lastname", res.data.userdata.lastname);
          window.localStorage.setItem("email", res.data.userdata.email);
          window.localStorage.setItem("permission", res.data.userpermission);
          window.localStorage.setItem(
            "google_uid",
            res.data.userdata.google_uid
          );
          window.localStorage.setItem(
            "access_token",
            res.data.token.original.access_token
          );
          if (this.form.remember_me == true) {
            window.sessionStorage.setItem("email", this.form.email);
            window.sessionStorage.setItem("password", this.form.password);
            window.sessionStorage.setItem("remember_me", true);
          } else {
            window.sessionStorage.removeItem("email");
            window.sessionStorage.removeItem("password");
            window.sessionStorage.removeItem("remember_me");
          }
          window.location.reload();
        } else {
          this.form_validate.error = true;
        }
      });
    },

    async onSubmitWithGoogle() {
      try {
        const googleUser = await this.$gAuth.signIn();
        if (!googleUser) {
          return null;
        }
        var google_gmail = googleUser.Ru.Hv;
        var google_uid = googleUser.Ru.fX;

        this.axios
          .post("api/loginwithgoogle", {
            google_gmail: google_gmail,
            google_uid: google_uid,
          })
          .then((res) => {
            if (res.data.status == true) {
              this.form_validate.error = false;
              window.localStorage.setItem("user_id", res.data.userdata.id);
              window.localStorage.setItem("name", res.data.userdata.name);
              window.localStorage.setItem(
                "lastname",
                res.data.userdata.lastname
              );
              window.localStorage.setItem("email", res.data.userdata.email);
              window.localStorage.setItem(
                "permission",
                res.data.userpermission
              );
              window.localStorage.setItem(
                "google_uid",
                res.data.userdata.google_uid
              );
              window.localStorage.setItem(
                "access_token",
                res.data.token.original.access_token
              );
              window.location.reload();
            } else {
              this.$swal.fire(
                "Error!",
                "ไม่พบผู้ใช้ที่เชื่อมต่อบัญชี google นี้ <br>" + google_gmail,
                "error"
              );
            }
          });
      } catch (error) {
        //on fail do something
        return null;
      }
    },
  },
};
</script>
