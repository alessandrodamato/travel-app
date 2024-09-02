<script>
import { markRaw } from 'vue';
import { store } from '@/data/store';
import { Modal } from 'bootstrap';
import axios from 'axios';
import tt from '@tomtom-international/web-sdk-maps';
import '@tomtom-international/web-sdk-maps/dist/maps.css';

export default {
  name: 'TTMap',
  data() {
    return {
      map: null,
      stops: [],
      mapMarkers: [],
      stopToDelete: null,
    };
  },
  methods: {
    fetchStops() {
      axios.get(`${store.apiUrl}all-stops`)
        .then(response => {
          this.stops = response.data.stops;
          this.stops.forEach(stop => {
            this.addStopToMap(stop.longitude, stop.latitude, stop.name, stop.id, stop.day.trip.name, stop.day.trip.slug);
          });
        })
        .catch(error => {
          console.error('Errore nel caricamento delle tappe:', error.response ? error.response.data : error.message);
        });
    },

    initMap() {
      const mapElement = document.getElementById('map');
      if (mapElement) {
        this.map = markRaw(tt.map({
          key: 'vXbg83rCHzRwMlWhyAwXADZhspyjYHPO',
          container: mapElement,
          center: [10, 40],
          zoom: 4,
        }));
      } else {
        console.error('Elemento con id "map" non trovato.');
      }
    },

    addStopToMap(lng, lat, name = '', stopId = null, tripName = '', slug) {
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
        const tripElement = document.createElement('div');
        tripElement.textContent = `Viaggio: ${tripName}`;
        tripElement.className = 'cp text-decoration-none text-black'
        const closeButton = document.createElement('span');
        closeButton.className = 'd-none position-absolute top-0 end-0 px-1 cp text-danger';
        closeButton.textContent = 'x';
        popupContent.appendChild(strongElement);
        popupContent.appendChild(tripElement);
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
            this.stopToDelete = stopId;
            this.showModal();
          }
        });

        tripElement.addEventListener('click', () => {
          this.$router.push({ name: 'tripDetail', params: { slug: slug}});
        });

        new tt.Popup({ closeButton: false, closeOnClick: false })
          .setLngLat([lng, lat])
          .setDOMContent(popupContent)
          .addTo(this.map);
      }
    },

    showModal() {
      const modalElement = document.getElementById('confirmDeleteModal');
      if (modalElement) {
        const modal = new Modal(modalElement);
        modal.show();
      }
    },

    deleteStop() {
      if (this.stopToDelete !== null) {
        axios.delete(`${store.apiUrl}stops/${this.stopToDelete}`)
          .then(() => {
            this.stops = this.stops.filter(stop => stop.id !== this.stopToDelete);
            this.mapMarkers = this.mapMarkers.filter(marker => marker.stopId !== this.stopToDelete);
            const markerToRemove = this.mapMarkers.find(marker => marker.stopId === this.stopToDelete);
            if (markerToRemove) {
              markerToRemove.marker.remove();
            }
            this.stopToDelete = null;
            this.hideModal();
            location.reload();
          })
          .catch(error => {
            console.error('Errore durante l\'eliminazione della tappa:', error.response ? error.response.data : error.message);
          });
      }
    },

    hideModal() {
      const modalElement = document.getElementById('confirmDeleteModal');
      if (modalElement) {
        const modal = Modal.getInstance(modalElement);
        if (modal) {
          modal.hide();
        }
      }
    },
  },

  mounted() {
    this.initMap();
    this.fetchStops();
  },
};
</script>



<template>
  <div>

    <div id="map"></div>

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
            <button type="button" class="btn btn-danger" @click="deleteStop">Elimina</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /Modal -->

  </div>
</template>



<style scoped>
#map {
  width: 100%;
  height: calc(100vh - 50px);
}
</style>
