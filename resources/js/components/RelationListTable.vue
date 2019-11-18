<template>
    <div>
        <el-table
            :data="showItems"
            style="width: 100%"
            class="relation-table"
            >
            <div slot="empty">
                <el-alert                                     
                    :title="$t('general.no_data_available')"
                    type="info"
                    show-icon
                    class="no_data_box"
                    :closable="false"
                >
                </el-alert>
            </div>
            <el-table-column
                :label="$t('general.resident')"
                v-if="!hideAvatar"
                >
                <template slot-scope="scope">
                    <el-tooltip
                            :content="`${scope.row.resident.first_name} ${scope.row.resident.last_name}`"
                            class="item"
                            effect="light" placement="top"
                    >
                        <avatar :size="30"
                                :src="'/' + scope.row.resident.user.avatar"
                                v-if="scope.row.resident.user.avatar"></avatar>
                        <avatar :size="28"
                                backgroundColor="rgb(205, 220, 57)"
                                color="#fff"
                                :username="scope.row.resident.user.first_name ? `${scope.row.resident.user.first_name} ${scope.row.resident.user.last_name}`: `${scope.row.resident.user.name}`"
                                v-if="!scope.row.resident.user.avatar"></avatar>
                    </el-tooltip>
                </template>
            </el-table-column>
            <!-- <el-table-column
                :label="$t('models.resident.relation.relation_id')"
                v-if="!hideRelationId"
                prop="relation_format"
            >
            </el-table-column> -->
            <el-table-column
                :label="$t('models.resident.building.name')"
                v-if="!hideBuilding"
                prop="building.name"
            >
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.unit.name')"
                v-if="!hideUnit"
                prop="unit.name"
            >
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.status.label')"
            >
                <template slot-scope="scope">
                    <i class="icon-dot-circled" :class="[constants.relations.status[scope.row.status] === 'active' ? 'icon-success' : 'icon-danger']"></i>
                    {{ constants.relations.status[scope.row.status] ? $t('models.resident.relation.status.' + constants.relations.status[scope.row.status]) : ''}}
                </template>
            </el-table-column>
            <el-table-column
                align="right"
                :width="70"
            >
                <template slot-scope="scope">
                    <el-tooltip
                        :content="$t('general.actions.edit')"
                        class="item" effect="light" 
                        placement="top-end">
                            <el-button @click="$emit('edit-relation', scope.$index)" icon="ti-search" size="mini" round/>
                    </el-tooltip>
                    <!-- <el-tooltip
                        :content="$t('general.actions.delete')"
                        class="item" effect="light" 
                        placement="top-end">
                            <el-button @click="$emit('delete-relation', scope.$index)" icon="ti-trash" size="mini" type="danger" round/>
                    </el-tooltip> -->
                </template>
            </el-table-column>
        </el-table>
        <div v-if="showLength < totalLength">
            <el-button @click="loadMore" size="mini" style="margin-top: 15px" type="text">{{$t('general.load_more')}}</el-button>
        </div>
    </div>
</template>

<script>

    import uuid from 'uuid/v1'
    import {mapActions, mapGetters} from 'vuex';
    import {Avatar} from 'vue-avatar'
    import {ResponsiveMixin} from 'vue-responsive-components'
    

    export default {
        name: 'RelationListTable',
        mixins: [
            ResponsiveMixin
        ],
        components: {
            Avatar,
        },
        props: {
            items: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            hideRelationId: {
                type: Boolean,
                default: false
            },
            hideBuilding: {
                type: Boolean,
                default: false
            },
            hideUnit: {
                type: Boolean,
                default: false
            },
            hideAvatar: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                // showItems: [],
                totalLength: 0,
                showLength: 0,
            }
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            showItems () {
                return this.items.slice(0, this.showLength)
            }
        },
        methods: {
           loadMore() {
               this.showLength += 5
               if(this.showLength > this.totalLength)
                this.showLength = this.totalLength
           }
        },
        mounted() {
            this.totalLength = this.items.length
            this.showLength = this.totalLength < 5 ? this.totalLength : 5
        },
        watch: {
            items () {
                this.totalLength = this.items.length
                this.showLength = this.totalLength < 5 ? this.totalLength : 5
            }
        },
    }
</script>

<style lang="scss" scoped>
    .relation-table {
        .clickable {
            display: block;
            cursor: pointer;
            width: 100%;
        }
        .icon-success {
            color: #5fad64;
        }
        .icon-danger {
            color: #dd6161;
        }
    }
</style>
