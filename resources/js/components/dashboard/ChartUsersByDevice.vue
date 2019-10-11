<template>
    <div class="residents-by-device-chart">
        <el-row type="flex">
            <el-col :span="24">
                <apexchart type="donut" :options="chartOptions" :series="series" />
            </el-col>
        </el-row>
        <el-row type="flex" class="legend-container">
            <el-col :span="8" v-for="(item, index) in yData" :key="index">
                <div class="custom-legend">
                  <img :src="icons[index]" />
                  <p>{{ xData[index] }}</p>
                  <p v-if="sum > 0">{{ Math.round(item * 1000 / sum) / 10 + '%' }}</p>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'
import axios from '@/axios';

import chartMixin from 'mixins/adminDashboardChartMixin';

import iconDesktop from 'img/desktop.png';
import iconTablet from 'img/tablet.png';
import iconMobile from 'img/mobile.png';

export default {
  components: {
    'apexchart': VueApexCharts
  },
  mixins: [chartMixin()],
  data() {
    return {
      icons: [iconDesktop, iconTablet, iconMobile],
      sum: 0,
    }
  },
  props: {
    colNum: {
        type: Number
    }
  },
  computed:{
    chartOptions: function(){
      let responsive = [];
      if (this.colNum == 2) {
        responsive = [{
          breakpoint: 900,
          options: {
            chart: {
              width: '100%',
              height: 'auto'
            }
          }
        }];
      }
      else {
        responsive = [{
          breakpoint: 1200,
          options: {
            chart: {
              width: '100%',
              height: 'auto'
            }
          }
        }];
      }
      return {
        labels: this.xData,
        responsive: responsive,
        legend: {
          show: false
        },
        chart:{
          toolbar: this.toolbar,
          width: '100%',
          height: 300
        },
        colors: ['#218BFB', '#84BFFD', '#B5D8FD'],
        plotOptions: {
          pie: {
            donut: {
              size: '80%'
            }
          }
        },
        dataLabels:{
          enabled: false,
        }
      }
    }
  },
  methods: {
    fetchData() {
      let that = this;                                               
      let url = 'admin/chartLoginDevice';

      return axios.get(url)
      .then(function (response) {
        that.yData = response.data.data.data.map(val => parseFloat(val) || 0);
        that.xData = response.data.data.labels;
        that.sum = that.yData.reduce(function(a, b) { return a + b; }, 0);
      }).catch(function (error) {
        console.log(error);
      })
    }
  }
}
</script>
<style lang="scss">
  .chart-card {
    .residents-by-device-chart .apexcharts-canvas {
      margin-top: 25px;
      @media screen and (max-width: 1650px) {
        margin-top: 30px;
      }
    }
    .residents-by-device-chart {
      position: relative;

      .apexcharts-canvas {
          position: unset;
      }

      .legend-container {
        max-width: 300px;
        margin-top: 30px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 23px;
      }
      .custom-legend {
        text-align: center;

        p {
          margin: 3px;
        }
      }
    }
  }
</style>
