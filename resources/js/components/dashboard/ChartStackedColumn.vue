<template>
    <div v-if="startDate" class="stackchart">
        <div class="chart-filter in-toolbar">
          <el-radio-group v-model="period" class="stack-radios">                
              <el-radio-button label="day">{{$t('general.timestamps.days')}}</el-radio-button>
              <el-radio-button label="week">{{$t('general.timestamps.weeks')}}</el-radio-button>
              <el-radio-button label="month">{{$t('general.timestamps.months')}}</el-radio-button>
              <el-radio-button label="year">{{$t('general.timestamps.years')}}</el-radio-button>
          </el-radio-group>
          <custom-date-range-picker :rangeType="period" :initialRange="dateRange"
            :pickHandler="pickHandler" :startDate="startDate">
          </custom-date-range-picker>
        </div>    
        <el-row type="flex">
            <el-col :span="24">
                <apexchart ref="stackColumnChart" width="100%" height="310" type="bar" :options="chartOptions" :series="series"></apexchart>
            </el-col>
        </el-row>        
    </div>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'
import {format, subDays, isBefore, isAfter, parse} from 'date-fns'
import axios from '@/axios';

import CustomDateRangePicker from 'components/CustomDateRangePicker';
import columChartMixin from 'mixins/adminDashboardColumnChartMixin';

export default {  
  components: {
    'apexchart': VueApexCharts,
    CustomDateRangePicker
  },
  mixins: [columChartMixin()],  
  computed: {
    chartOptions: function(){
      const options = this.columnChartOptions;
      options.chart.stacked = true;
      options.legend = {
        position: 'bottom',              
        horizontalAlign: 'center'
      };
      return options;
    }        
  },
  methods: {
    fetchData(){
      let that = this;                                               
      let url = '';
      let langPrefix = '';
      if(this.type === 'request_by_creation_date'){
        url = 'admin/chartRequestByCreationDate';
      }
      else if (this.type === 'pinboard_by_creation_date') {
        url = 'admin/chartByCreationDate?table=pinboard';
        langPrefix = 'models.pinboard.status.';
      }
      else if (this.type === 'listings_by_creation_date') {
        url = 'admin/chartByCreationDate?table=listings';
        langPrefix = 'models.listing.status.';
      }
      else if (this.type === 'residents_by_creation_date') {
        url = 'admin/chartByCreationDate?table=residents';
        langPrefix = 'models.resident.status.';
      }
      let params = {
        period: that.period
      };
      if (this.dateRange != null) {
        params.start_date = this.dateRange[0],
        params.end_date = this.dateRange[1]
      }
      return axios.get(url,{
        params: params
      })
      .then(function (response) {
        that.yData = response.data.data.requests_per_day_ydata.map(value => {
          if (langPrefix !== '') {
            value.name = that.$t(langPrefix + value.name);
          }
          return value;
        });
        that.xData = response.data.data.requests_per_day_xdata;
      }).catch(function (error) {
        console.log(error);
      })
    }
  }
}
</script>

<style scoped>
  .stackchart {
    position: relative;
  }
  .stack-radios {
    margin-right: 5px;
  }
</style>