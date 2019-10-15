<template>
    <div style="flex-grow: 1;">
        <heading class="custom-heading" :icon="headingIcon" :title="$t(`menu.${activeName}`)" shadow="heavy" />
        <el-row :gutter="20" class="dashboard" style="margin-bottom: 24px;" type="flex">
            <el-col class="dashboard-tabpanel">
                <el-tabs type="border-card" @tab-click="handleTabClick" v-model="activeName">
                    <el-tab-pane :label="$t('menu.requests')" name="requests">
                        <el-row type="flex">
                            <el-col :span="24">
                                <statistics-card :totalRequest="totalRequest" :data="reqStatusCount" :avgReqDuration="avgReqDuration" :animationTrigger="activeName"></statistics-card>
                            </el-col>
                        </el-row>
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="24">
                                <el-card class="chart-card" :header="$t('dashboard.requests_by_creation_date')">
                                    <chart-stacked-column type="request_by_creation_date" :startDate="startDates.requests"></chart-stacked-column>
                                </el-card>
                            </el-col>
                         </el-row>
                        <el-row :gutter="20" style="margin-bottom: 24px;" type="flex">
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.requests_by_status')">
                                    <chart-pie-and-donut type="request_by_status" :colNum="3" :bottom="true" :startDate="startDates.requests"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.requests_by_category')">
                                    <chart-pie-and-donut type="request_by_category" :bottom="true" :colNum="3" :startDate="startDates.requests"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.requests_by_assigned_status')">
                                    <chart-pie-and-donut type="request_by_assigned_status" :bottom="true" :colNum="3" :startDate="startDates.requests"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20" style="margin-bottom: 24px;" type="flex">
                            <el-col :span="24">
                                <el-card class="chart-card">
                                    <el-tabs v-model="activeChart" @tab-click="handleHeatmapTabClick">
                                        <el-tab-pane :label="$t('dashboard.week_hour')" name="week">
                                            <div class="chart-filter in-toolbar">              
                                                <el-date-picker
                                                    v-model="weekSelected"
                                                    type="week"
                                                    :format="$t('general.date_range.week') + ' WW.yyyy'"
                                                    value-format="dd.MM.yyyy"
                                                    :placeholder="$t('general.date_range.peek_week')"
                                                    popper-class="custom-week-panel"
                                                >
                                                </el-date-picker>
                                            </div>
                                            <chart-heat-map type="week-hour" :tab="activeChart" :week="weekSelected"></chart-heat-map>
                                        </el-tab-pane>
                                        <el-tab-pane :label="$t('dashboard.month_date')" name="month">
                                            <chart-heat-map type="month-date" :tab="activeChart"></chart-heat-map>
                                        </el-tab-pane>
                                    </el-tabs>
                                </el-card>
                            </el-col>
                        </el-row>
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="12">
                                <el-card class="chart-card" :header="$t('dashboard.requests.property_managers')">
                                    <managers-list type="property-managers"></managers-list>
                                </el-card>
                            </el-col>
                            <el-col :span="12">
                                <el-card class="chart-card" :header="$t('dashboard.requests.service_partners')">
                                    <services-list type="service-partners"></services-list>
                                </el-card>
                            </el-col>
                        </el-row>
                     
                    </el-tab-pane>
                    <el-tab-pane :label="$t('menu.buildings')" name="buildings">
                        <el-row type="flex">
                            <el-col :span="24">
                                <buildings-statistics-card :data="buildingStatistics" :animationTrigger="activeName"></buildings-statistics-card>
                            </el-col>
                        </el-row>
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="24">
                                <el-card class="chart-card" :header="$t('dashboard.buildings.buildings_by_creation_date')">
                                    <chart-column-line type="buildings_by_creation_date" :startDate="startDates.buildings"></chart-column-line>
                                </el-card>
                            </el-col>
                        </el-row>
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="24">
                                <el-card class="chart-card" :header="$t('dashboard.buildings.buildings_map')">
                                    <google-map type="buildings"></google-map>
                                </el-card>
                            </el-col>
                        </el-row>
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="16">
                                <el-card class="chart-card" :header="$t('dashboard.buildings.latest_buildings')">
                                    <latest-buildings type="buildings"></latest-buildings>
                                </el-card>
                            </el-col>
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.buildings.buildings_by_state')">
                                   <buildings-by-state type="buildings_by_state" :startDate="startDates.requests"></buildings-by-state>
                                    <!-- <chart-pie-and-donut type="buildings_by_state" :cented="true" :colNum="3" :startDate="startDates.requests"></chart-pie-and-donut> -->
                                </el-card>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane :label="$t('menu.residents')" name="residents">
                        <el-row type="flex">
                            <el-col :span="24">
                                <residents-statistics-card :data="residentsStatistics" :animationTrigger="activeName"></residents-statistics-card>
                            </el-col>
                        </el-row>
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="24">
                                <el-card class="chart-card" :header="$t('dashboard.residents_by_creation_date')">
                                    <chart-stacked-column type="residents_by_creation_date" :startDate="startDates.residents"></chart-stacked-column>
                                </el-card>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20" style="margin-bottom: 24px;" type="flex">
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.residents_by_request_status')">
                                    <chart-pie-and-donut type="residents_by_request_status" :colNum="3" :centered="true" :startDate="startDates.residents"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.residents_by_status')">
                                    <chart-pie-and-donut type="residents_by_status" :colNum="3" :startDate="startDates.residents"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.residents_by_language')">
                                    <chart-pie-and-donut type="residents_by_language" :colNum="3" :startDate="startDates.residents"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                        </el-row>
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="16">
                                <el-card class="chart-card" :header="$t('dashboard.residents.latest_residents')">
                                    <latest-residents type="residents"></latest-residents>
                                </el-card>
                                <el-row style="margin-top: 24px" :gutter="20" tyle="flex">
                                    <el-col :span="12">
                                        <el-card class="chart-card col-3" :header="$t('dashboard.residents_by_title')">
                                            <chart-pie-and-donut type="residents_by_title" :colNum="3" :startDate="startDates.residents" :centered="true"></chart-pie-and-donut>
                                        </el-card>
                                    </el-col>
                                    <el-col :span="12">
                                        <el-card class="chart-card col-3" :header="$t('dashboard.residents_by_gender')">
                                            <chart-residents-by-gender type="residents_by_gender" :startDate="startDates.residents"></chart-residents-by-gender>
                                        </el-card>
                                    </el-col>
                                </el-row>
                            </el-col>
                             <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.residents_by_device')">
                                    <chart-users-by-device type="residents_by_device" :colNum="3" :startDate="startDates.residents"></chart-users-by-device>
                                </el-card>
                                <el-card class="chart-card col-3 mt-24" :header="$t('dashboard.residents_by_age')">
                                    <chart-residents-by-age type="residents_by_age" :colNum="3" :startDate="startDates.residents"></chart-residents-by-age>
                                </el-card>
                            </el-col>
                        </el-row>
                     
                    </el-tab-pane>
                    <el-tab-pane :label="$t('menu.pinboard')" name="pinboard">
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="24">
                                <el-card class="chart-card" :header="$t('dashboard.pinboard_by_creation_date')">
                                    <chart-stacked-column type="pinboard_by_creation_date" :startDate="startDates.pinboard"></chart-stacked-column>
                                </el-card>
                            </el-col>
                         </el-row>
                        <el-row :gutter="20" style="margin-bottom: 24px;" type="flex">
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.pinboard_by_status')">
                                    <chart-pie-and-donut type="pinboard_by_status" :colNum="3" :startDate="startDates.pinboard"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.pinboard_by_type')">
                                    <chart-pie-and-donut type="pinboard_by_type" :colNum="3" :startDate="startDates.pinboard"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20" style="margin-bottom: 24px;" type="flex">
                            <el-col :span="16">
                                <el-card class="chart-card" :header="$t('dashboard.pinboard.latest_pinboard')">
                                    <latest-pinboard></latest-pinboard>
                                </el-card>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <!-- <el-tab-pane :label="$t('menu.listing')" name="listing">
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="24">
                                <el-card class="chart-card" :header="$t('dashboard.listings_by_creation_date')">
                                    <chart-stacked-column type="listings_by_creation_date" :startDate="startDates.listings"></chart-stacked-column>
                                </el-card>
                            </el-col>
                         </el-row>
                        <el-row :gutter="20" style="margin-bottom: 24px;" type="flex">
                            <el-col :span="8">
                                <el-card class="chart-card col-3" :header="$t('dashboard.listings_by_type')">
                                    <chart-pie-and-donut type="listings_by_type" :colNum="3" :startDate="startDates.listings"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                            <el-col :span="16">
                                <el-card class="chart-card" :header="$t('dashboard.latest_listings')">
                                    <latest-listings type="latest_listings"></latest-listings>
                                </el-card>
                            </el-col>
                        </el-row>
                    </el-tab-pane> -->
                </el-tabs>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import axios from '@/axios';
    import StatisticsCard from 'components/dashboard/StatisticsCard';
    import ChartStackedColumn from 'components/dashboard/ChartStackedColumn';
    import ChartPieAndDonut from 'components/dashboard/ChartPieAndDonut'; 
    import ChartHeatMap from 'components/dashboard/ChartHeatMap';
    import Heading from 'components/Heading';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import ColoredStatisticsCard from 'components/ColoredStatisticsCard.vue';
    import ProgressStatisticsCard from 'components/ProgressStatisticsCard.vue';
    import CircularProgressStatisticsCard from 'components/CircularProgressStatisticsCard.vue';

    import BuildingsStatisticsCard from 'components/BuildingsStatisticsCard';
    import ChartColumnLine from 'components/dashboard/ChartColumnLine';
    import ChartUsersByDevice from 'components/dashboard/ChartUsersByDevice';
    import ChartResidentsByGender from 'components/dashboard/ChartResidentsByGender';
    import ResidentsStatisticsCard from 'components/dashboard/ResidentsStatisticsCard';
    import ChartResidentsByAge from 'components/dashboard/ChartResidentsByAge';

    import LatestListings from 'components/dashboard/LatestListings';
    import GoogleMap from 'components/dashboard/GoogleMap';
    import LatestBuildings from 'components/dashboard/LatestBuildings';
    import LatestResidents from 'components/dashboard/LatestResidents';
    import ManagersList from 'components/dashboard/ManagersList';
    import ServicesList from 'components/dashboard/ServicesList';
    import LatestPinboard from 'components/dashboard/LatestPinboard';
    import BuildingsByState from 'components/dashboard/BuildingsByState';

    export default {
        name: 'AdminDashboard',
        components: {
            Heading,
            StatisticsCard,
            ColoredStatisticsCard,
            ProgressStatisticsCard,
            CircularProgressStatisticsCard,
            ChartStackedColumn,
            ChartPieAndDonut,
            ChartHeatMap,
            BuildingsStatisticsCard,
            ChartColumnLine,
            ChartResidentsByGender,
            ChartUsersByDevice,
            ResidentsStatisticsCard,
            LatestListings,
            GoogleMap,
            LatestBuildings,
            LatestResidents,
            ManagersList,
            ServicesList,
            LatestPinboard,
            BuildingsByState,
            ChartResidentsByAge
        },
        data() {
            return {
                totalRequest: "0",
                avgReqDuration: '',                
                chartDataReqByHour:{
                    xData: [],
                    yData: []
                },
                chartOptionsTotalReqByCreationDate: {},
                reqStatusCount: {},
                buildingStatistics: {},
                residentsStatistics: {},
                statistics: [{
                    icon: 'ti-shopping-cart',
                    color: '#f06292',
                    value: 648,
                    description: 'Requests open'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#26c6da',
                    value: '47.5k',
                    description: 'Requests pending'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#9575cd',
                    value: 764,
                    description: 'Requests done'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#1a237e',
                    value: 256,
                    description: 'Requests archived'
                }],
                liteStatistics: [{
                    icon: 'ti-shopping-cart',
                    color: '#9575cd',
                    value: 764,
                    description: 'Daily earnings'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#1a237e',
                    value: 256,
                    description: 'Listings'
                }],
                headingIcon: 'icon-chat-empty',
                activeName: 'requests',
                activeChart: 'week',
                weekSelected: null,
                startDates: {
                    requests: '',
                    buildings: '',
                    pinboard: '',
                    listings: '',
                    residents: ''
                },
            }
        },
        computed: {
        },
        methods: {
            getReqStatastics() {
                let that = this;

                return axios.get('admin/statistics')
                .then(function (response) {
                    that.reqStatusCount = response.data.data.requests_per_status;

                    that.totalRequest = response.data.data.total_requests;
                    that.avgReqDuration = response.data.data.avg_request_duration;

                    that.buildingStatistics = {
                        total_buildings: response.data.data.total_buildings,
                        card_data: response.data.data.buildings_per_status
                    };

                    that.residentsStatistics = {
                        total_residents: response.data.data.total_residents,
                        card_data: response.data.data.residents_per_status
                    };
                    that.startDates = response.data.data.all_start_dates;
                }).catch(function (error) {
                    console.log(error);
                })
            },

            handleTabClick(tab, event) {
                const icons = {
                    'requests': 'icon-chat-empty',
                    'buildings': 'icon-commerical-building',
                    'pinboard': 'icon-megaphone-1',
                    'listing': 'icon-basket',
                    'residents': 'icon-group'
                };
                this.headingIcon = icons[tab.name];
            },
            handleHeatmapTabClick(tab, event) {
                
            }
        },
        created(){
            this.getReqStatastics();
        }
    }
</script>


<style lang="scss" scoped>
    .custom-heading {
        margin-bottom: 2em;
    }
    .dashboard{
        padding: 5px;
    }
</style>
<style lang="scss">
.mt-24 {
    margin-top: 24px;
}
.el-row.dashboard {
    margin-top: -137px;

    @media screen and (max-width: 1000px) {
        margin-top: 0;
    }
}
.dashboard-tabpanel{
    .el-tabs--border-card > .el-tabs__header .el-tabs__item{
        flex-basis: 0;
        -webkit-box-flex: 1;    
        flex-grow: 1;
        text-align: center;
        color: #495057;    
        cursor: pointer;    
        font-weight:400;    
        -webkit-box-align: center;
        align-items: center;
        text-align: center;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 0 13px !important;

        &.is-active, &:hover{
            background: var(--primary-color);
            //border-radius: 120px;
            border-right-color: none;
            border-left-color: none;
            -ms-flex-positive: 1;
            color: #fff !important;
            transition: background-color .3s ease,color .3s ease !important;
        }

        &:first-child {
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        &:last-child {
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }
    }
    .el-tabs__nav {
        float: none;
        text-align: center;
        border-radius: 120px;
        padding: .75rem;    
        display: flex;
        flex-wrap: wrap;
        width: fit-content;
        margin: 1.5rem 0 1.5em auto;

        @media screen and (max-width: 1000px) {
            margin: 1.5rem auto;
        }
    }
    .el-tabs--border-card{
        background:none;
    }
    .el-tabs--border-card{
        border: none;
        background: none;
        box-shadow: none;
    }
    .el-tabs--border-card > .el-tabs__header{
        border-bottom: none !important;
        background: none !important;    
    }
    .chart-card{
        //height: 420px;

        overflow: visible;

        .el-card__header {
            padding: 15px;
            font-size: 15px;
        }

        .dashboard-table {
            position: relative;

            .link-container {
                position: absolute;
                top: -55px;
                right: 0px;
                text-align: right;
                padding: 20px 15px;
                font-size: 14px;

                a {
                    text-decoration: none;
                    color: #525252;

                    &:hover {
                        color: #303133;
                    }
                }
            }
        }

        .chart-filter {
            display: flex;
            align-items: center;

            &.in-toolbar {
                position: absolute;
                top: -42px;
                right: 50px;

                background-color: transparent;
                border-bottom: none;
                padding: 0;
            }

            .el-radio-button__inner {
                padding: 8px 12px;
                font-weight: 400;
            }

            .el-date-editor {
                width: 135px;

                .el-input__inner {
                    height: 33px;
                    line-height: 33px;
                }

                .el-input__icon {
                    display: flex;
                    justify-content: center;
                    align-items: center;

                    &.el-range__close-icon {
                        display: none;
                    }
                }
            }

            .el-date-editor--week {
                width: 160px;

                input {
                    text-align: center;
                    padding-right: 10px;
                }

                .el-input__suffix {
                    display: none;
                }
            }

            .el-range-editor {
                width: 250px;
                padding: 0 0 0 7px;
                height: 32px;
                line-height: 32px;

                .el-range-separator {
                    width: 6%;
                }
            }
        }

        .apexcharts-toolbar {
            // margin-top: -88px;
            margin-top: -38px;
            margin-right: 7px;
            .apexcharts-menu.open {
                right: 7px;
            }
        }

        .apexcharts-legend.center.position-bottom {
            padding-top: 10px;
        }

        .el-tabs {
            .el-tabs__header {
                margin-bottom: 0;
                .el-tabs__nav {
                    margin: 0;
                    padding: 6px 0;
                    margin-left: 15px;

                    .el-tabs__item {
                        font-size: 15px;
                        font-weight: 400;
                    }
                }
            }

            .el-tabs__content {
                overflow: visible;
            }
        }
    }
}
.chart-card .el-card__body{
    padding: 0 0 0 0;
}
.chart-filter{
    background-color: #fbfbfb; 
    border-bottom: 1px solid #e1e5eb; 
    padding: .5rem 14px;
}

.dashboard .box-card-count{
    .el-card__body{
        height: 100%;
    }
    .total-box-card-header{
    clear: both;
    padding: 15px 20px 5px 20px;
    opacity: 0.5;
    text-transform: uppercase;
    text-align: center;
    border-bottom: none;
    box-sizing: border-box;
    font-size: 13px;
  }  
  .total-box-card-body{
    clear: both;
    padding: 5px 20px 15px 20px;
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    text-align: center;
  }
  .el-divider--horizontal{
    margin: 0 0;
  }
}
.dashboard .box-card{ 
    border: none;
    border-bottom: 4px solid transparent;  
    
    .el-card__header {        
        padding: 20px 20px 0px 20px;       
        opacity: 0.5;     
        text-transform: uppercase;        
        border-bottom: none;
        font-size: 13px;
    }
    .box-card-body{
        display: flex;        
        .box-card-count{
            padding: 8px 20px 12px 20px;
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
            text-align: left;            
            float: left;
        }
        .box-card-progress{            
            float: left;
            margin-left: auto;
            padding: 0 15px 0 0;
            position: relative;
            top: -27px;
            margin-bottom: -27px;

            .el-progress__text {
                font-size: 13px !important;
            }
        }
    }
}
.el-picker-panel.custom-week-panel {
    line-height: normal;
    width: min-content;

    .el-picker-panel__body-wrapper {
        .el-date-picker__header {
            margin: 10px 7px 0px;

            .el-picker-panel__icon-btn {
                margin-top: 4px;
                padding: 1px 1px;
            }
            .el-date-picker__header-label {
                font-size: 15px;
                padding: 0 2px;
            }
        }
        .el-picker-panel__content {
            padding: 7px 5px 5px 5px;
            margin: 0;
            width: 202px;

            th {
                width: 25px;
                height: 25px;
                padding:2px;
            }

            td {
                padding: 0;
                width: 25px;
                height: 25px;
                max-width: 25px;

                div {
                    width: 25px;
                    height: 25px;
                    padding: 0;
                    margin: 0;
                }
            }
        }
    }
}
</style>
