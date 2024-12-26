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
  name: 'ExistenciaLotes',
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
        thead: ['Almacén', 'Lote', 'Unidad de medida',  'Existencia', 'Apartados', 'Disponible'],
        tfoot: ['Almacén', 'Lote', 'Unidad de medida',  'Existencia', 'Apartados', 'Disponible'],
        footerCallback:  function( row, data, start, end, display ) {
            //TOFIX: resolve scope for remove local function
            window.numberFormat = (value, decimals = 2) => {
              let int_Part = Math.trunc(value); 
              return int_Part.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ( (value - int_Part).toFixed(decimals) ).toString().substring(1) ;
            }
            let api = this.api(), total = 0;
            let dataCol = api.column(3).cache('search')
            for (let i = 0 ; i < dataCol.length ; i++)  
              total += Number( dataCol[i].replace(',','') ) 
            $( api.column( 3 ).footer() ).html( numberFormat(total, 4) )
            total = 0;
            dataCol = api.column(4).cache('search')
            for (let i = 0 ; i < dataCol.length ; i++)  
              total += Number( dataCol[i].replace(',','') ) 
            $( api.column( 4 ).footer() ).html( numberFormat(total, 4) )
            total = 0;
            dataCol = api.column(5).cache('search')
            for (let i = 0 ; i < dataCol.length ; i++)  
              total += Number( dataCol[i].replace(',','') ) 
            $( api.column( 5 ).footer() ).html( numberFormat(total, 4) )
        },
        columns: [
          {
            render: function ( data, type, row ) {
              return `(${ row.CODIGO_ALMACEN }) ${ row.NOMBRE_ALMACEN }`
            }
          },
          { data: 'lote' },
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
      let ajax = "./index.php?c=inventario&f=lotesEnInventario"
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
