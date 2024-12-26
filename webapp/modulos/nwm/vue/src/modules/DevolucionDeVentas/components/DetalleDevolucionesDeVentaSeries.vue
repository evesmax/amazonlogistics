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
  props: ['productName','sale', 'product'],
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
      let ajax = "./index.php?c=caja&f=detalleDevolucioneVentaSeries"
      let sale = 'sale='+(this.$props.sale)
      let product = 'product='+(this.$props.product)
      return `${ajax}&${sale}&${product}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}
</script>

<style scope>

</style>
