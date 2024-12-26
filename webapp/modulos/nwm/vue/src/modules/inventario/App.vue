<template>
  <div class="card">
    <div class="card-header">
      <div class="container">
        <h5 class="card-title">{{ title }}</h5>
        <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label for="">{{warehouse.label}}</label>
                <v-select multiple v-model="warehouse.selected" :options="warehouse.options"></v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{provider.label}}</label>
                <v-select multiple v-model="provider.selected" :options="provider.options"></v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{product.label}}</label>
                <v-select multiple v-model="product.selected" :options="product.options"></v-select>
              </div>
            </div>
            <!-- <div class="col-md">
              <div class="form-group">
                <label for="">{{measurement.label}}</label>
                <v-select class="not-clear" v-model="measurement.selected" :options="measurement.options"></v-select> 
              </div>
            </div> -->
            <div class="col-md">
              <div class="form-group">
                <label for="">{{measurement.label}}</label>
                <br>
                <input type="radio" id="purchaseUnit" value="1" v-model="measurement.selected">
                <label for="purchaseUnit">Compra</label>
                <input type="radio" id="saleUnit" value="2" v-model="measurement.selected">
                <label for="saleUnit">Venta</label>
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
  import router from './router.js'
  import axios from 'axios'
  import vSelect from 'vue-select'
  import {
    getWarehouses,
    getProviders,
    getProducts,
    getUnitsMeasurement,
  } from "../../helpers/filters.js";


  export default {
    router: router,
    name: 'app',
    data () {
      return {
        title: 'Inventario',

        warehouse: {
          label: 'Almacén',
          options: [],
          selected: [],
        },

        provider: {
          label: 'Proveedor',
          options: [],
          selected: [],
        },

        product: {
          label: 'Producto',
          options: [],
          selected: [],
        },

        // measurement: {
        //   label: 'Unidad de Medida',
        //   options: [{label:"Compra",value:1}, {label:"Venta",value:2}],
        //   selected: {label:"Compra",value:1},
        // },
        measurement: {
          label: 'Unidad de Medida',
          selected: 1,
        },

      }
    },
    watch: {
      'measurement.selected': function (newVal , oldVal) {
        if( !newVal )
          this.$set( this.measurement, 'selected' , oldVal )
      }
    },
    methods: {
      setRouteInventario() {
        this.$router.push({ path: '/inventario/productos', query: {
          warehouse: JSON.stringify( this.warehouse.selected.map( (e) => Number(e.value) ) ) ,
          provider: JSON.stringify( this.provider.selected.map( (e) => Number(e.value) ) ) ,
          product: JSON.stringify( this.product.selected.map( (e) => Number(e.value) ) ) ,
          measurement: this.measurement.selected
        }})
      }
    },
    created() {
      window.$router = this.$router
      getWarehouses().then(res => {
        this.$set( this.warehouse, 'options' , res )
      })
      getProviders().then(res => {
        this.$set( this.provider, 'options' , res )
      })
      getProducts().then(res => {
        this.$set( this.product, 'options' , res )
      })
    },
    components: {
      'v-select': vSelect,
    }
  }
</script>

<style lang="scss">
  @import '../../../node_modules/bootstrap/scss/bootstrap.scss';

  .not-clear .clear {
    visibility: hidden;
  }
</style>
