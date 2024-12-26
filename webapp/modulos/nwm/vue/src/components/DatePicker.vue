<template>
  <div>
    <date-picker :date="date" :option="option"></date-picker>
  </div>
</template>

<script>
import Datepicker from 'vue-datepicker'

export default {
  name: 'DataPicker',
  props: {
    date: {
      type: Object,
      default: {

      }
    },
  },
  data() {
    return {
      option: {
        type: 'day',
        week: ['L', 'M', 'I', 'J', 'V', 'S', 'D'],
        month: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        format: 'YYYY-MM-DD',
        placeholder: 'Fecha',
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
          ok: 'Aceptar',
          cancel: 'Cancelar'
        },
        overlayOpacity: 0.5, // 0.5 as default
        dismissible: true // as true as default
      },
    };
  },
  components: {
    'date-picker': Datepicker
  }

};
</script>

<style scope>

</style>
