<template>
  <div>
    <data-table :config="config" :ajax="ajax"></data-table>
  </div>
</template>

<script>
import DataTable from '../../../components/DataTable.vue'

export default {
  name: 'Kardex',
  props: ['startTime','endTime','warehouse','product'],
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
        scrollX: true,
        scrollY: "450px",
        bPaginate: false,
        thead: ['Folio', 'Fecha y hora', 'Responsable', 'Atributos', 'Detalle', 'Almacén Destino', 'Almacén Origen', 'Entrada Cantidad', 'Entrada Costo', 'Entrada valor', 'Salida Cantidad', 'Salida Costo', 'Salida valor', 'Saldo Cantidad', 'Saldo Costo', 'Saldo valor', ],
        tfoot: ['Folio', 'Fecha y hora', 'Responsable', 'Atributos', 'Detalle', 'Almacén Destino', 'Almacén Origen', 'Entrada Cantidad', 'Entrada Costo', 'Entrada valor', 'Salida Cantidad', 'Salida Costo', 'Salida valor', 'Saldo Cantidad', 'Saldo Costo', 'Saldo valor', ],
        columns: [
          { data: 'ID_MOVIMIENTO'},
          { data: 'FECHA' },
          { data: 'RESPONSABLE' },
          // { data: 'ATRIBUTOS' },
          {
            render:  ( data, type, row ) => {
              let button =
              row.tieneSerie == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                onclick="(function() {
                    window.$router.push({ path: '/kardex/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/series', query: {
                      movement: ${row.ID_MOVIMIENTO},
                      product: ${row.ID_PRODUCTO},
                    }})
                })()">Series</button>` :
                ''
                return `${row.ATRIBUTOS}${button}`
            },
          },
          { data: 'DETALLE' },
          { data: 'NOMBRE_ALMACEN_DESTINO', notVisible: true  },
          { data: 'NOMBRE_ALMACEN_ORIGEN', notVisible: true  },

          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.CANTIDAD_ENTRADA),
          },
          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.COSTO_UNITARIO_ENTRADA, 4),
            notVisible: true,
          },
          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.COSTO_ENTRADA, 4),
            notVisible: true,
          },

          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.CANTIDAD_SALIDA),
          },
          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.COSTO_UNITARIO_SALIDA, 4),
            notVisible: true,
          },
          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.COSTO_SALIDA, 4),
            notVisible: true,
          },

          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.CANTIDAD_SALDO),
          },
          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.COSTO_UNITARIO_SALDO, 4),
          },
          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.COSTO_SALDO, 4),
          },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=inventario&f=kardex"
      let startTime = 'startTime='+(this.$props.startTime)
      let endTime = 'endTime='+(this.$props.endTime)
      let warehouse = 'warehouse='+(this.$props.warehouse)
      let product = 'product='+(this.$props.product)
      return `${ajax}&${startTime}&${endTime}&${warehouse}&${product}`
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
