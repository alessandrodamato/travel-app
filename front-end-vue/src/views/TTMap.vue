<script>
import { store } from '../data/store'
import tt from '@tomtom-international/web-sdk-maps';
import '@tomtom-international/web-sdk-maps/dist/maps.css'; 
export default {
  name: 'ttmap',
  data() {
    return {
      mapInitialized: false
    };
  },
  methods: {
    initializeMap() {
      if (!store.apiKey) {
        console.error('API key is missing!');
        return;
      }

      tt.setProductInfo("Travel App", "1.0");

      this.map = tt.map({
        key: store.apiKey,
        container: 'map',
        center: [10, 40],
        zoom: 4,
        language: 'it-IT',
      });

      const savedState = localStorage.getItem('mapState');
      if (savedState) {
        const { center, zoom } = JSON.parse(savedState);
        this.map.setCenter(center);
        this.map.setZoom(zoom);
      }

      this.map.on('moveend', () => {
        const { lng, lat } = this.map.getCenter();
        const zoom = this.map.getZoom();
        localStorage.setItem('mapState', JSON.stringify({
          center: [lng, lat],
          zoom: zoom,
        }));
      });
    }
  },
  mounted() {
    this.initializeMap();
  }
}
</script>



<template>
  <div class="map" id="map"></div>
</template>



<style lang="scss" scoped>

@import "@tomtom-international/web-sdk-maps";

#map{
  height: calc(100vh - 50px)
}

</style>