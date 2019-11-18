<template>
    <div>
        <div v-if="list.length > 0">
            <el-timeline>
                <el-timeline-item
                        v-for="(element, index) in list"
                        :key="index"
                        :color="'var(--primary-color)'">
                        <el-row :gutter="20" class="main-section">
                            <el-col class="avatar-square" v-if="element.media.length > 0">
                                 <el-tooltip
                                    :content="$t('models.pinboard.images')"
                                    class="item"
                                    effect="light" placement="top">
                                    <span>
                                        <el-avatar  shape="square" :size="40" :src="element.media[0].url"></el-avatar>
                                    </span>
                                 </el-tooltip>
                            </el-col>
                            <el-col style="flex: 1;">
                                <h4>
                                    {{element.title}} 
                                    <TimelineStatus v-if="fetchAction == 'getRequests'" :status="element.status" />
                                    <template v-if="fetchAction == 'getPinboardTruncated'">
                                         <el-tooltip
                                            :content="element.announcement ? $t('models.pinboard.announcement') : $t('models.pinboard.type.article')"
                                            class="item"
                                            effect="light" placement="top">
                                            <span>
                                                <el-button
                                                    class="btn-hover"
                                                    type="succcess"
                                                    size="mini"
                                                    round
                                                    :style="{'padding': '2px 5px' ,'border-color': '#d2ecd4','color' : '#6AC06F','background-color': '#f0f9f1'}"
                                                >
                                                    {{element.announcement ? $t('models.pinboard.announcement') : $t('models.pinboard.type.article')}}
                                                </el-button>
                                            </span>
                                         </el-tooltip>
                                    </template>
                                    <template v-if="loggedInUser.roles[0].name === 'administrator' || fetchAction === 'getRequests'">
                                        <span
                                            class="btn-view"
                                          >
                                            <template
                                                >
                                                <el-button
                                                    size="mini"
                                                    round
                                                    icon="ti-search"
                                                    @click="viewPage(element,fetchAction)"
                                                >
                                                   {{$t('general.actions.view')}}
                                                </el-button>
                                            </template>
                                        </span>
                                    </template>
                                </h4>
                                <p class="subtitle text-secondary" v-if="element.category.name">
                                    <el-tooltip
                                        :content="(element.category.name ? '' : '') + $t('models.pinboard.category.label')"
                                        class="item"
                                        effect="light" placement="top">
                                        <span>
                                            {{ $t(`models.request.category_list.${element.category.name}`) }} 
                                            {{element.sub_category ? ' > ' + $t(`models.request.sub_category.${element.sub_category.name}`)   : ''}} 
                                        </span>
                                    </el-tooltip>
                                </p>
                                <p class="activity-date text-secondary" v-if="fetchAction == 'getPinboardTruncated'">
                                    <el-tooltip
                                        :content="$t('models.resident.created_date')"
                                        class="item"
                                        effect="light" placement="top">
                                            <!-- <span v-if="fetchAction == 'getRequests'">{{ extractDate(element.created_at) }}</span> -->
                                            <span v-if="fetchAction == 'getPinboardTruncated'">{{ new Date(element.created_at) | formatDate}}</span>
                                    </el-tooltip> 
                                </p>
                                <div class="reactions" v-if="fetchAction == 'getListings'">
                                    <div><i class="ti-thumb-up"  />{{element.likes_count}}</div>
                                    <div><i class="ti-comments" /> {{element.comments_count}} </div>
                                    <div><i class="ti-gallery"  /> {{element.media.length}}</div>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="24" v-if="fetchAction == 'getRequests'">
                                <p class="activity-date text-secondary">
                                    <el-tooltip
                                        :content="$t('models.resident.created_date')"
                                        class="item"
                                        effect="light" placement="top">
                                            <span v-if="fetchAction == 'getRequests'">{{ extractDate(element.created_at) }}</span>
                                    </el-tooltip> 
                                </p>
                            </el-col>
                        </el-row>
                </el-timeline-item>
            </el-timeline>
            <div v-if="meta.current_page < meta.last_page">
                <el-button @click="loadMore" size="mini" style="margin-top: 15px" type="text">{{$t('general.load_more')}}</el-button>
            </div>
        </div>
        <div v-else>
            <el-alert
                :title="noDataMessage"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>
        </div>
    </div>
</template>

<script>
    import TimelineStatus from 'components/TimelineStatus';
    import {format} from 'date-fns'
    
import { mapGetters } from 'vuex';
    export default {
        async created() {
            await this.fetch();
        },
        data() {
            return {
                list: [],
                meta: {},
            }
        },
        props: {
            filter: {
                type: String,
                required: true
            },
            filterValue: {
                type: Number,
                required: true
            },
            fetchAction: {
                type: String,
                required: true
            },
            noDataMessage: {
                type: String,
            }

        },
         components: {
            TimelineStatus
        },
        filters: {
            formatDate(date) {
                return format(date, 'DD.MM.YYYY, HH:mm')
            }
        },
        computed: {
            ...mapGetters({
                loggedInUser : 'loggedInUser'
            })
        },
        methods: {
            extractDate(date) {
                var res = date.split(" ");
                return res[0] + ', ' +res[1].substr(0, 5);
            },
            viewPage(data, action) {
                if (action === 'getRequests') {
                    this.$router.push({
                       name: 'adminRequestsEdit',
                       params: {
                           id : data.id
                       }
                   });
                }
                if (action === 'getListings') {
                    this.$router.push({
                       name: 'adminListingsEdit',
                       params: {
                           id : data.id
                       }
                   });
                }
                if (action === 'getPinboardTruncated') {
                    this.$router.push({
                       name: 'adminPinboardEdit',
                       params: {
                           id : data.id
                       }
                   });
                }
            },
            async fetch(page = 1) {
                try {
                    const resp = await this.$store.dispatch(this.fetchAction, {
                        [this.filter]: this.filterValue,
                        per_page: 5,
                        page
                    });
                    this.meta = _.omit(resp.data, 'data');
                    let currentFetchedDate = resp.data.data.map(data => {
                        switch (this.fetchAction) {
                            case 'getRequests':
                                break;
                            case 'getPinboardTruncated':
                                data.title = data.preview
                                data.category = {
                                    ...data.category,
                                    name: null
                                }
                                break;
                            case 'getListings':
                                data.category = {
                                    ...data.category,
                                    name: null
                                }
                                break;
                        }
                        return data
                    })
                    if (page === 1) {
                        this.list = currentFetchedDate;
                    } else {
                        this.list.push(...currentFetchedDate);
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            loadMore() {
                if (this.meta.current_page < this.meta.last_page) {
                    this.fetch(this.meta.current_page + 1);
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    .btn-view{
        float: right;
    }
    .btn-hover:hover{
        text-decoration: none;
        cursor: text;
    }
    .main-section {
        display: flex;
    }
    .avatar-square{
        width: 50px;
        padding-top: 5px;
    }
    .el-timeline {
        h4 {
            margin-bottom: 0;
            color: #616161;
            font-weight: 700;
            margin-top: 0;
        }

        p {
            margin-top: 5px;
            margin-bottom: 0;
        }

        .subtitle {
            font-size: 13px;
        }

        .activity-date {
            font-size: 12px;
        }

        ul.el-timeline {
            padding-left: 0;
        }
    }

    .el-timeline-item:last-of-type {
        padding-bottom: 0;
    }
     .reactions {
        display: flex;
        align-items: center;
        padding-top: 3px;
        > div {
            i {
                vertical-align: middle;
            }
            &:not(:last-child) {
                margin-right: 4px;
                &:after {
                    content: '\2022';
                    margin-left: 4px;
                    margin-right: 2px;
                }
            }
        }
        .el-button {
            padding: 0;
            :global(span) {
                margin-left: 5px;
            }
            &:before {
                background-color: transparent;
            }            
        }
        .btn-wrap:not(:first-child) {
            margin-left: 5px;
        }
        .icon-success {
            color: #5fad64;
        }

    }
</style>
