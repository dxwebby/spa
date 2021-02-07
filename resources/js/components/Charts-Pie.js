
// Added due to causing server denied access
window.Laravel = {
    csrfToken: '{{csrf_token()}}'
}

import { Pie, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {  
  extends: Pie,
  mixins: [reactiveProp],
  props: ['options'],
  mounted () {    
    this.renderChart(this.chartData, this.options)
  }
}