<template>
    <div class="residents-by-age-chart">
        <el-row type="flex">
            <el-col :span="24">
                <apexchart type="donut" :options="chartOptions" :series="series" />
            </el-col>
        </el-row>
    </div>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'
import axios from '@/axios';

import chartMixin from 'mixins/adminDashboardChartMixin';

export default {
  components: {
    'apexchart': VueApexCharts
  },
  mixins: [chartMixin()],
  data() {
    return {
      iconMale: require('img/male.png'),
      iconFemale: require('img/female.png')
    }
  },
  computed:{
    chartOptions: function(){
      let responsive = [{
        breakpoint: 1500,
        options: {
          chart: {
            width: '100%',
            height: 'auto'
          },
          legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            width: undefined
          }
        }
      }];
      return {
        labels: this.xData,
        responsive: responsive,
        chart:{
          toolbar: this.toolbar,
          width: '100%',
          height: 320
        },
        plotOptions: {
          pie: {
            donut: {
              size: '32%'
            }
          }
        },
        colors: this.colors,
        dataLabels: {
          formatter: function(value, { seriesIndex, dataPointIndex, w }) {
            return w.config.series[seriesIndex] + "(" + value.toFixed(0) + '%)';
          }
        }
      }
    }
  },
  methods: {
    fetchData() {
      let that = this;                                               
      let url = 'residents/age-statistics';

      return axios.get(url)
      .then(function (response) {
        that.yData = response.data.data.data.map(val => parseFloat(val) || 0);
        that.xData = response.data.data.labels;
      }).catch(function (error) {
        console.log(error);
      })
    }
  }
}
</script>
<style lang="scss">
  .chart-card {
    .residents-by-age-chart .apexcharts-canvas {
      @media screen and (max-width: 1650px) {
        margin-top: 30px;
      }
    }
    .residents-by-age-chart {
      position: relative;

      .apexcharts-canvas {
        position: unset;
        margin-right: auto;
        margin-left: auto;
      }

      .apexcharts-legend {
        display: flex;
        justify-content: center !important;

        .apexcharts-legend-marker {
          border-radius: 0 !important;
          width: 45px !important;
        }
      }

      .average-age {
        margin: 20px;
        text-align: center;
        font-size: 15px;
        padding: 5px;
        border-bottom: 3px double #e3e3e3;
        border-top: 3px double #e3e3e3;
      }

      .legend-container {
        .custom-legend {
          text-align: center;
          margin-bottom: 20px;

          .title {
            display: flex;
            justify-content: center;
            align-items: center;

            img {
              max-width: 80px;
            }

            span {
              font-size: 15px;
              margin-left: 10px;
              padding-bottom: 5px;
            }
          }
        }
      }
    }
  }
</style>
