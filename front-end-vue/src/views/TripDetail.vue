<script>
import { store } from '@/data/store';
import { formatDate } from '../assets/js/utils';
import { Modal } from 'bootstrap';
import axios from 'axios';
import PlaneLoader from '@/components/partials/PlaneLoader.vue';

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
    },
    deleteTrip() {
      const modalElement = document.getElementById('confirmDeleteModal');
      const confirmDeleteButton = document.getElementById('confirmDeleteButton');

      if (modalElement && confirmDeleteButton) {
        const modal = new Modal(modalElement);
        modal.show();

        confirmDeleteButton.addEventListener('click', () => {
          this.confirmDeleteTrip();
          modal.hide();
        }, { once: true });
      }
    },
    confirmDeleteTrip() {
      axios.delete(`${store.apiUrl}trip-detail/${this.$route.params.slug}`)
        .then(() => {
          this.$router.push({ name: 'home' });
        })
        .catch(error => {
          console.error('Error deleting trip:', error);
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
      <div class="d-flex justify-content-center align-items-center flex-wrap mb-4">
        <h1 class="text-center trip-name mx-2 my-0">
          <span v-if="!isEditing">{{ trip.name }}</span>
          <div v-else>
            <input v-model="editedTrip.name" class="form-control" @keyup.enter="saveTrip" />
            <div v-if="errors.name" class="text-danger">
              {{ errors.name }}
            </div>
          </div>
        </h1>
        <button @click="isEditing ? saveTrip() : toggleEdit()" class="btn btn-warning mx-2">
          <i :class="isEditing ? 'fa-solid fa-check' : 'fa-regular fa-pen-to-square'"></i>
        </button>
        <button @click="deleteTrip" class="btn btn-danger mx-2">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>

      <div class="text-center mb-3">
        <img :src="trip.image ? 'http://127.0.0.1:8000/storage/' + trip.image : '/img/noimage.jpg'"
          onerror="this.src = '/img/noimage.jpg'" :alt="trip.name" class="img-fluid rounded-3 trip-image">
      </div>

      <div class="text-center trip-dates">
        <span>{{ formattedDate(trip.start_date) }} - {{ formattedDate(trip.end_date) }}</span>
      </div>

      <div class="trip-description">
        <p v-if="!isEditing">{{ trip.description }}</p>
        <div v-else>
          <textarea v-model="editedTrip.description" class="form-control" @keyup.enter.prevent="saveTrip"
            @keydown.enter.prevent></textarea>
          <div v-if="errors.description" class="text-danger">
            {{ errors.description }}
          </div>
        </div>
      </div>

      <div class="trip-days">
        <h3>Giornate</h3>
        <ul>
          <li class="cp" v-for="(day, index) in editedTrip.days" :key="day.id"
            @click="!isEditing ? this.$router.push({ name: 'dayDetail', params: { slug: this.$route.params.slug, date: day.date } }) : ''">
            <div>
              <strong>{{ formattedDate(day.date) }}:</strong>
              <textarea v-if="isEditing" v-model="editedTrip.days[index].description" class="form-control"
                @keyup.enter.prevent="saveTrip" @keydown.enter.prevent></textarea>
              <div v-else>
                {{ day.description || 'Nessuna descrizione' }}
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Conferma Eliminazione</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Sei sicuro di voler cancellare questo viaggio? Questa azione è irreversibile.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteButton">Elimina</button>
          </div>
        </div>
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

.trip-image {
  width: 250px;
  aspect-ratio: 1 / 1;
  object-fit: cover;
}

.trip-name {
  color: $color-2;
  font-weight: bold;
  font-size: 1.2rem;
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

@media screen and (min-width:576px) {
  .trip-name {
    font-size: 2.5rem;
  }
}
</style>
