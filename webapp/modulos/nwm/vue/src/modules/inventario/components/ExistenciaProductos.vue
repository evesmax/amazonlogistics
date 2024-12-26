<template>
  <div>
    <data-table :config="config" :ajax="ajax"></data-table>
  </div>
</template>

<script>
import $ from 'jquery';
import DataTable from '../../../components/DataTable.vue'

export default {
  name: 'ExistenciaProductos',
  props: ['warehouse','provider','product','measurement'],
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
        thead: ['Almacén', 'Código', 'Nombre',  'Unidad de medida', 'Existencia', 'Apartados', 'Disponible'],
        tfoot: ['Almacén', 'Código', 'Nombre',  'Unidad de medida', 'Existencia', 'Apartados', 'Disponible'],
        columns: [
          {
            render:  ( data, type, row ) => {
              let button =
              row.TIENE_CARACTERISTICA == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
              onclick="(function() {
                  window.$router.push({ path: '/existencia/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/caracteriticas', query: {
                    warehouse: JSON.stringify( ${this.$props.warehouse} ),
                    product: ${row.ID_PRODUCTO},
                    measurement: ${row.UNIT_OF_MEASUREMENT},
                  }})
              })()">Características</button>` :
              row.TIENE_LOTE == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
              onclick="(function() {
                  window.$router.push({ path: '/existencia/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/lotes', query: {
                    warehouse: JSON.stringify( ${this.$props.warehouse} ),
                    product: ${row.ID_PRODUCTO},
                    measurement: ${row.UNIT_OF_MEASUREMENT},
                  }})
              })()">Lotes</button>` :
              row.TIENE_SERIE == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
              onclick="(function() {
                  window.$router.push({ path: '/existencia/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/series', query: {
                    warehouse: JSON.stringify(  ${this.$props.warehouse} ),
                    product: ${row.ID_PRODUCTO},
                    measurement: ${row.UNIT_OF_MEASUREMENT},
                  }})
              })()">Series</button>` :

              ''
              return ` (${ row.CODIGO_ALMACEN }) ${ row.NOMBRE_ALMACEN } <br/> ${button}`
            }
          },
          { data: 'CODIGO_PRODUCTO' },
          { data: 'NOMBRE_PRODUCTO' },
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
      let ajax = "./index.php?c=inventario&f=productosEnInventario"
      let warehouse = 'warehouse='+(this.$props.warehouse)
      let provider = 'provider='+(this.$props.provider)
      let product = 'product='+(this.$props.product)
      let measurement = 'measurement='+(this.$props.measurement)
      return `${ajax}&${warehouse}&${provider}&${product}&${measurement}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}
</script>

<style scope>
  .btn {
    border-color: gray;
  }
</style>
