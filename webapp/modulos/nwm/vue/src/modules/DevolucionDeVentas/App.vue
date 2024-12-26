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
                <label for="">{{branchOffice.label}}</label>
                <v-select v-model="branchOffice.selected" :options="branchOffice.options"></v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{customer.label}}</label>
                <v-select class="not-clear" v-model="customer.selected" :options="customer.options"></v-select>
              </div>
            </div>
        </div>
        <div class="row">
          <div class="col-md">
            <button class="btn btn-primary" @click.prevent="setRouteDevoluciones" >Enviar</button>
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
  import DatePicker from '../../components/DatePicker'
  import moment from 'moment'
  import {
    getBranchOffices,
    getCustomers,
  } from "../../helpers/filters.js";


  export default {
    router: router,
    name: 'app',
    data () {
      return {
        title: 'Devoluciones de Venta',

        startTime: {
          label: 'Inicio',
          time: moment(new Date()).subtract(1, 'years').format("YYYY-MM-DD")
        },
        endTime: {
          label: 'Fin',
          time: moment(new Date()).format("YYYY-MM-DD")
        },
        
        branchOffice: {
          label: 'Sucursal',
          options: [],
          selected: null,
        },

        customer: {
          label: 'Cliente',
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
    methods: {
      setRouteDevoluciones() {
        this.$router.push({ path: '/devolucionesVenta', query: {
          startTime: this.startTime.time ,
          endTime: this.endTime.time ,
          branchOffice: this.branchOffice.selected ? this.branchOffice.selected.value : 0,
          customer: this.customer.selected ? this.customer.selected.value : 0,
        }})
      }
    },
    created() {
      window.$router = this.$router
      getBranchOffices().then(res => {
        this.$set( this.branchOffice, 'options' , res )
      })
      getCustomers().then(res => {
        this.$set( this.customer, 'options' , res )
      })
    },
    components: {
      'v-select': vSelect,
      'date-picker': DatePicker
    }
  }
</script>

<style lang="scss">
  @import '../../../node_modules/bootstrap/scss/bootstrap.scss';
</style>
