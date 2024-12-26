<template>
  <div>
    <table class="display" style="width:100%">
        <thead></thead>
        <tfoot></tfoot>
    </table>
  </div>
</template>

<script>
import $ from 'jquery';
// import 'dataTables.net';
import 'datatables.net-buttons'
import 'datatables.net-buttons/js/buttons.colVis'
import 'datatables.net-buttons/js/buttons.html5'
import 'datatables.net-buttons/js/buttons.flash'
import 'datatables.net-buttons/js/buttons.print'

export default {
  name: 'DataTable',
  data() {
    return {
      datatable: null
    };
  },
  props: {
    ajax: {
      type: String,
      required: true
    },
    config: {
      type: Object,
      default: {

      }
    },
  },
  watch: {
  	ajax(newVal, oldVal) { // watch it
      this.datatable.ajax.url( newVal ).load()
    }
  },
  mounted() {
    if (this.config.thead) {
      if ( Array.isArray( this.config.thead ) ) {
        $(this.$el).find('table thead').append(`<tr>
          ${ this.config.thead.map((th) => `<th>${th}</th>` ) }
          </tr>`)
      } else {
        $(this.$el).find('table thead').append(this.config.thead)
      }
    }
    
    if (this.config.tfoot) {
      if ( Array.isArray( this.config.tfoot ) ) {
        $(this.$el).find('table tfoot').empty().append(`<tr>
          ${ this.config.tfoot.map((th) => `<th>${th}</th>` ) }
          </tr>`)
      } else {
        $(this.$el).find('table tfoot').append(this.config.tfoot)
      }
    }
    

    this.config.ajax = this.ajax
    this.config.dom = 'Bfrtip'
    this.config.buttons = [
        {
          extend: 'colvis',
          text: 'Columnas',
          footer: true
        },
        {
          extend: 'copy',
          text: 'Copiar',
          footer: true
        },
        {
          extend: 'print',
          text: 'Imprimir',
          footer: true
        },
        {
          extend: 'csv',
          text: 'Exportar',
          footer: true
        },

        'excel',
        'pdf',

    ]
    this.config.language = {
      "sProcessing":     "Procesando...",
      "sLengthMenu":     "Mostrar _MENU_ registros",
      "sZeroRecords":    "No se encontraron resultados",
      "sEmptyTable":     "Ningún dato disponible en esta tabla",
      "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
      "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
      "sInfoPostFix":    "",
      "sSearch":         "Buscar:",
      "sUrl":            "",
      "sInfoThousands":  ",",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
          "sFirst":    "Primero",
          "sLast":     "Último",
          "sNext":     "Siguiente",
          "sPrevious": "Anterior"
      },
      "oAria": {
          "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
      }
    }

    this.datatable = $(this.$el)
      .find('table')
      .DataTable( this.config )

    this.config.columns
      .forEach( (val, i) => {
        if( val.notVisible ) {
          let column = this.datatable.column( i )
          column.visible( false )
        }
      });


  },
  beforeDestroy() {
    this.datatable.destroy()
  }
};
</script>

<style scope>
  @import '../../node_modules/datatables.net-dt/css/jquery.dataTables.css';
  @import '../../node_modules/datatables.net-buttons-dt/css/buttons.dataTables.css';
</style>
