<script>
  import TripCard from '../components/partials/TripCard.vue'
  export default {
    name: 'home',
    components: {
      TripCard
    }
  }
</script>



<template>
  <div>

    <h1 class="text-center py-5">I miei viaggi</h1>

    <div class="container">

      <div class="row row-cols-1 row-cols-xl-2">

        <!-- trip -->
        <div v-for="(trip, index) in 8" :key="index" class="col">

          <TripCard />

        </div>
        <!-- /trip -->

      </div>

    </div>

  </div>
</template>



<style lang="scss" scoped>
  
</style>