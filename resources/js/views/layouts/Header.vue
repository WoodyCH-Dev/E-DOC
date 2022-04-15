<template>
  <header>
    <va-navbar color="#003153" class="mb-3" shape>
      <template #left>
        <va-navbar-item>
          <router-link to="/">
            <img src="images/logo.png" class="header-logo" />
          </router-link>
        </va-navbar-item>
      </template>

      <template #right>
        <va-navbar-item>
          <router-link to="/">หน้าแรก</router-link>
        </va-navbar-item>
        <va-navbar-item>
          <router-link to="/about">เกี่ยวกับ</router-link>
        </va-navbar-item>
      </template>
    </va-navbar>
  </header>
</template>
