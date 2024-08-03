<script>
  export default {
    name: 'createTrip', 
  }
</script>



<template>
  <div>

    <h1 class="text-center py-5">Pianifica un nuovo viaggio</h1>

    <form @submit.prevent class="container">

      <div class="row">

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Nome del viaggio</span>
            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
        </div>

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Data di inizio</span>
            <input type="date" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
        </div>

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Data di fine</span>
            <input type="date" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
        </div>

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="input-group mb-3">
            <input type="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload" multiple>
            <button class="btn btn-secondary " type="button" id="inputGroupFileAddon04">Rimuovi</button>
          </div>
        </div>

        <div class="col-12 col-lg-6 offset-lg-3">
          <div class="form-floating mb-3">
            <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
            <label for="floatingTextarea">Descrizione</label>
          </div>
        </div>

        <div class="col-12 col-lg-6 offset-lg-3">
          <button type="submit" class="btn btn-secondary float-end">Crea</button>
        </div>

      </div>

    </form>

  </div>
</template>



<style lang="scss" scoped>

@use '../assets/scss/partials/vars.scss' as *;

form {
  padding: 150px 20px;
  background-color: $color-2;
}

input:focus, textarea:focus, button {
  border-color: #e1e4e8;
  outline: none !important;
  box-shadow: none !important;
}

@media screen and (min-width: 576px) {
  form{
    border-radius: 5rem;
  }
}

</style>