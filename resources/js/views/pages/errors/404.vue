<template>
  <div class="row">
    <div class="flex xs12">
      <va-card color="warning" gradient>
        <va-card-title>พบข้อผิดพลาด | Error</va-card-title>
        <va-card-content>
          <b>รหัสข้อผิดพลาด: 404</b><br />
          <label>Error Code: 404</label><br /><br />
          <va-card style="background-color: rgb(255, 236, 236)">
            <va-card-content>
              <label style="font-size: 14pt">ข้อความรายละเอียด:</label>
              <br /><br />
              หน้า: {{ $route.fullPath }}<br />
              คำอธิบาย:
              <u> ไม่พบหน้าที่ระบุ กรุณาย้อนกลับหรือกดปุ่มกลับสู่หน้าหลัก </u>
              <br /><br />
              <label style="color: rgb(255, 68, 77)">
                *หากคิดว่านี่เป็นข้อผิดพลาดของระบบแอพพลิเคชั่นกรุณา Report มาที่
                <va-button size="small" color="danger" class="mr-4">
                  คลิก
                </va-button>
              </label>
            </va-card-content>
          </va-card>
          <br />
          <va-button
            color="danger"
            gradient
            class="mr-4"
            style="width: 100%"
            to="/"
          >
            กลับสู่หน้าหลัก
          </va-button>
        </va-card-content>
      </va-card>
    </div>
  </div>
</template>
