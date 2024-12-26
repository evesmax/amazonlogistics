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
import $ from 'jquery';
import DataTable from '../../../components/DataTable.vue'

export default {
  name: 'DetalleAntiguedadDeInventario',
  props: ['productName','product'],
  filters: {
    numberFormat: (value, decimals = 2) => {
      let intPart = Math.trunc(value); 
      let floatPart = Number((value - intPart).toFixed(decimals));
      return intPart.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + floatPart.toFixed(decimals).toString().substring(1) ;
    }
  },
  data () {
    return {
      config: {
        thead: ['#Recepción', 'Fecha de Entrega', 'Almacén destino', 'Proveedor',  'Cantidad Recibida', 'Moneda', 'Costo Unitario', 'Importe Total'],
        tfoot: ['#Recepción', 'Fecha de Entrega', 'Almacén destino', 'Proveedor',  'Cantidad Recibida', 'Moneda', 'Costo Unitario', 'Importe Total'],
        columns: [
          { data: 'NUMERO_RECEPCION' },
          { data: 'FECHA_ENTREGA' },
          { data: 'ALMACEN_DESTINO' },
          { data: 'PROVEEDOR' },
          {
            render:  ( data, type, row ) => {
              return this.$options.filters.numberFormat(row.CANTIDAD)
            }
          },
          { data: 'MONEDA' },
          {
            render:  ( data, type, row ) => {
              return this.$options.filters.numberFormat(row.COSTO_UNITARIO, 4)
            }
          },
          {
            render:  ( data, type, row ) => {
              return this.$options.filters.numberFormat(row.IMPORTE_TOTAL, 4)
            }
          },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=inventario&f=detalleAntiguedadInventario"
      let product = 'product='+(this.$props.product)
      return `${ajax}&${product}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}
</script>

<style scope>

</style>
