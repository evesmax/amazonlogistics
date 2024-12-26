<template>
  <div>
    <div class="row">
      <div class="col-md">
        <button type="button" class="btn btn-light"
          onclick="(() => {
              window.$router.go(-1)
          })()"> Regresar </button>
      </div>
      <div class="col-md">
        <h6>{{ productName }}</h6>
      </div>
      <div class="col-md"></div>
    </div>
    <data-table :config="config" :ajax="ajax"></data-table>
  </div>
</template>

<script>
import DataTable from '../../../components/DataTable.vue'

export default {
  name: 'KardexSeries',
  props: ['productName','movement', 'product'],
  data () {
    return {
      config: {
        thead: ['Serie'],
        tfoot: ['Serie'],
        columns: [
          { data: 'NOMBRE_SERIE' },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=inventario&f=kardexMovimientoSeries"
      let movement = 'movement='+(this.$props.movement)
      let product = 'product='+(this.$props.product)
      return `${ajax}&${movement}&${product}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}
</script>

<style scope>

</style>
