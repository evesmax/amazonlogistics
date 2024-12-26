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
        <h6>Venta:<br/></h6><h5>{{ sale }}</h5>
      </div>
      <div class="col-md">
        <h6>Cliente:<br/></h6><h5>{{ customer }}</h5>
      </div>
      <div class="col-md">
        <h6>Importe total de Venta:</h6><h5>{{ saleAmount | numberFormat(4) }}</h5>
      </div>
      <div class="col-md">
        <h6>Importe total de Devolución:</h6><h5>{{ returnAmount | numberFormat(4) }}</h5>
      </div>
      <div class="col-md">
        <h6>Fecha de Devolución:</h6><h5>{{ returnDate }}</h5>
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
  name: 'DetalleDevolucionesDeVenta',
  props: ['sale','customer','saleAmount','returnAmount','returnDate'],
  filters: {
    numberFormat: (value, decimals = 2) => {
      let intPart = Math.trunc(value); 
      let floatPart = Number((value - intPart).toFixed(100));
      return intPart.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + floatPart.toFixed(100).toString().substring(1,decimals+2) ;
    }
  },
  data () {
    return {
      config: {
        thead: ['Producto', 'Atributos', 'Cantidad devuelta', 'Importe Unitario', ],
        tfoot: ['Producto', 'Atributos', 'Cantidad devuelta', 'Importe Unitario', ],
        columns: [
          { data: 'NOMBRE_PRODUCTO' },
          {
            render:  ( data, type, row ) => {
              let button =
              row.tieneSerie == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                onclick="(function() {
                    window.$router.push({ path: '/devolucionesVenta/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/series', query: {
                      sale: ${row.ID_VENTA},
                      product: ${row.ID_PRODUCTO},
                    }})
                })()">Series</button>` :
                ''
                return `${row.ATRIBUTOS}${button}`
            },
          },
          {
            render:  ( data, type, row ) => {
              return this.$options.filters.numberFormat(row.CANTIDAD)
            }
          },
          {
            render:  ( data, type, row ) => {
              return this.$options.filters.numberFormat(row.IMPORTE_UNITARIO, 4 )
            }
          },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=caja&f=detalleDevolucioneVenta"
      let sale = 'sale='+(this.$props.sale)
      return `${ajax}&${sale}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}
</script>

<style scope>

</style>
