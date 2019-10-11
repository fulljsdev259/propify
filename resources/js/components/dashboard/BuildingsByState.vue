<template>
    <div v-if="startDate" class="progress-bar-container">
        <div class="chart-filter in-toolbar">              
            <custom-date-range-picker 
                rangeType="all"
                :initialRange="dateRange"
                :pickHandler="pickHandler"
                :style="{display: showPicker ? 'inline-flex' : 'none'}"
                :startDate="startDate"
            />
            <div class="show-button" v-if="!showPicker" @click="handleShowClick(true)"><i class="el-icon-date"></i></div>
            <div class="hide-button" v-if="showPicker" @click="handleShowClick(false)"><i class="el-icon-circle-close"></i></div>
        </div>
        <el-row class="progress-card-body">
            <el-col :span="24" v-for="(data, index) in yData" :key="index">
                <div class="progress-bar">
                    <progress-bar :label="`${xData[index]}`" :value="yData[index]" :maxValue="total" :color="`${colorsPredefined[index%12]}`" ></progress-bar>
                </div>
            </el-col>
        </el-row>       
    </div>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'
import {format, subDays, isBefore, isAfter, parse} from 'date-fns'
import axios from '@/axios';

import CustomDateRangePicker from 'components/CustomDateRangePicker';
import chartMixin from 'mixins/adminDashboardChartMixin';
import ProgressBar from 'components/ProgressBar';

export default {
  components: {
    'apexchart': VueApexCharts,
    CustomDateRangePicker,
    ProgressBar
  },
  mixins: [chartMixin()],
  props: {
      height: {
          type: Number,
          default: ()=> {
              return 400;
          }
      }
  },  
  data() {
    return {      
          showPicker: false,  
    }
  },
  computed:{
  
  },
    methods: {
        fetchData(){
            if (this.dateRange == null) {
                return;
            }
            let that = this;                                               
            let url = '';
            let langPrefix = '';
            if(this.type === 'request_by_status'){
                this.chartType = 'pie';
                url = 'admin/donutChart';
                langPrefix = 'models.request.status.';
            }
            else if(this.type === 'request_by_category'){
                this.chartType = 'donut';
                url = 'admin/donutChartRequestByCategory';
                langPrefix = '';
            }
            else if(this.type === 'request_by_assigned_status'){
                this.chartType = 'donut';
                url = 'admin/chartRequestByAssignedProvider';
                langPrefix = 'dashboard.requests.';
            }
            else if(this.type === 'buildings_by_state'){
                this.chartType = 'pie';
                url = 'admin/pieChartBuildingByState';
                langPrefix = '';
            }
            else if (this.type === 'pinboard_by_status') {
                this.chartType = 'donut';
                url = 'admin/donutChart?table=pinboard&column=status';
                langPrefix = 'models.pinboard.status.';
            }
            else if (this.type === 'pinboard_by_type') {
                this.chartType = 'donut';
                url = 'admin/donutChart?table=pinboard&column=type';
                langPrefix = 'models.pinboard.type.';
            }
            else if (this.type === 'listings_by_type') {
                this.chartType = 'donut';
                url = 'admin/donutChart?table=listings&column=type';
                langPrefix = 'models.listing.type.';
            }
            else if (this.type === 'residents_by_request_status') {
                this.chartType = 'donut';
                url = 'admin/donutChartResidentsByDateAndStatus';
                langPrefix = 'models.request.status.';
            }
            else if (this.type === 'residents_by_status') {
                this.chartType = 'donut';
                url = 'admin/donutChart?table=residents&column=status';
                langPrefix = 'models.resident.status.';
            }
            else if (this.type === 'residents_by_language') {
                this.chartType = 'donut';
                url = 'admin/chartResidentLanguage';
                langPrefix = '';
            }
            else if (this.type === 'residents_by_title') {
                this.chartType = 'donut';
                url = 'admin/donutChart?table=residents&column=title';
                langPrefix = 'general.salutation_option.';
            }

            return axios.get(url,{
            	params: {
                    start_date: this.dateRange[0],
                    end_date: this.dateRange[1]
                }
            })
            .then(function (response) {
                that.total = 0;
                that.yData = response.data.data.data.map((val) => {
                    that.total += parseInt(val);
                    return parseFloat(val) || 0;
                });
                that.xData = response.data.data.labels.map(function(e) {
                    if (langPrefix !== '') {
                        return that.$t(langPrefix + e);
                    }
                    else {
                        return e;
                    }
                });
            }).catch(function (error) {
                console.log(error);
            })
        },
        handleShowClick(val) {
            this.showPicker = val;
        }
    },
    watch: {
      'startDate': function(val) {
          if (val) {
            this.dateRange = [val, format(new Date(), 'DD.MM.YYYY')];
            this.fetchData();
          }
      }
    }
}
</script>
<style lang="scss" scoped>
    .progress-bar-container {
        .progress-card-body {
            max-height: 242px;
            overflow: auto;
            padding: 20px 5px;
            .progress-bar {
                padding-top: 5px;
                padding-bottom: 10px;
            }
        }
    }
</style>
