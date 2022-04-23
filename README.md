# Laravel 8 with Vue 3 E-DOC Project

E-DOC หรือระบบ เอกสารในรูปแบบอิเล็กทรอนิกส์

คือ ไฟล์เอกสาร ไฟล์รูปภาพ ฯลฯ โดยประเภทของเอกสารอิเล็กทรอนิกส์ที่นิยมใช้กันในปัจจุบันคือ PDF เพราะมีจุดเด่นที่คงรูปแบบการแสดงผลของเอกสาร PDF เป็นรูปแบบหนึ่งในการจัดทำเอกสารอิเล็กทรอนิกส์ และมีการพัฒนามาเรื่อยๆ เริ่มตั้งแต่ PDF ที่เก็บเป็นภาพ เช่น การสแกนเอกสารกระดาษ ต่อมาได้มีการพัฒนาให้ไฟล์ PDF เก็บข้อมูลแบบเพิ่ม-ลดช่องกรอกได้ เรียกว่า PDF/XFA แต่ยังมีข้อจำกัดในการแนบไฟล์อื่น ๆ เพิ่ม จึงมีการพัฒนาต่อเป็นตัวปัจจุบันคือ รูปแบบ PDF/A ที่สามารถเก็บในระยะยาว เนื่องจากมีการเก็บค่าต่าง ๆ ที่จำเป็นต่อการเปิดใช้งานไฟล์ไว้แล้ว โดยเฉพาะใน PDF/A-3 ที่มีคุณสมบัติในการแนบไฟล์ได้หลากหลายประเภท ส่วนผู้ใช้เอกสารก็สามารถอ่านข้อมูลจากไฟล์ PDF ได้



พัฒนาด้วย Laravel 8 และ Vue 3



*ถ้าหากมีปัญหาสามารถลงปัญหาใน Github ของโปรเจคได้ เนื่องจาก Website Wutthiphon Space กำลังพัฒนาอยู่เช่นกัน


## Authors

- [@Wutthiphon](https://www.github.com/Wutthiphon)


## Feedback

พบปัญหา ติชม แนะนำ https://wutthiphon.space/admin/projects


## Installation

ให้รันคำสั่งตามนี้

```bash
  cd <ตำแหน่งที่อยู่ไฟล์>
  composer install
  npm install --force
```
จากหลัง npm install เสร็จให้เปลี่ยนชื่อไฟล์ .env.example เป็น .env จากนั้นเปิดไฟล์ แก้ไขตามนี้
```bash
  APP_URL=<ที่อยู่ Server/Subfolder> //เช่น https://wutthiphon.space/Projects/demo/e-doc
  DB_HOST=<IP ที่อยู่ฐานข้อมูล MySQL> //เช่น 127.0.0.1
  DB_PORT=3306 //Port ของ MySQL Server
  DB_DATABASE=<ชื่อฐานข้อมูล>
  DB_USERNAME=root //DB User
  DB_PASSWORD= //DB Pass
```
หลังจากแก้ไขไฟล์ .env เสร็จแล้ว
```bash
  php artisan key:generate
  php artisan jwt:secret
```
