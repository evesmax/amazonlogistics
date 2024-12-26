import Vue from 'vue'
import VueRouter from 'vue-router'
//import App from './App.vue'
//import App from './modules/inventario/App.vue'
//import App from './modules/kardex/App.vue'
//import App from './modules/AntiguedadDeInventario/App.vue'
import App from './modules/DevolucionDeVentas/App.vue'

Vue.use(VueRouter)

new Vue({
  el: '#app',
  // router: router,
  render: h => h(App)
})
