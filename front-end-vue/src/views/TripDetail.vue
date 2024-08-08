<script>
import { store } from '@/data/store';
import axios from 'axios';
import PlaneLoader from '@/components/partials/PlaneLoader.vue';
import { formatDate } from '../assets/js/utils';

export default {
  name: 'tripDetail',
  components: {
    PlaneLoader,
  },
  data() {
    return {
      trip: {
        days: []
      },
      isLoading: true,
      isEditing: false,
      editedTrip: {
        days: []
      },
      errors: {}
    };
  },
  methods: {
    getTrip() {
      axios.get(`${store.apiUrl}trip-detail/${this.$route.params.slug}`)
        .then(res => {
          this.trip = res.data;
          this.editedTrip = { ...res.data, days: [...res.data.days] };
          this.isLoading = false;
        })
        .catch(err => {
          this.isLoading = false;
          console.log(err.message);
        });
    },
    formattedDate(date) {
      return formatDate(date);
    },
    toggleEdit() {
      this.isEditing = !this.isEditing;
    },
    saveTrip() {
      const days = Array.isArray(this.editedTrip.days) ? this.editedTrip.days : [];

      axios.put(`${store.apiUrl}trip-detail/${this.$route.params.slug}/edit`, {
        name: this.editedTrip.name,
        description: this.editedTrip.description,
        days: days.map(day => ({
          id: day.id,
          description: day.description
        }))
      })
        .then(response => {
          const updatedSlug = response.data.trip.slug;
          this.trip = { ...response.data.trip };
          this.editedTrip = { ...response.data.trip };
          this.isEditing = false;
          this.errors = {};

          this.$router.push({ name: 'tripDetail', params: { slug: updatedSlug } });
        })
        .catch(error => {
          if (error.response && error.response.data.errors) {
            this.errors = error.response.data.errors;
          } else {
            console.error('Error updating trip:', error);
          }
        });
    }


  },
  mounted() {
    this.getTrip();
  },
};
</script>



<template>
  <div>
    <PlaneLoader v-if="isLoading" class="mt-5" />

    <div v-else class="container trip-detail my-5">
      <div class="d-flex justify-content-center align-items-center">
        <h1 class="text-center py-5 trip-name mx-2 my-0">
          <span v-if="!isEditing">{{ trip.name }}</span>
          <div v-else>
            <input 
              v-model="editedTrip.name" 
              class="form-control"
              @keyup.enter="saveTrip" 
            />
            <div v-if="errors.name" class="text-danger">
              {{ errors.name }}
            </div>
          </div>
        </h1>
        <button @click="isEditing ? saveTrip() : toggleEdit()" class="btn btn-warning mx-2">
          <i :class="isEditing ? 'fa-solid fa-check' : 'fa-regular fa-pen-to-square'"></i>
        </button>
      </div>

      <div class="text-center trip-dates">
        <span>{{ formattedDate(trip.start_date) }} - {{ formattedDate(trip.end_date) }}</span>
      </div>

      <div class="trip-description">
        <p v-if="!isEditing">{{ trip.description }}</p>
        <div v-else>
          <textarea 
            v-model="editedTrip.description" 
            class="form-control"
            @keyup.enter.prevent="saveTrip" 
            @keydown.enter.prevent
          ></textarea>
          <div v-if="errors.description" class="text-danger">
            {{ errors.description }}
          </div>
        </div>
      </div>

      <div class="trip-days">
        <h3>Giornate</h3>
        <ul>
          <li v-for="(day, index) in editedTrip.days" :key="day.id">
            <div>
              <strong>{{ formattedDate(day.date) }}:</strong>
              <textarea 
                v-if="isEditing" 
                v-model="editedTrip.days[index].description" 
                class="form-control"
                @keyup.enter.prevent="saveTrip" 
                @keydown.enter.prevent
              ></textarea>
              <div v-else>
                {{ day.description || 'Nessuna descrizione' }}
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>



<style lang="scss" scoped>
@use '../assets/scss/partials/vars.scss' as *;

.container {
  background-color: $color-1;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.trip-name {
  color: $color-2;
  font-weight: bold;
  font-size: 2.5rem;
}

.trip-dates {
  color: #757575;
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
}

.trip-description {
  background-color: rgba($color-2, .4);
  padding: 1rem;
  border-radius: 5px;
  margin-bottom: 1.5rem;
}

.trip-days {
  background-color: rgba($color-2, .4);
  padding: 1rem;
  border-radius: 5px;
}

.trip-days h3 {
  color: $color-1;
}

.trip-days ul {
  list-style-type: none;
  padding: 0;
}

.trip-days li {
  background-color: white;
  padding: 0.5rem;
  margin-bottom: 0.5rem;
  border-radius: 5px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.text-danger {
  color: red;
  margin-top: 0.5rem;
  font-size: 0.875rem;
}
</style>
