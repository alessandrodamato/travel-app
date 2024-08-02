<script>
  export default {
    
  }
</script>



<template>

  <header class="d-flex align-items-center justify-content-end px-5">

    <div class="logo w-100 text-center fw-bold">Travel App</div>
    <div class="actions position-absolute">
      <router-link class="btn btn-outline-success border-0 me-2"><i class="fa-regular fa-map"></i></router-link>
      <button class="btn btn-outline-success border-0"><i class="fa-solid fa-plus"></i></button>
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