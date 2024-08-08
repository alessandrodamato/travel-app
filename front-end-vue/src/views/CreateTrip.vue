<script>
import { store } from '@/data/store';
import axios from 'axios';
import PlaneLoader from '../components/partials/PlaneLoader.vue';
export default {
  name: 'createTrip',
  components:{
    PlaneLoader
  },
  data() {
    return {
      tripObj: {
        name: '',
        start_date: '',
        end_date: '',
        description: '',
      },
      isValidData: true,
      isLoading: false,
      errors: ''
    }
  },
  methods: {
    sendData() {
      axios.get(store.apiUrl + 'create-trip', {
        params: this.tripObj
      })
      .then(res => {
        this.isLoading = true;
        this.errors = '';
        this.tripObj = {
          name: '',
          start_date: '',
          end_date: '',
          description: '',
        }
        setTimeout(() => {
          this.isLoading = false;
          this.$router.push({name: 'home'});
        }, 2500);
      })
      .catch(err => {
        this.isLoading = false;
        this.errors = err.response.data.errors;
      })
    }
  }
}
</script>



<template>
  <div>

    <h1 class="text-center py-5">Pianifica un nuovo viaggio</h1>

    <PlaneLoader v-if="isLoading" />

    <form v-else @submit.prevent="sendData()" class="container mb-5">

      <div class="row">

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group" :class="{'mb-3' : !errors?.name}">
            <span class="input-group-text" id="inputGroup-sizing-default">Nome del viaggio</span>
            <input v-model="tripObj.name" type="text" class="form-control" aria-label="Sizing example input"
              aria-describedby="inputGroup-sizing-default">
            </div>
            <small v-if="errors.name" class="text-white">{{ errors.name }}</small>
        </div>

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group" :class="{'mb-3' : !errors?.start_date}">
            <span class="input-group-text" id="inputGroup-sizing-default">Data di inizio</span>
            <input v-model="tripObj.start_date" type="date" class="form-control" aria-label="Sizing example input"
              aria-describedby="inputGroup-sizing-default">
          </div>
          <small v-if="errors.start_date" class="text-white">{{ errors.start_date }}</small>
        </div>

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group" :class="{'mb-3' : !errors?.end_date}">
            <span class="input-group-text" id="inputGroup-sizing-default">Data di fine</span>
            <input v-model="tripObj.end_date" type="date" class="form-control" aria-label="Sizing example input"
              aria-describedby="inputGroup-sizing-default">
          </div>
          <small v-if="errors.end_date" class="text-white">{{ errors.end_date }}</small>
        </div>

        <!-- <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group mb-3">
            <input type="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload" multiple>
            <button class="btn btn-secondary " type="button" id="inputGroupFileAddon04">Rimuovi</button>
          </div>
        </div> -->

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="form-floating" :class="{'mb-3' : !errors?.description}">
            <textarea v-model="tripObj.description" class="form-control"
              id="floatingTextarea"></textarea>
            <label for="floatingTextarea">Descrizione</label>
          </div>
          <small v-if="errors.description" class="text-white">{{ errors.description }}</small>
        </div>

        <div class="col-12 col-lg-6 offset-lg-3">
          <button type="submit" class="btn btn-light float-end">Crea</button>
        </div>

      </div>

    </form>

  </div>
</template>



<style lang="scss" scoped>
@use '../assets/scss/partials/vars.scss' as *;

form {
  padding: 150px 20px;
  background-color: rgba($color-2, .4);
}

input:focus,
textarea:focus,
button {
  border-color: #e1e4e8;
  outline: none !important;
  box-shadow: none !important;
}

@media screen and (min-width: 576px) {
  form {
    border-radius: 5rem;
  }
}
</style>