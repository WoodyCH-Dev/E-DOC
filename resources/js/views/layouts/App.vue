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

header {
  position: sticky;
  top: 0;
}
</style>

<template>
  <Header></Header>
  <div class="main row">
    <div class="flex xs11 md10 center">
      <router-view></router-view>
    </div>
  </div>
  <!-- <Footer></Footer> -->
</template>

<script>
import Header from "./Header";
import Footer from "./Footer";
export default {
  components: { Header, Footer },
};
</script>
