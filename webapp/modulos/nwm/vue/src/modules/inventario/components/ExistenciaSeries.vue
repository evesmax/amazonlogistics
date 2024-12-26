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
  name: 'ExistenciaSeries',
  props: ['productName','warehouse','product','measurement'],
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
        thead: ['Almacén', 'Serie', 'Unidad de medida',  'Existencia', 'Apartados', 'Disponible'],
        tfoot: ['Almacén', 'Serie', 'Unidad de medida',  'Existencia', 'Apartados', 'Disponible'],
        columns: [
          {
            render: function ( data, type, row ) {
              return `(${ row.CODIGO_ALMACEN }) ${ row.NOMBRE_ALMACEN }`
            }
          },
          { data: 'serie' },
          {
            render: ( data, type, row ) => {
              return row.UNIT_OF_MEASUREMENT == "1" ? row.CODIGO_UNIDAD_COMPRA : row.CODIGO_UNIDAD_VENTA;
            }
          },
          {
            render:  ( data, type, row ) => {
              return row.UNIT_OF_MEASUREMENT == "1" ? this.$options.filters.numberFormat(row.EXISTENCIA_COMPRA) : this.$options.filters.numberFormat(row.EXISTENCIA_VENTA) ;
            }
          },
          {
            render:  ( data, type, row ) => {
              return row.UNIT_OF_MEASUREMENT == "1" ? this.$options.filters.numberFormat(row.APARTADOS_COMPRA) : this.$options.filters.numberFormat(row.APARTADOS_VENTA) ;
            }
          },
          {
            render:  ( data, type, row ) => {
              return row.UNIT_OF_MEASUREMENT == "1" ? this.$options.filters.numberFormat(row.DISPONIBLE_COMPRA) : this.$options.filters.numberFormat(row.DISPONIBLE_VENTA) ;
            }
          },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=inventario&f=seriesEnInventario"
      let warehouse = 'warehouse='+(this.$props.warehouse)
      let product = 'product='+(this.$props.product)
      let measurement = 'measurement='+(this.$props.measurement)
      return `${ajax}&${warehouse}&${product}&${measurement}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}
</script>

<style scope>

</style>
