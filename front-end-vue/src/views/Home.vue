<script>
  import { store } from '@/data/store';
  import axios from 'axios';
  import TripCard from '../components/partials/TripCard.vue'
  import PlaneLoader from '../components/partials/PlaneLoader.vue'
  export default {
    name: 'home',
    components: {
      TripCard,
      PlaneLoader
    },
    data(){
      return{
        isLoading: true,
        trips: [],
      }
    },
    methods:{
      getTrips(){
        axios.get(store.apiUrl + 'get-trips')
          .then(res => {
            this.isLoading = false;
            this.trips = res.data;
          })
          .catch(err => {
            this.isLoading = false;
            console.log(err.message); 
          })
      }
    },
    mounted(){
      this.getTrips();
    }
  }
</script>



<template>
  <div>

    <h1 class="text-center py-5">I miei viaggi</h1>

    <PlaneLoader v-if="isLoading" />

    <div v-else class="container">

      <div class="row row-cols-1 row-cols-xl-2">

        <!-- trip -->
        <div v-for="(trip, index) in trips" :key="index" class="col">

          <TripCard :tripObj="trip" />

        </div>
        <!-- /trip -->

      </div>

    </div>

  </div>
</template>



<style lang="scss" scoped>
  
</style>