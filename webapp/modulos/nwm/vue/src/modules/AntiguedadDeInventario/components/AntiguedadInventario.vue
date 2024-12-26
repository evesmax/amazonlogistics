<template>
  <div>
    <data-table :config="config" :ajax="ajax">
        <!-- <thead>
            <tr>
                <th rowspan="2">Departamento</th>
                <th rowspan="2">Familia</th>
                <th rowspan="2">Linea</th>
                <th rowspan="2">Código</th>
                <th rowspan="2">Nombre de producto</th>
                <th rowspan="2">Unidad de Compra</th>
                <th colspan="3">+60 días</th>
                <th colspan="3">+90 días</th>
                <th colspan="3">+120 días</th>
                <th colspan="3">+150 días</th>
            </tr>
            <tr>
                <th >Cantidad</th>
                <th >Costo Total</th>
                <th >Detalle</th>

                <th >Cantidad</th>
                <th >Costo Total</th>
                <th >Detalle</th>

                <th >Cantidad</th>
                <th >Costo Total</th>
                <th >Detalle</th>

                <th >Cantidad</th>
                <th >Costo Total</th>
                <th >Detalle</th>
            </tr>
        </thead> -->
    </data-table>
  </div>
</template>

<script>
import $ from 'jquery';
import DataTable from '../../../components/DataTable.vue'

export default {
  name: 'AntiguedadInventario',
  props: ['department','family','line','product'],
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
        thead: `<tr>
                    <th rowspan="2">Departamento</th>
                    <th rowspan="2">Código</th>
                    <th rowspan="2">Nombre de producto</th>
                    <th rowspan="2">Unidad de Compra</th>
                    <th colspan="3" style="text-align: center; border-right-style: solid; border-right-width: 1px;">+60 días</th>
                    <th colspan="3" style="text-align: center; border-right-style: solid; border-right-width: 1px;">+90 días</th>
                    <th colspan="3" style="text-align: center; border-right-style: solid; border-right-width: 1px;">+120 días</th>
                    <th colspan="3" style="text-align: center; border-right-style: solid; border-right-width: 1px;">+150 días</th>
                </tr>
                <tr>
                    <th >Cantidad</th>
                    <th >Costo Total</th>
                    <th >Detalle</th>

                    <th >Cantidad</th>
                    <th >Costo Total</th>
                    <th >Detalle</th>

                    <th >Cantidad</th>
                    <th >Costo Total</th>
                    <th >Detalle</th>

                    <th >Cantidad</th>
                    <th >Costo Total</th>
                    <th >Detalle</th>
                </tr>`,
        tbody: `<tr>
                    <th rowspan="2">Departamento</th>
                    <th rowspan="2">Código</th>
                    <th rowspan="2">Nombre de producto</th>
                    <th rowspan="2">Unidad de Compra</th>
                    <th colspan="3">+60 días</th>
                    <th colspan="3">+90 días</th>
                    <th colspan="3">+120 días</th>
                    <th colspan="3">+150 días</th>
                </tr>
                <tr>
                    <th >Cantidad</th>
                    <th >Costo Total</th>
                    <th >Detalle</th>

                    <th >Cantidad</th>
                    <th >Costo Total</th>
                    <th >Detalle</th>

                    <th >Cantidad</th>
                    <th >Costo Total</th>
                    <th >Detalle</th>

                    <th >Cantidad</th>
                    <th >Costo Total</th>
                    <th >Detalle</th>
                </tr>`,

        // thead: ['Departamento', 'Código', 'Nombre de producto', 'Unidad de Compra', 'Cantidad', 'Costo Total', 'Detalle', 'Cantidad', 'Costo Total', 'Detalle', 'Cantidad', 'Costo Total', 'Detalle', 'Cantidad', 'Costo Total', 'Detalle'],
        // tfoot: ['Departamento', 'Código', 'Nombre de producto', 'Unidad de Compra', 'Cantidad', 'Costo Total', 'Detalle', 'Cantidad', 'Costo Total', 'Detalle', 'Cantidad', 'Costo Total', 'Detalle', 'Cantidad', 'Costo Total', 'Detalle'],
        columns: [
        //   {
        //     render:  ( data, type, row ) => {
        //       let button =
        //       row.TIENE_CARACTERISTICA == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        //       onclick="(function() {
        //           window.$router.push({ path: '/existencia/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/caracteriticas', query: {
        //             warehouse: JSON.stringify( ${this.$props.warehouse} ),
        //             product: ${row.ID_PRODUCTO},
        //             measurement: ${row.UNIT_OF_MEASUREMENT},
        //           }})
        //       })()">Características</button>` :
        //       row.TIENE_LOTE == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        //       onclick="(function() {
        //           window.$router.push({ path: '/existencia/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/lotes', query: {
        //             warehouse: JSON.stringify( ${this.$props.warehouse} ),
        //             product: ${row.ID_PRODUCTO},
        //             measurement: ${row.UNIT_OF_MEASUREMENT},
        //           }})
        //       })()">Lotes</button>` :
        //       row.TIENE_SERIE == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        //       onclick="(function() {
        //           window.$router.push({ path: '/existencia/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/series', query: {
        //             warehouse: JSON.stringify(  ${this.$props.warehouse} ),
        //             product: ${row.ID_PRODUCTO},
        //             measurement: ${row.UNIT_OF_MEASUREMENT},
        //           }})
        //       })()">Series</button>` :

        //       ''
        //       return ` (${ row.CODIGO_ALMACEN }) ${ row.NOMBRE_ALMACEN } <br/> ${button}`
        //     }
        //   },
        //   { data: 'CODIGO_PRODUCTO' },
        //   { data: 'NOMBRE_PRODUCTO' },
        //   {
        //     render: ( data, type, row ) => {
        //       return row.UNIT_OF_MEASUREMENT == "1" ? row.CODIGO_UNIDAD_COMPRA : row.CODIGO_UNIDAD_VENTA;
        //     }
        //   },
        //   {
        //     render:  ( data, type, row ) => {
        //       return row.UNIT_OF_MEASUREMENT == "1" ? this.$options.filters.numberFormat(row.EXISTENCIA_COMPRA) : this.$options.filters.numberFormat(row.EXISTENCIA_VENTA) ;
        //     }
        //   },
        //   {
        //     render:  ( data, type, row ) => {
        //       return row.UNIT_OF_MEASUREMENT == "1" ? this.$options.filters.numberFormat(row.APARTADOS_COMPRA) : this.$options.filters.numberFormat(row.APARTADOS_VENTA) ;
        //     }
        //   },
        //   {
        //     render:  ( data, type, row ) => {
        //       return row.UNIT_OF_MEASUREMENT == "1" ? this.$options.filters.numberFormat(row.DISPONIBLE_COMPRA) : this.$options.filters.numberFormat(row.DISPONIBLE_VENTA) ;
        //     }
        //   },
            {
                render:  ( data, type, row ) => {
                return row.DERTAMENTO ;
                },
                notVisible: true,
            },
            {
                render:  ( data, type, row ) => {
                return row.CODIGO ;
                },
                notVisible: true,
            },
            {
                render:  ( data, type, row ) => {
                return row.NOMBRE ;
                }
            },
            {
                render:  ( data, type, row ) => {
                return row.UNIDAD_COMPRA ;
                },
                notVisible: true,
            },
            {
                render:  ( data, type, row ) => {
                return (row.RANGO == '60' ? this.$options.filters.numberFormat(row.CANTIDAD) : "") ;
                }
            },
            {
                render:  ( data, type, row ) => {
                return (row.RANGO == '60' ? this.$options.filters.numberFormat(row.FECHA,4): "") ;
                }
            },
            {
                render:  ( data, type, row ) => {
                    return row.RANGO == '60' ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    onclick="(function() {
                        window.$router.push({ path: '/antiguedadInventario/productos/(${row.CODIGO}) ${row.NOMBRE}/', query: {
                            product: ${row.ID},
                        }})
                    })()">Detalles</button>` : ""
                }
            },
            {
                render:  ( data, type, row ) => {
                return (row.RANGO == '90' ? this.$options.filters.numberFormat(row.CANTIDAD) : "") ;
                }
            },
            {
                render:  ( data, type, row ) => {
                return (row.RANGO == '90' ? this.$options.filters.numberFormat(row.FECHA,4): "") ;
                }
            },
            {
                render:  ( data, type, row ) => {
                    return row.RANGO == '90' ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    onclick="(function() {
                        window.$router.push({ path: '/antiguedadInventario/productos/(${row.CODIGO}) ${row.NOMBRE}/', query: {
                            product: ${row.ID},
                        }})
                    })()">Detalles</button>` : ""
                }
            },
            {
                render:  ( data, type, row ) => {
                return (row.RANGO == '120' ? this.$options.filters.numberFormat(row.CANTIDAD) : "") ;
                }
            },
            {
                render:  ( data, type, row ) => {
                return (row.RANGO == '120' ? this.$options.filters.numberFormat(row.FECHA,4): "") ;
                }
            },
            {
                render:  ( data, type, row ) => {
                    return row.RANGO == '120' ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    onclick="(function() {
                        window.$router.push({ path: '/antiguedadInventario/productos/(${row.CODIGO}) ${row.NOMBRE}/', query: {
                            product: ${row.ID},
                        }})
                    })()">Detalles</button>` : ""
                }
            },
            {
                render:  ( data, type, row ) => {
                return (row.RANGO == '+' ? this.$options.filters.numberFormat(row.CANTIDAD) : "") ;
                },
                notVisible: true,
            },
            {
                render:  ( data, type, row ) => {
                return (row.RANGO == '+' ? this.$options.filters.numberFormat(row.FECHA,4): "") ;
                },
                notVisible: true,
            },
            {
                render:  ( data, type, row ) => {
                    return row.RANGO == '+' ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    onclick="(function() {
                        window.$router.push({ path: '/antiguedadInventario/productos/(${row.CODIGO}) ${row.NOMBRE}/', query: {
                            product: ${row.ID},
                        }})
                    })()">Detalles</button>` : ""
                },
                notVisible: true,
            },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=inventario&f=antiguedadInventario"
      let department = 'department='+(this.$props.department)
      let family = 'family='+(this.$props.family)
      let line = 'line='+(this.$props.line)
      let product = 'product='+(this.$props.product)
      let measurement = 'measurement='+(this.$props.measurement)
      return `${ajax}&${department}&${family}&${line}&${product}`
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