import Vue from 'vue'
import Router from 'vue-router'
import DevolucionesDeVenta from './components/DevolucionesDeVenta.vue'
import DetalleDevolucionesDeVenta from './components/DetalleDevolucionesDeVenta.vue'
import DetalleDevolucionesDeVentaSeries from './components/DetalleDevolucionesDeVentaSeries.vue'

export default new Router({
  routes: [

    { path: '/devolucionesVenta',
      component: DevolucionesDeVenta,
      props: (route) => ({
        startTime: route.query.startTime ,
        endTime: route.query.endTime ,
        branchOffice: route.query.branchOffice ,
        customer: route.query.customer ,
      }),
    },

    { path: '/devolucionesVenta/:idVenta',
      component: DetalleDevolucionesDeVenta,
      props: (route) => ({
        sale: route.params.idVenta,
        customer: route.query.customer,
        saleAmount: route.query.saleAmount,
        returnAmount: route.query.returnAmount,
        returnDate: route.query.returnDate,
      }),
    },

    { path: '/devolucionesVenta/:productName/series',
      component: DetalleDevolucionesDeVentaSeries,
      props: (route) => ({
        productName: route.params.productName,
        sale: route.query.sale,
        product: route.query.product,
      }),
    },


    { path: '*', component: { template: `<div></div>` }
    }
  ]
})
