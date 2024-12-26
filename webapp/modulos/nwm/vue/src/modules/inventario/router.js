import Vue from 'vue'
import Router from 'vue-router'
import InventarioProductos from './components/InventarioProductos.vue'
import InventarioCaracteristicas from './components/InventarioCaracteristicas.vue'
import InventarioLotes from './components/InventarioLotes.vue'
import InventarioSeries from './components/InventarioSeries.vue'

import ExistenciaProductos from './components/ExistenciaProductos.vue'
import ExistenciaCaracteristicas from './components/ExistenciaCaracteristicas.vue'
import ExistenciaLotes from './components/ExistenciaLotes.vue'
import ExistenciaSeries from './components/ExistenciaSeries.vue'
import {
  getActionPermission,
} from "../../helpers/filters.js";

Vue.use(Router)


export default new Router({
  routes: [
    { path: '/inventario/productos',
      component: InventarioProductos,
      props: (route) => ({
        warehouse: route.query.warehouse,
        provider: route.query.provider,
        product: route.query.product,
        measurement: route.query.measurement,
      }),
      beforeEnter: (to, from, next) => {
        getActionPermission('22'/*Permiso para ver Costos*/).then(res => {
          if( !res )
            next({ path: to.path.replace('inventario','existencia') , query: to.query })
          else
            next()
        })
      }
    },
    { path: '/inventario/productos/:productName/caracteriticas',
      component: InventarioCaracteristicas,
      props: (route) => ({
        productName: route.params.productName,
        warehouse: route.query.warehouse,
        product: route.query.product,
        measurement: route.query.measurement,
      }),
      beforeEnter: (to, from, next) => {
        getActionPermission('22'/*Permiso para ver Costos*/).then(res => {
          if( !res )
            next({ path: to.path.replace('inventario','existencia') , query: to.query })
          else
            next()
        })
      }
    },
    { path: '/inventario/productos/:productName/lotes',
      component: InventarioLotes,
      props: (route) => ({
        productName: route.params.productName,
        warehouse: route.query.warehouse,
        product: route.query.product,
        measurement: route.query.measurement,
      }),
      beforeEnter: (to, from, next) => {
        getActionPermission('22'/*Permiso para ver Costos*/).then(res => {
          if( !res )
            next({ path: to.path.replace('inventario','existencia') , query: to.query })
          else
            next()
        })
      }
    },
    { path: '/inventario/productos/:productName/series',
      component: InventarioSeries,
      props: (route) => ({
        productName: route.params.productName,
        warehouse: route.query.warehouse,
        product: route.query.product,
        measurement: route.query.measurement,
      }),
      beforeEnter: (to, from, next) => {
        getActionPermission('22'/*Permiso para ver Costos*/).then(res => {
          if( !res )
            next({ path: to.path.replace('inventario','existencia') , query: to.query })
          else
            next()
        })
      }
    },


    { path: '/existencia/productos',
      component: ExistenciaProductos,
      props: (route) => ({
        warehouse: route.query.warehouse,
        provider: route.query.provider,
        product: route.query.product,
        measurement: route.query.measurement,
      })
    },
    { path: '/existencia/productos/:productName/caracteriticas',
      component: ExistenciaCaracteristicas,
      props: (route) => ({
        productName: route.params.productName,
        warehouse: route.query.warehouse,
        product: route.query.product,
        measurement: route.query.measurement,
      })
    },
    { path: '/existencia/productos/:productName/lotes',
      component: ExistenciaLotes,
      props: (route) => ({
        productName: route.params.productName,
        warehouse: route.query.warehouse,
        product: route.query.product,
        measurement: route.query.measurement,
      })
    },
    { path: '/existencia/productos/:productName/series',
      component: ExistenciaSeries,
      props: (route) => ({
        productName: route.params.productName,
        warehouse: route.query.warehouse,
        product: route.query.product,
        measurement: route.query.measurement,
      })
    },


    { path: '*', component: { template: `<div></div>` }
    }
  ]
})
