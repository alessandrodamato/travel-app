<script>
import { formatDate } from '../../assets/js/utils';
export default {
  props:{
    tripObj: Object
  },
  methods:{
    formattedDate(date){
      return formatDate(date);
    }
  }
}
</script>



<template>

  <div class="trip d-flex flex-wrap mb-4 p-3 rounded-3" @click="this.$router.push({name: 'tripDetail', params: {slug: tripObj.slug}})">

    <!-- // lato di sinistra -->
    <div class="trip-left w-100 pe-1">
      <h4 class="title fw-bold">{{ tripObj.name }}</h4>
      <p class="description">{{ tripObj.description }}</p>
    </div>

    <!-- // lato di destra -->
    <div class="trip-right w-100 ps-1">
      <img
        :src="tripObj.image ? 'http://127.0.0.1:8000/storage/' + tripObj.image : '/img/noimage.jpg'"
        onerror="this.src = '/img/noimage.jpg'"
        :alt="tripObj.name"
        class="img-fluid thumb rounded-3">
      <div class="text-end pt-3 fs-09">{{ formattedDate(tripObj.start_date) }} - {{ formattedDate(tripObj.end_date) }}</div>
    </div>

  </div>

</template>



<style lang="scss" scoped>

@use '../../assets/scss/partials/vars.scss' as *;

.trip{
  cursor: pointer;
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