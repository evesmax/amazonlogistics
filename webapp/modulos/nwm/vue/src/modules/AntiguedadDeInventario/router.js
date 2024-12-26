import Vue from 'vue'
import Router from 'vue-router'
import AntiguedadInventario from './components/AntiguedadInventario.vue'
import DetalleAntiguedadDeInventario from './components/DetalleAntiguedadDeInventario' 
import {
  getActionPermission,
} from "../../helpers/filters.js";

Vue.use(Router)


export default new Router({
  routes: [

    { path: '/antiguedadInventario',
      component: AntiguedadInventario,
      props: (route) => ({
        department: route.query.department ,
        family: route.query.family ,
        line: route.query.line ,
        product: route.query.product ,
      })
    },

    { path: '/antiguedadInventario/productos/:productName',
      component: DetalleAntiguedadDeInventario,
      props: (route) => ({
        productName: route.params.productName,
        product: route.query.product ,
      })
    },

    { path: '*', component: { template: `<div></div>` }
    }
  ]
})
