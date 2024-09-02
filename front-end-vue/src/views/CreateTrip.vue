<script>
import { store } from '@/data/store';
import axios from 'axios';
import PlaneLoader from '../components/partials/PlaneLoader.vue';

export default {
  name: 'createTrip',
  components: {
    PlaneLoader,
  },
  data() {
    return {
      tripObj: {
        name: '',
        start_date: '',
        end_date: '',
        image: null,
        description: '',
      },
      isValidData: true,
      isLoading: false,
      errors: {},
    };
  },
  methods: {
    handleFileUpload(event) {
      this.tripObj.image = event.target.files[0];
      this.errors.image = '';
    },
    removeImage() {
      this.tripObj.image = null;
      this.$refs.imageInput.value = '';
      this.errors.image = '';
    },
    sendData() {
      const formData = new FormData();
      formData.append('name', this.tripObj.name);
      formData.append('start_date', this.tripObj.start_date);
      formData.append('end_date', this.tripObj.end_date);
      formData.append('description', this.tripObj.description);
      if (this.tripObj.image) {
        formData.append('image', this.tripObj.image);
      }

      axios.post(store.apiUrl + 'create-trip', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
        .then(res => {
          this.isLoading = true;
          this.errors = {};
          this.tripObj = {
            name: '',
            start_date: '',
            end_date: '',
            image: null,
            description: '',
          };
          setTimeout(() => {
            this.isLoading = false;
            this.$router.push({ name: 'home' });
          }, 2500);
        })
        .catch(err => {
          this.isLoading = false;
          this.errors = err.response.data.errors || {};
        });
    }
  }
};
</script>



<template>
  <div>
    <h1 class="text-center py-5">Pianifica un nuovo viaggio</h1>

    <PlaneLoader v-if="isLoading" />

    <form v-else @submit.prevent="sendData()" class="container mb-5">
      <div class="row">
        <!-- Nome del viaggio -->
        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group" :class="{ 'mb-3': !errors?.name }">
            <span class="input-group-text" id="inputGroup-sizing-default">Nome del viaggio</span>
            <input v-model="tripObj.name" type="text" class="form-control" aria-label="Sizing example input"
              aria-describedby="inputGroup-sizing-default">
          </div>
          <small v-if="errors.name" class="text-white">{{ errors.name }}</small>
        </div>

        <!-- Data di inizio -->
        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group" :class="{ 'mb-3': !errors?.start_date }">
            <span class="input-group-text" id="inputGroup-sizing-default">Data di inizio</span>
            <input v-model="tripObj.start_date" type="date" class="form-control" aria-label="Sizing example input"
              aria-describedby="inputGroup-sizing-default">
          </div>
          <small v-if="errors.start_date" class="text-white">{{ errors.start_date }}</small>
        </div>

        <!-- Data di fine -->
        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group" :class="{ 'mb-3': !errors?.end_date }">
            <span class="input-group-text" id="inputGroup-sizing-default">Data di fine</span>
            <input v-model="tripObj.end_date" type="date" class="form-control" aria-label="Sizing example input"
              aria-describedby="inputGroup-sizing-default">
          </div>
          <small v-if="errors.end_date" class="text-white">{{ errors.end_date }}</small>
        </div>

        <!-- Immagine -->
        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group" :class="{ 'mb-3': !errors?.image }">
            <input ref="imageInput" type="file" class="form-control" id="inputGroupFile04" @change="handleFileUpload"
              aria-describedby="inputGroupFileAddon04" aria-label="Upload">
            <button class="btn btn-light" type="button" id="inputGroupFileAddon04" @click="removeImage">Rimuovi</button>
          </div>
          <small v-if="errors.image" class="text-white">{{ errors.image === 'The image failed to upload.' ? 'Impossibile caricare l\'immagine.' : errors.image }}</small>
        </div>

        <!-- Descrizione -->
        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="form-floating" :class="{ 'mb-3': !errors?.description }">
            <textarea v-model="tripObj.description" class="form-control" id="floatingTextarea"></textarea>
            <label for="floatingTextarea">Descrizione</label>
          </div>
          <small v-if="errors.description" class="text-white">{{ errors.description }}</small>
        </div>

        <!-- Pulsante di invio -->
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
