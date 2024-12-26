<template>
  <div class="card">
<!-- <pre>
{{$data}}
</pre> -->
    <div class="card-header">
      <div class="container">
        <h5 class="card-title">{{ title }}</h5>
        <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label for="">{{startTime.label}}</label>
                <!-- <date-picker v-model="startTime.time"></date-picker> -->
                <date-picker :date="startTime"></date-picker>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{endTime.label}}</label>
                <date-picker :date="endTime"></date-picker>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{warehouse.label}}</label>
                <v-select v-model="warehouse.selected" :options="warehouse.options"></v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{product.label}}</label>
                <v-select class="not-clear" v-model="product.selected" :options="product.options"></v-select>
              </div>
            </div>
        </div>
        <div class="row">
          <div class="col-md">
            <button class="btn btn-primary" @click.prevent="setRouteKardex" >Enviar</button>
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
  // import datePicker from '../../components/DateTimePicker'
  import DatePicker from '../../components/DatePicker'
  // import Datepicker from 'vue-datepicker'
  import moment from 'moment'
  import {
    getWarehouses,
    getProducts,
  } from "../../helpers/filters.js";


  export default {
    router: router,
    name: 'app',
    data () {
      return {
        title: 'Kárdex',

        startTime: {
          label: 'Inicio',
          time: moment(new Date()).subtract(1, 'years').format("YYYY-MM-DD")
        },
        endTime: {
          label: 'Fin',
          time: moment(new Date()).format("YYYY-MM-DD")
        },

        warehouse: {
          label: 'Almacén',
          options: [],
          selected: null,
        },

        product: {
          label: 'Producto',
          options: [],
          selected: null,
        },



        option: {
          type: 'day',
          week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
          month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          format: 'YYYY-MM-DD',
          placeholder: 'when?',
          inputStyle: {
            'display': 'inline-block',
            'padding': '6px',
            'line-height': '22px',
            'font-size': '16px',
            'border': '2px solid #fff',
            'box-shadow': '0 1px 3px 0 rgba(0, 0, 0, 0.2)',
            'border-radius': '2px',
            'color': '#5F5F5F'
          },
          color: {
            header: '#ccc',
            headerText: '#f00'
          },
          buttons: {
            ok: 'Ok',
            cancel: 'Cancel'
          },
          overlayOpacity: 0.5, // 0.5 as default
          dismissible: true // as true as default
        },

      }
    },
    watch: {
      'product.selected': function (newVal , oldVal) {
        if( !newVal )
          this.$set( this.product, 'selected' , oldVal )
      }
    },
    methods: {
      setRouteKardex() {
        this.$router.push({ path: '/kardex', query: {
          startTime: this.startTime.time ,
          endTime: this.endTime.time ,
          warehouse: this.warehouse.selected ? this.warehouse.selected.value : 0,
          product: this.product.selected ? this.product.selected.value : 0,
        }})
      }
    },
    created() {
      window.$router = this.$router
      getWarehouses().then(res => {
        this.$set( this.warehouse, 'options' , res )
      })
      getProducts().then(res => {
        this.$set( this.product, 'options' , res )
        this.$set( this.product, 'selected' , res[0] )
      })
    },
    components: {
      'v-select': vSelect,
      // 'date-picker': datePicker,
      'date-picker': DatePicker
    }
  }
</script>

<style lang="scss">
  @import '../../../node_modules/bootstrap/scss/bootstrap.scss';

  .not-clear .clear {
    visibility: hidden;
  }
</style>
