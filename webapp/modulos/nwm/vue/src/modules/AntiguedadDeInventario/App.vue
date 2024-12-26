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
                <label for="">{{department.label}}</label>
                <v-select label="name" :filterable="false" v-model="department.selected" :options="department.options" @search="onSearchDepartment">
                    <template slot="no-options"></template>
                    <template slot="option" slot-scope="option">
                        <div class="d-center">
                            {{ option.label }}
                        </div>
                    </template>
                    <template slot="selected-option" scope="option">
                        <div class="selected d-center">
                            {{ option.label }}
                        </div>
                    </template>
                </v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{family.label}}</label>
                <v-select label="name" :filterable="false" v-model="family.selected"  :options="family.options" @search="onSearchFamily">
                    <template slot="no-options"></template>
                    <template slot="option" slot-scope="option">
                        <div class="d-center">
                            {{ option.label }}
                        </div>
                    </template>
                    <template slot="selected-option" scope="option">
                        <div class="selected d-center">
                            {{ option.label }}
                        </div>
                    </template>
                </v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{line.label}}</label>
                <v-select label="name" :filterable="false" v-model="line.selected" :options="line.options" @search="onSearchLine">
                    <template slot="no-options"></template>
                    <template slot="option" slot-scope="option">
                        <div class="d-center">
                            {{ option.label }}
                        </div>
                    </template>
                    <template slot="selected-option" scope="option">
                        <div class="selected d-center">
                            {{ option.label }}
                        </div>
                    </template>
                </v-select>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="">{{product.label}}</label>
                <v-select label="name" :filterable="false" v-model="product.selected" :options="product.options" @search="onSearchProduct">
                    <template slot="no-options"></template>
                    <template slot="option" slot-scope="option">
                        <div class="d-center">
                            {{ option.label }}
                        </div>
                    </template>
                    <template slot="selected-option" scope="option">
                        <div class="selected d-center">
                            {{ option.label }}
                        </div>
                    </template>
                </v-select>
              </div>
            </div>

        </div>
        <div class="row">
          <div class="col-md">
            <button class="btn btn-primary" @click.prevent="setMainRoute" >Enviar</button>
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
  import lodash from 'lodash'
  import DatePicker from '../../components/DatePicker'
  import moment from 'moment'
  import {
    getWarehouses,
    getProducts,
    getSorters,
  } from "../../helpers/filters.js";


  export default {
    router: router,
    name: 'app',
    data () {
      return {
        title: 'Antigüedad de Inventario',

        testdepartment: {
          label: 'Departamento',
          options: [],
          selected: null,
        },

        department: {
          label: 'Departamento',
          options: [],
          selected: null,
        },

        family: {
          label: 'Familia',
          options: [],
          selected: null,
        },

        line: {
          label: 'Linea',
          options: [],
          selected: null,
        },

        product: {
          label: 'Producto',
          options: [],
          selected: null,
        },


      }
    },
    methods: {
      onSearchDepartment(search, loading) {
        search = (search) == ' ' ? '' : (search)
        loading(true);
        getSorters(search, 1).then(res => {
          this.$set( this.department, 'options' , res.rows )
          loading(false)
        })
      },
      onSearchFamily(search, loading) {
        search = (search) == ' ' ? '' : (search)
        loading(true);
        let department = this.department.selected ? this.department.selected.value : 0
        getSorters(search, 2, department).then(res => {
          this.$set( this.family, 'options' , res.rows )
          loading(false)
        })
      },
      onSearchLine(search, loading) {
        search = (search) == ' ' ? '' : (search)
        loading(true);
        let department = this.department.selected ? this.department.selected.value : 0
        let family = this.family.selected ? this.family.selected.value : 0
        getSorters(search, 3, department, family).then(res => {
          this.$set( this.line, 'options' , res.rows )
          loading(false)
        })
      },
      onSearchProduct(search, loading) {
        search = (search) == ' ' ? '' : (search)
        loading(true);
        let department = this.department.selected ? this.department.selected.value : 0
        let family = this.family.selected ? this.family.selected.value : 0
        let line = this.line.selected ? this.line.selected.value : 0
        getSorters(search, 4, department, family, line).then(res => {
          this.$set( this.product, 'options' , res.rows )
          loading(false)
        })
      },
      setMainRoute() {
        this.$router.push({ path: '/antiguedadInventario', query: {
          department: this.department.selected ? this.department.selected.value : 0,
          family: this.family.selected ? this.family.selected.value : 0,
          line: this.line.selected ? this.line.selected.value : 0,
          product: this.product.selected ? this.product.selected.value : 0,
        }})
      }
    },
    created() {
      window.$router = this.$router
      getSorters('', 1).then(res => {
        this.$set( this.department, 'options' , res.rows )
      })
      getSorters('', 2).then(res => {
        this.$set( this.family, 'options' , res.rows )
      })
      getSorters('', 3).then(res => {
        this.$set( this.line, 'options' , res.rows )
      })
      getSorters('', 4).then(res => {
        this.$set( this.product, 'options' , res.rows )
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
