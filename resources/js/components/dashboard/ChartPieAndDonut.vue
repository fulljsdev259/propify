<template>
    <div v-if="startDate" class="piechart">
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
        <el-row type="flex" :class="{'legendMargin':bottom}">
            <el-col :span="24">
                <apexchart :type="chartType" :options="chartOptions" :series="series" />
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

export default {
  components: {
    'apexchart': VueApexCharts,
    CustomDateRangePicker
  },
  mixins: [chartMixin()],
  props: {
    colNum: {
        type: Number
    },
    centered: {
        type: Boolean
    },
    bottom: {
        type: Boolean,
        default: () => {
            return false;
        }
    }
  },  
  data() {
    return {        
        chartType: 'pie',
        showPicker: false,
        windowWidth: window.innerWidth,
        legendHeight: 380
    }
  },
  computed:{
    legendBottomMargin() {
        return this.bottom?5:0;
    },
    chartOptions: function(){
        let responsive = [];
        if(this.bottom == true) {
            return {
                labels: this.xData,
                legend: {
                    show: true,
                    position: 'bottom',
                    width: '100%',
                },
                chart:{
                    toolbar: this.toolbar,
                    width: (this.windowWidth-400)/this.colNum>400?400:(this.windowWidth-400)/this.colNum,
                    height: 'auto'
                },
                colors: this.colors
            };
        } else if (this.colNum == 2) {
            responsive = [{
                breakpoint: 1300,
                options: {
                    chart: {
                        width: 490,
                    },
                    legend: {
                        width: 170,
                    }
                }
            }, {
                breakpoint: 1200,
                options: {
                    chart: {
                        width: '100%',
                        height: 'auto'
                    },
                    legend: {
                        position: 'bottom',
                        width: undefined
                    }
                }
            }, {
                breakpoint: 480,
                options: {
                    legend: {
                        show: false
                    }
                }
            }];
        }
        else if (this.colNum == 3 && this.centered == true) {
            responsive = [{
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
        }
        else {
            responsive = [{
                breakpoint: 1800,
                options: {
                    chart: {
                        width: 490,
                    },
                    legend: {
                        width: 170,
                    }
                }
            }, {
                breakpoint: 1650,
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
        }
        return {
            labels: this.xData,
            responsive: responsive,
            legend: {
                show: true,
                width: this.centered ? undefined : 220
            },
            chart:{
                toolbar: this.toolbar,
                width: this.centered? '100%' : 540,
                height: 320
            },
            colors: this.colors
        }
    },
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
            else if (this.type === 'residents_by_age') {
                this.chartType = 'donut';
                url = 'residents/age-statistics';
                langPrefix = '';
            }

            return axios.get(url,{
            	params: {
                    start_date: this.dateRange[0],
                    end_date: this.dateRange[1]
                }
            })
            .then(function (response) {
                that.yData = response.data.data.data.map(val => parseFloat(val) || 0);
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
    },
    mounted() {
        this.$nextTick(() => {
            window.addEventListener('resize', () => {
                this.windowWidth = window.innerWidth;
                if(this.windowWidth < 850)
                    this.legendHeight = 150;
            });
    })
},
}
</script>
<style lang="scss">
    .chart-card {
        @media screen and (max-width: 1200px) {
            .piechart .apexcharts-canvas {
                margin-top: 30px;
            }
        }

        @media screen and (max-width: 1650px) {
            &.col-3 .piechart .apexcharts-canvas {
                margin-top: 30px;
            }
        }
        .piechart {
            //max-height: 420px;
            position: relative;

            .apexcharts-canvas {
                position: unset;
                margin-right: auto;
                margin-left: auto;
            }

            .apexcharts-legend {
                display: flex;
                //flex-direction: column;
                justify-content: center !important;
            }

            .chart-filter {
                &.in-toolbar {
                    right: 40px;
                }

                .show-button {
                    cursor: pointer;
                    padding: 5px 0;
                    color: #6E8192;
                    font-size: 17px;

                    &:hover {
                        color: #333;
                    }
                }

                .hide-button {
                    cursor: pointer;
                    position: absolute;
                    padding: 7px;
                    top: 0;
                    right: 0;
                    color: #C0C4CC;

                    &:hover {
                        color: #333;
                    }
                }
                .el-input__icon.el-range__close-icon {
                    display: none;
                }

                .el-range-editor {
                    padding-right: 15px;
                }
            }
        }
    }
    .legendMargin {
        .apexcharts-legend {
            .apexcharts-legend-series {
                margin-bottom: 5px !important;
            }
        }
    }
</style>