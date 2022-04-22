<style>
* {
  font-family: "Kanit", sans-serif;
}

.router-link:link {
  text-decoration: none !important;
}

.router-link:hover {
  text-decoration: none !important;
}

.router-link:active {
  text-decoration: none !important;
}

.router-link:visited {
  text-decoration: none !important;
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
