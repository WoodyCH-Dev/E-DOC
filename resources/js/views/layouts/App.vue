<style>
* {
  font-family: "Kanit", sans-serif;
}

a:link {
  color: white;
  text-decoration: none;
}
a:active {
  color: white;
  text-decoration: none;
}
a:hover {
  color: rgb(255, 211, 65);
  text-decoration: none;
}
a:visited {
  color: white;
  text-decoration: none;
}

.header-logo {
  height: 45px;
}

.center {
  margin: auto;
}

.form-group {
  padding-bottom: 15px;
}

.full-width {
  width: 100%;
}

/* footer {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
  text-align: center;
} */

.nav-item {
  cursor: pointer;
}

.text-white {
  color: white;
}

.card-title-dashboard {
  font-size: 12pt !important;
}

.card-subtitle-dashboard {
  font-size: 22pt !important;
}

.data-content {
  z-index: 10000;
  position: fixed;
  top: 0px;
  width: 100%;
  height: 100vh;
}
</style>

<template>
  <Header></Header>
  <!-- <Footer></Footer> -->
</template>

<script>
import Header from "./Header";
import Footer from "./Footer";
export default {
  components: { Header, Footer },
};
</script>
