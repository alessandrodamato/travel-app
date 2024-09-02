<script>
import { markRaw } from 'vue';
import { store } from '@/data/store';
import { formatDate } from '@/assets/js/utils';
import { Modal } from 'bootstrap';
import axios from 'axios';
import tt from '@tomtom-international/web-sdk-maps';
import '@tomtom-international/web-sdk-maps/dist/maps.css';
import PlaneLoader from '@/components/partials/PlaneLoader.vue';

export default {
  name: 'dayDetail',
  components: {
    PlaneLoader,
  },
  data() {
    return {
      isLoading: true,
      day: {},
      error: '',
      images: [],
      newImages: [],
      uploadError: '',
      map: null,
      stops: [],
      mapMarkers: [],
      currentLng: null,
      currentLat: null,
      popup: null,
      stopToDelete: null,
    };
  },
  methods: {
    getDay() {
      axios.get(`${store.apiUrl}day-detail/${this.$route.params.slug}/${this.$route.params.date}`)
        .then(res => {
          this.isLoading = false;
          this.day = res.data;
          this.images = res.data.images || [];
          this.getStops();
        })
        .catch(err => {
          this.isLoading = false;
          this.error = err.response.data.message;
          this.$router.push({ name: 'home' });
        });
    },

    getStops() {
      axios.get(`${store.apiUrl}stops/${this.day.id}`)
        .then(response => {
          this.stops = response.data.stops;
          this.stops.forEach(stop => {
            this.addStop(stop.longitude, stop.latitude, stop.name, stop.id);
          });
        })
        .catch(err => {
          console.error('Errore nel caricamento delle tappe:', err.response ? err.response.data : err.message);
        });
    },

    formattedDate(date) {
      return formatDate(date);
    },

    getImageUrl(imagePath) {
      const baseUrl = store.apiUrl.replace('/api', '');
      return `${baseUrl}storage/${imagePath}`;
    },

    handleFileUpload(event) {
      this.newImages = Array.from(event.target.files);
    },

    uploadImages() {
      if (this.newImages.length === 0) {
        this.uploadError = 'Nessuna immagine selezionata per il caricamento.';
        return;
      }

      const formData = new FormData();
      this.newImages.forEach(file => {
        formData.append('images[]', file);
      });
      formData.append('day_id', this.day.id);

      axios.post(`${store.apiUrl}upload-images`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        }
      })
        .then(response => {
          this.images = [...this.images, ...response.data.images];
          this.newImages = [];

          const inputFile = document.getElementById('inputGroupFile02');
          if (inputFile) {
            inputFile.value = '';
          }

          this.uploadError = '';
        })
        .catch(error => {
          console.log(error.response.data);

          this.uploadError = error.response.data.message;
        });
    },

    initMap() {
      const mapElement = document.getElementById('map');
      if (mapElement) {
        this.map = markRaw(tt.map({
          key: 'vXbg83rCHzRwMlWhyAwXADZhspyjYHPO',
          container: mapElement,
          center: [12.4964, 41.9028],
          zoom: 5,
        }));

        this.map.on('dblclick', (e) => {
          const { lng, lat } = e.lngLat;
          this.addStop(lng, lat);
        });
      } else {
        console.error('Elemento con id "map" non trovato.');
      }
    },

    addStop(lng, lat, name = '', stopId = null) {
      const marker = new tt.Marker()
        .setLngLat([lng, lat])
        .addTo(this.map);

      if (stopId) {
        this.mapMarkers.push({ stopId, marker });
      }

      if (name) {
        const popupContent = document.createElement('div');
        const strongElement = document.createElement('strong');
        strongElement.textContent = name;
        const closeButton = document.createElement('span');
        closeButton.className = 'd-none position-absolute top-0 end-0 px-1 cp text-danger';
        closeButton.textContent = 'x';
        popupContent.appendChild(strongElement);
        popupContent.appendChild(closeButton);

        popupContent.addEventListener('mouseenter', () => {
          closeButton.classList.remove('d-none');
          closeButton.classList.add('d-inline');
        });

        popupContent.addEventListener('mouseleave', () => {
          closeButton.classList.remove('d-inline');
          closeButton.classList.add('d-none');
        });

        closeButton.addEventListener('click', () => {
          if (stopId) {
            this.confirmDelete(stopId);
          }
        });

        new tt.Popup({ closeButton: false, closeOnClick: false })
          .setLngLat([lng, lat])
          .setDOMContent(popupContent)
          .addTo(this.map);
      } else {
        this.popup = new tt.Popup({
          closeButton: false,
          closeOnClick: false,
        })
          .setLngLat([lng, lat])
          .setHTML(`
        <div class="pt-1">
          <input type="text" placeholder="Nome della tappa" id="stopName" class="form-control mb-3" />
          <button class="btn btn-dark" id="saveButton">Aggiungi</button>
        </div>
      `)
          .addTo(this.map);

        this.currentLng = lng;
        this.currentLat = lat;

        const stopNameInput = document.getElementById('stopName');
        const saveButton = document.getElementById('saveButton');

        if (stopNameInput) {
          stopNameInput.addEventListener('keyup', this.handleKeyUpEnter);
        }

        if (saveButton) {
          saveButton.addEventListener('click', this.renameStop);
        }
      }
    },

    handleKeyUpEnter(event) {
      if (event.key === 'Enter') {
        this.renameStop();
      }
    },

    renameStop() {
      const stopName = document.getElementById('stopName')?.value;
      if (stopName && this.currentLng !== null && this.currentLat !== null) {
        this.saveStop({ name: stopName, lng: this.currentLng, lat: this.currentLat });

        if (this.popup) {
          this.popup.remove();
          this.popup = null;
        }

        const stopNameInput = document.getElementById('stopName');
        const saveButton = document.getElementById('saveButton');

        if (stopNameInput) {
          stopNameInput.removeEventListener('keyup', this.handleKeyUpEnter);
        }

        if (saveButton) {
          saveButton.removeEventListener('click', this.renameStop);
        }
      }
    },

    saveStop(stop) {
      const stopData = {
        day_id: this.day.id,
        name: stop.name,
        latitude: stop.lat,
        longitude: stop.lng,
      };

      axios.post(`${store.apiUrl}stops`, stopData)
        .then(response => {
          this.stops.push(response.data.stop);
          this.addStop(response.data.stop.longitude, response.data.stop.latitude, response.data.stop.name, response.data.stop.id);
        })
        .catch(err => {
          console.error('Errore nel salvataggio della tappa:', err.response ? err.response.data : err.message);
        });
    },

    confirmDelete(stopId) {
      this.stopToDelete = stopId;
      const confirmDeleteButton = document.getElementById('confirmDeleteButton');
      const modalElement = document.getElementById('confirmDeleteModal');

      if (confirmDeleteButton && modalElement) {
        const modal = new Modal(modalElement);
        modal.show();

        confirmDeleteButton.addEventListener('click', () => {
          this.deleteStop(this.stopToDelete);
        });
      }
    },

    deleteStop(stopId) {
      axios.delete(`${store.apiUrl}stops/${stopId}`)
        .then(() => {
          this.stops = this.stops.filter(stop => stop.id !== stopId);

          location.reload();
        })
        .catch(err => {
          console.error('Errore durante l\'eliminazione della tappa:', err.response ? err.response.data : err.message);
        });
    },
  },

  watch: {
    isLoading(newValue) {
      if (!newValue) {
        this.$nextTick(() => {
          this.initMap();
        });
      }
    }
  },

  mounted() {
    this.getDay();
  },
};
</script>

<template>
  <div>
    <h2 v-if="error !== ''" class="text-center mt-5">{{ error }}</h2>

    <PlaneLoader v-if="isLoading" class="mt-5" />

    <div v-else>

      <div class="container trip-detail my-5">

        <h2 class="text-center">{{ formattedDate(day.date) }}</h2>
        <p v-if="day.description" class="description mt-3">{{ day.description }}</p>

        <div v-if="images.length > 0" class="carousel-container mt-5">
          <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
              <button v-for="(image, index) in images" :key="index" type="button"
                :data-bs-target="'#carouselExampleIndicators'" :data-bs-slide-to="index"
                :class="{ active: index === 0 }" aria-current="true" aria-label="'Slide ' + (index + 1)"></button>
            </div>
            <div class="carousel-inner">
              <div v-for="(image, index) in images" :key="index" :class="['carousel-item', { active: index === 0 }]">
                <img :src="getImageUrl(image.path)" class="d-block img-fluid" :alt="image.name">
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
              data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Precedente</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
              data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Successivo</span>
            </button>
          </div>
        </div>

        <div class="mt-5 row">
          <div class="col-12 col-md-8 offset-md-2">
            <div class="input-group mb-3">
              <input type="file" class="form-control" id="inputGroupFile02" ref="fileInput" multiple
                @change="handleFileUpload">
              <button class="btn btn-success input-group-text" @click="uploadImages">
                Carica Immagini
              </button>
            </div>
            <p v-if="uploadError" class="text-danger mt-2 text-center">
              {{ uploadError === 'The images.0 failed to upload.' || uploadError === 'The images.1 failed to upload.' ||
              uploadError === 'The images.2 failed to upload.' ? 'Impossibile caricare l\'immagine.' : uploadError }}
            </p>
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
                Sei sicuro di voler eliminare questa tappa?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Elimina</button>
              </div>
            </div>
          </div>
        </div>
        <!-- /Modal -->

      </div>

      <div class="container my-5">
        <div id="map"></div>
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
  box-shadow: none;

  @media (max-width: 768px) {
    padding: 1rem;
  }
}

.description {
  background-color: rgba($color-2, .1);
  padding: 1rem;
  border-radius: 5px;
  font-size: 1.2rem;

  @media (max-width: 768px) {
    font-size: 1rem;
  }
}

.carousel-container {
  position: relative;
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
}

.carousel-item img {
  width: 100%;
  height: auto;
  max-height: 500px;
  object-fit: cover;
}

#map {
  width: 100%;
  height: 500px;
}
</style>
