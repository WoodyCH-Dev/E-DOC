<template>
  <div class="row">
    <div class="flex md6 lg3 xs12 center">
      <va-card tag="b" outlined>
        <va-card-title>กรุณาเข้าสู่ระบบ | Please Login.</va-card-title>
        <va-card-content>
          <div class="form-group">
            <b>ชื่อผู้ใช้งาน</b>
            <va-input v-model="usernmae" placeholder="ชื่อผู้ใช้งาน" />
          </div>
          <div class="form-group">
            <b>รหัสผ่าน</b>
            <va-input
              v-model="password"
              placeholder="รหัสผ่าน"
              type="password"
            />
          </div>
          <div class="form-group">
            <va-checkbox v-model="remember_me" label="จดจำฉัน" />
          </div>
          <div class="form-group">
            <va-button
              color="info"
              gradient
              class="full-width"
              style="margin-bottom: 10px"
            >
              <i class="far fa-sign-in"></i>&nbsp;เข้าสู่ระบบ
            </va-button>
            <va-button color="danger" gradient class="full-width">
              <i class="fab fa-google"></i>&nbsp;เข้าสู่ระบบด้วย Google
            </va-button>
          </div>
        </va-card-content>
      </va-card>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      usernmae: "",
      password: "",
      remember_me: false,
    };
  },
};
</script>
