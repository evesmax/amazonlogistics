import Vue from 'vue'
import Router from 'vue-router'
import Kardex from './components/Kardex.vue'
import KardexLimitado from './components/KardexLimitado.vue'
import KardexSeries from './components/KardexSeries.vue'
import {
  getActionPermission,
} from "../../helpers/filters.js";

Vue.use(Router)


export default new Router({
  routes: [
    { path: '/kardex',
      component: Kardex,
      props: (route) => ({
        startTime: route.query.startTime ,
        endTime: route.query.endTime ,
        warehouse: route.query.warehouse,
        product: route.query.product,
      }),
      beforeEnter: (to, from, next) => {
        getActionPermission('23'/*Permiso para ver Costos*/).then(res => {
          if( !res )
            next({ path: to.path.replace('kardex','kardexLimitado') , query: to.query })
          else
            next()
        })
      }
    },


    { path: '/kardexLimitado',
      component: KardexLimitado,
      props: (route) => ({
        startTime: route.query.startTime ,
        endTime: route.query.endTime ,
        warehouse: route.query.warehouse,
        product: route.query.product,
      })
    },

    { path: '/kardex/:productName/series',
      component: KardexSeries,
      props: (route) => ({
        productName: route.params.productName,
        movement: route.query.movement,
        product: route.query.product,
      }),
    },


    { path: '*', component: { template: `<div></div>` }
    }
  ]
})
