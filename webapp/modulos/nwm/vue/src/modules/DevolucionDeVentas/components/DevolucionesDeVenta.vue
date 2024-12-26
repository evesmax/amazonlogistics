<template>
  <div>
    <data-table :config="config" :ajax="ajax"></data-table>
  </div>
</template>

<script>
import DataTable from '../../../components/DataTable.vue'

export default {
  name: 'DevolucionesDeVenta',
  props: ['startTime','endTime','branchOffice','customer'],
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
        thead: ['ID Venta', 'Fecha de Venta', 'Cliente', 'Sucursal', 'Importe total de Venta', 'Importe total de Devolución', 'Acciones'],
        tfoot: ['ID Venta', 'Fecha de Venta', 'Cliente', 'Sucursal', 'Importe total de Venta', 'Importe total de Devolución', 'Acciones'],
        columns: [
          { data: 'ID_VENTA'},
          { data: 'FECHA_VENTA'},
          { data: 'NOMBRE_CLIENTE'},
          { data: 'NOMBRE_SUCURSAL'},
          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.TOTAL_VENTA, 4),
          },

          {
            render:  ( data, type, row ) => this.$options.filters.numberFormat(row.TOTAL_DEVOLUCION, 4),
          },
          {
            render:  ( data, type, row ) => {
                return `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                onclick="(function() {
                    window.$router.push({ path: '/devolucionesVenta/${row.ID_VENTA}', query: {
                      customer: '${row.NOMBRE_CLIENTE}',
                      saleAmount: ${row.TOTAL_VENTA},
                      returnAmount: ${row.TOTAL_DEVOLUCION},
                      returnDate: '${row.FECHA_DEVOLUCION}',
                    }})
                })()">Detalles</button>`
            },
          },
          
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=caja&f=devolucionesVenta"
      let startTime = 'startTime='+(this.$props.startTime)
      let endTime = 'endTime='+(this.$props.endTime)
      let branchOffice = 'branchOffice='+(this.$props.branchOffice)
      let customer = 'customer='+(this.$props.customer)
      return `${ajax}&${startTime}&${endTime}&${branchOffice}&${customer}`
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
