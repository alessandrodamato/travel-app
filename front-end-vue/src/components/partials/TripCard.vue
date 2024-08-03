<script>
export default {

}
</script>



<template>

  <div class="trip d-flex flex-wrap mb-4 p-3 rounded-3">

    <!-- // lato di sinistra -->
    <div class="trip-left w-100 pe-1">
      <h4 class="title fw-bold">Viaggio ad amalfi</h4>
      <p class="description">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Odio repellendus velit iure animi
        neque repellat quae, obcaecati nostrum quisquam amet molestiae accusamus. Eius aut voluptas molestiae
        consequuntur dolore dolorum ipsum.</p>
    </div>

    <!-- // lato di destra -->
    <div class="trip-right w-100 ps-1">
      <img src="https://picsum.photos/1000/1000" alt="Trip Image" class="img-fluid thumb rounded-3">
      <div class="text-end pt-3 fs-09">02/08/2024 - 01/09/2024</div>
    </div>

  </div>

</template>



<style lang="scss" scoped>

@use '../../assets/scss/partials/vars.scss' as *;

.trip{
  background-color: $color-1;
  .title{
    color: $color-2;
  }
  .thumb{
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    object-position: center;
  }
}

@media screen and (min-width: 576px) {
  .trip-left{
    width: 60% !important;
  }
  .trip-right{
    width: 40% !important;
  }
}

</style>