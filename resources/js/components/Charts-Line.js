
// Added due to causing server denied access
window.Laravel = {
    csrfToken: '{{csrf_token()}}'
}

import { Line, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {  
  extends: Line,
  mixins: [reactiveProp],
  props: ['options'],
  mounted () {    
    this.renderChart(this.chartData, this.options)
  }
}