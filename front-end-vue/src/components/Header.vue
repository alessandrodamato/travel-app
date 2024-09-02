<script>
  export default {
    
  }
</script>



<template>

  <header class="d-flex align-items-center justify-content-end px-5">

    <div class="logo w-100 text-sm-center"><router-link class="fw-bold text-decoration-none text-black" to="/">Travel App</router-link></div>
    
    <div class="actions position-absolute">
      <router-link class="btn btn-outline-success border-0 me-2" to="/map"><i class="fa-regular fa-map"></i></router-link>
      <router-link class="btn btn-outline-success border-0" to="/create"><i class="fa-solid fa-plus"></i></router-link>
    </div>
    
  </header>

</template>



<style lang="scss" scoped>

@use '../assets/scss/partials/vars.scss' as *;

header {
  height: 50px;
  background-color: $color-1;
}

</style>