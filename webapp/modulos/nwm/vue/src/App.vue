<template>
  <div class="card">
    <div class="card-header">
      <div class="container">
        <!-- <pre>
{{ $data }}
        </pre> -->
        <h5 class="card-title">{{ title }}</h5>
        <div class="row">
            <!-- <div class="col-md">
              <div class="form-group">
                <label for="">Fecha</label>
                <date-picker :date="startTime" :option="optionDatepicker" ></date-picker>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">Fecha</label>
                <date-picker :date="endTime" :option="optionDatepicker" ></date-picker>
              </div>
            </div> -->
            <div class="col-md">
              <div class="form-group">
                <label for="">{{warehouse.label}}</label>
                <v-select v-model="warehouse.selected" :options="warehouse.options"></v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{provider.label}}</label>
                <v-select v-model="provider.selected" :options="provider.options"></v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{product.label}}</label>
                <v-select v-model="product.selected" :options="product.options"></v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{unitMeasurement.label}}</label>
                <v-select v-model="unitMeasurement.selected" :options="unitMeasurement.options"></v-select>
              </div>
            </div>
        </div>
        <div class="row">
          <div class="col-md">
            <button class="btn btn-primary" @click.prevent="setRouteInventario" >Enviar</button>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import VueRouter from 'vue-router'
import Datepicker from 'vue-datepicker'
import vSelect from 'vue-select'
import Vuetable from 'vuetable-2'
import DataTable from './components/DataTable.vue'


const InventarioProductos = {
  template: `<div>
              <data-table :config="config" :ajax="ajax"></data-table>
            </div>`,
  props: ['warehouse','provider','product','unitMeasurement'],
  data () {
    return {
      config: {
        thead: ['Almacén', 'Código', 'Caracteristicas', 'Unidad de medida', 'Costo Unitario', 'Método de costeo', 'Existencia', 'Apartados', 'Disponible'],
        tfoot: ['Almacén', 'Código', 'Caracteristicas', 'Unidad de medida', 'Costo Unitario', 'Método de costeo', 'Existencia', 'Apartados', 'Disponible'],
        columns: [
          {
            render:  ( data, type, row ) => {
              let button =
              row.TIENE_CARACTERISTICA == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
              onclick="(() => {
                  window.$router.push({ path: '/inventario/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/caracteriticas', query: {
                    warehouse: ${this.$props.warehouse},
                    provider: ${this.$props.provider},
                    unitMeasurement: ${this.$props.unitMeasurement},
                    product: ${row.ID_PRODUCTO},
                  }})
              })()">C</button>` :
                row.TIENE_SERIE == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                onclick="(() => {
                    window.$router.push({ path: '/inventario/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/series', query: {
                      warehouse: ${this.$props.warehouse},
                      provider: ${this.$props.provider},
                      unitMeasurement: ${this.$props.unitMeasurement},
                      product: ${row.ID_PRODUCTO},
                    }})
                })()">S</button>` :
                row.TIENE_LOTE == "1" ? `<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                onclick="(() => {
                    window.$router.push({ path: '/inventario/productos/(${row.CODIGO_PRODUCTO}) ${row.NOMBRE_PRODUCTO}/lotes', query: {
                      warehouse: ${this.$props.warehouse},
                      provider: ${this.$props.provider},
                      unitMeasurement: ${this.$props.unitMeasurement},
                      product: ${row.ID_PRODUCTO},
                    }})
                })()">L</button>` :
                ''
              return ` (${ row.CODIGO_ALMACEN }) ${ row.NOMBRE_ALMACEN } <br/> ${button}`
            }
          },
          { data: 'CODIGO_PRODUCTO' },
          { data: 'NOMBRE_PRODUCTO' },
          { data: 'NOMBRE_UNIDAD_COMPRA' },
          { data: 'COSTO_UNITARIO' },
          { data: 'NOMBRE_COSTEO' },
          { data: 'EXISTENCIA' },
          { data: 'APARTADOS' },
          { data: 'DISPONIBLE' },
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
      let unitMeasurement = 'unitMeasurement='+(this.$props.unitMeasurement)
      return `${ajax}&${warehouse}&${provider}&${product}&${unitMeasurement}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}

const InventarioCaracteristicas = {
  template: `<div>
              <div class="row">
                <div class="col-md">
                  <button type="button" class="btn btn-light"
                    onclick="(() => {
                        window.$router.go(-1)
                    })()"> Regresar </button>
                </div>
                <div class="col-md">
                  <h6>{{ productName }}<h6>
                </div>
                <div class="col-md"></div>
              </div>
              <data-table :config="config" :ajax="ajax"></data-table>
            </div>`,
  props: ['productName','warehouse','provider','product','unitMeasurement'],
  data () {
    return {
      config: {
        thead: ['Almacén', 'Caracteristicas', 'Unidad de medida', 'Costo Unitario', 'Método de costeo', 'Existencia', 'Apartados', 'Disponible'],
        tfoot: ['Almacén', 'Caracteristicas', 'Unidad de medida', 'Costo Unitario', 'Método de costeo', 'Existencia', 'Apartados', 'Disponible'],
        columns: [
          {
            render: function ( data, type, row ) {
              return `(${ row.CODIGO_ALMACEN }) ${ row.NOMBRE_ALMACEN }`
            }
          },
          { data: 'caracteristicas' },
          { data: 'NOMBRE_UNIDAD_COMPRA' },
          { data: 'COSTO_UNITARIO' },
          { data: 'NOMBRE_COSTEO' },
          { data: 'EXISTENCIA' },
          { data: 'APARTADOS' },
          { data: 'DISPONIBLE' },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=inventario&f=caracteristicasEnInventario"
      let warehouse = 'warehouse='+(this.$props.warehouse)
      let provider = 'provider='+(this.$props.provider)
      let product = 'product='+(this.$props.product)
      let unitMeasurement = 'unitMeasurement='+(this.$props.unitMeasurement)
      return `${ajax}&${warehouse}&${provider}&${product}&${unitMeasurement}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}

const InventarioLotes = {
  template: `<div>
              <div class="row">
                <div class="col-md">
                  <button type="button" class="btn btn-light"
                    onclick="(() => {
                        window.$router.go(-1)
                    })()"> Regresar </button>
                </div>
                <div class="col-md">
                  <h6>{{ productName }}<h6>
                </div>
                <div class="col-md"></div>
              </div>
              <data-table :config="config" :ajax="ajax"></data-table>
            </div>`,
  props: ['productName','warehouse','provider','product','unitMeasurement'],
  data () {
    return {
      config: {
        thead: ['Almacén', 'Lote', 'Unidad de medida', 'Fabricación', 'Caducidad', 'Costo Unitario', 'Método de costeo', 'Existencia', 'Apartados', 'Disponible'],
        tfoot: ['Almacén', 'Lote', 'Unidad de medida', 'Fabricación', 'Caducidad', 'Costo Unitario', 'Método de costeo', 'Existencia', 'Apartados', 'Disponible'],
        columns: [
          {
            render: function ( data, type, row ) {
              return `(${ row.CODIGO_ALMACEN }) ${ row.NOMBRE_ALMACEN }`
            }
          },
          { data: 'lote' },
          { data: 'NOMBRE_UNIDAD_COMPRA' },
          { data: 'fabricacion' },
          { data: 'caducidad' },
          { data: 'COSTO_UNITARIO' },
          { data: 'NOMBRE_COSTEO' },
          { data: 'EXISTENCIA' },
          { data: 'APARTADOS' },
          { data: 'DISPONIBLE' },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=inventario&f=lotesEnInventario"
      let warehouse = 'warehouse='+(this.$props.warehouse)
      let provider = 'provider='+(this.$props.provider)
      let product = 'product='+(this.$props.product)
      let unitMeasurement = 'unitMeasurement='+(this.$props.unitMeasurement)
      return `${ajax}&${warehouse}&${provider}&${product}&${unitMeasurement}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}

const InventarioSeries = {
  template: `<div>
              <div class="row">
                <div class="col-md">
                  <button type="button" class="btn btn-light"
                    onclick="(() => {
                        window.$router.go(-1)
                    })()"> Regresar </button>
                </div>
                <div class="col-md">
                  <h6>{{ productName }}<h6>
                </div>
                <div class="col-md"></div>
              </div>
              <data-table :config="config" :ajax="ajax"></data-table>
            </div>`,
  props: ['productName','warehouse','provider','product','unitMeasurement'],
  data () {
    return {
      config: {
        thead: ['Almacén', 'Serie', 'Unidad de medida', 'Costo Unitario', 'Método de costeo', 'Existencia', 'Apartados', 'Disponible'],
        tfoot: ['Almacén', 'Serie', 'Unidad de medida', 'Costo Unitario', 'Método de costeo', 'Existencia', 'Apartados', 'Disponible'],
        columns: [
          {
            render: function ( data, type, row ) {
              return `(${ row.CODIGO_ALMACEN }) ${ row.NOMBRE_ALMACEN }`
            }
          },
          { data: 'serie' },
          { data: 'NOMBRE_UNIDAD_COMPRA' },
          { data: 'COSTO_UNITARIO' },
          { data: 'NOMBRE_COSTEO' },
          { data: 'EXISTENCIA' },
          { data: 'APARTADOS' },
          { data: 'DISPONIBLE' },
        ]
      }
    }
  },
  computed: {
    ajax() {
      let ajax = "./index.php?c=inventario&f=seriesEnInventario"
      let warehouse = 'warehouse='+(this.$props.warehouse)
      let provider = 'provider='+(this.$props.provider)
      let product = 'product='+(this.$props.product)
      let unitMeasurement = 'unitMeasurement='+(this.$props.unitMeasurement)
      return `${ajax}&${warehouse}&${provider}&${product}&${unitMeasurement}`
    }
  },
  components: {
    'data-table': DataTable,
  },
}


const routes = [
  { path: '/inventario/productos', component: InventarioProductos, props: (route) => ({
    warehouse: route.query.warehouse,
    provider: route.query.provider,
    product: route.query.product,
    unitMeasurement: route.query.unitMeasurement,
  }) },
  { path: '/inventario/productos/:productName/caracteriticas', component: InventarioCaracteristicas, props: (route) => ({
    productName: route.params.productName,
    warehouse: route.query.warehouse,
    provider: route.query.provider,
    product: route.query.product,
    unitMeasurement: route.query.unitMeasurement,
  }) },
  { path: '/inventario/productos/:productName/lotes', component: InventarioLotes, props: (route) => ({
    productName: route.params.productName,
    warehouse: route.query.warehouse,
    provider: route.query.provider,
    product: route.query.product,
    unitMeasurement: route.query.unitMeasurement,
  }) },
  { path: '/inventario/productos/:productName/series', component: InventarioSeries, props: (route) => ({
    productName: route.params.productName,
    warehouse: route.query.warehouse,
    provider: route.query.provider,
    product: route.query.product,
    unitMeasurement: route.query.unitMeasurement,
  }) },
  { path: '*', component: { template: `<div></div>` }
  }
]
const router = new VueRouter({
  routes: routes,
})

export default {
  router: router,
  name: 'app',
  data () {
    return {
      title: 'Inventario',
      // startTime: {
      //   label: 'Inicio',
      //   time: new Date()
      // },
      // endTime: {
      //   label: 'Fin',
      //   time: new Date()
      // },

      warehouse: {
        label: 'Almacén',
        options: [{label: 'Almacén', value: '1'}],
        selected: null,
      },

      provider: {
        label: 'Proveedor',
        options: [{label: 'Proveedor', value: '1'}],
        selected: null,
      },

      product: {
        label: 'Producto',
        options: [{label: 'Producto', value: '1'}],
        selected: null,
      },

      unitMeasurement: {
        label: 'Unidad de medida',
        options: [{label: 'UM', value: '1'}],
        selected: null,
      },


      // optionDatepicker: {
      //   type: 'day',
      //   week: ['L', 'M', 'I', 'J', 'V', 'S', 'D'],
      //   month: ['Enero', 'Febrero', 'Merzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      //   format: 'YYYY-MM-DD',
      //   placeholder: 'Fecha',
      //   inputStyle: {
      //     'display': 'inline-block',
      //     'padding': '6px',
      //     'line-height': '22px',
      //     'font-size': '16px',
      //     'border': '2px solid #fff',
      //     'box-shadow': '0 1px 3px 0 rgba(0, 0, 0, 0.2)',
      //     'border-radius': '2px',
      //     'color': '#5F5F5F'
      //   },
      //   color: {
      //     header: '#ccc',
      //     headerText: '#f00'
      //   },
      //   buttons: {
      //     ok: 'Aceptar',
      //     cancel: 'Cancelar'
      //   },
      //   overlayOpacity: 0.5, // 0.5 as default
      //   dismissible: true, // as true as default
      //   inputClass: 'form-control'
      // },

    }
  },
  methods: {
    getWarehouses() {
      axios.get('./index.php', {
        params: {
          c: 'filters',
          f: 'almacenes'
        }
      })
        .then(res => {
          this.$set( this.warehouse, 'options' , res.data )
        })
        .catch(error => {
          this.$set( this.warehouse, 'options' , [] )
        })
    },
    getProviders() {
      axios.get('./index.php', {
        params: {
          c: 'filters',
          f: 'proveedores'
        }
      })
        .then(res => {
          this.$set( this.provider, 'options' , res.data )
        })
        .catch(error => {
          this.$set( this.provider, 'options' , [] )
        })
    },
    getProducts() {
      axios.get('./index.php', {
        params: {
          c: 'filters',
          f: 'productos'
        }
      })
        .then(res => {
          this.$set( this.product, 'options' , res.data )
        })
        .catch(error => {
          this.$set( this.product, 'options' , [] )
        })
    },
    getUnitsMeasurement() {
      axios.get('./index.php', {
        params: {
          c: 'filters',
          f: 'unidadesDeMedida'
        }
      })
        .then(res => {
          this.$set( this.unitMeasurement, 'options' , res.data )
        })
        .catch(error => {
          this.$set( this.unitMeasurement, 'options' , [] )
        })
    },
    setRouteInventario() {
      this.$router.push({ path: '/inventario/productos', query: {
        warehouse: this.warehouse.selected ? this.warehouse.selected.value : 0,
        provider: this.provider.selected ? this.provider.selected.value : 0,
        product: this.product.selected ? this.product.selected.value : 0,
        unitMeasurement: this.unitMeasurement.selected ? this.unitMeasurement.selected.value : 0,
      }})
    }
  },
  beforeCreate() {
    window.$router = this.$router
  },
  created() {
    window.$router = this.$router
    this.getWarehouses()
    this.getProviders()
    this.getProducts()
    this.getUnitsMeasurement()
  },
  components: {
    'date-picker': Datepicker,
    'v-select': vSelect,
    'vuetable': Vuetable,
    'data-table': DataTable,
  }
}
</script>

<style lang="scss">
  @import '../node_modules/bootstrap/scss/bootstrap.scss';
  col-md {
    btn {
      width: 100%;
      height: 100%;
    }
  }
</style>
