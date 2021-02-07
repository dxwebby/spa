
// Added due to causing server denied access
window.Laravel = {
    csrfToken: '{{csrf_token()}}'
}

import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {  
  extends: Bar,
  mixins: [reactiveProp],
  props: ['options'],
  mounted () {    
    this.renderChart(this.chartData, this.options)
  }
}