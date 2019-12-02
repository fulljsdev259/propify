<template>
    <div>
        <el-table
            :data="showItems"
            style="width: 100%"
            class="relation-table"
            :show-header="false"
            @row-dblclick="handleRowDblClick"
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
                :label="$t('models.resident.relation.type.label')"
                prop="type"
                :width="120"
            >
                <template slot-scope="scope">
                    {{translateRelationType(scope.row.type)}}
                </template>
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.quarter.name')"
                prop="quarter.name"
                :min-width="130"
            >
                 <template slot-scope="scope">
                    {{ scope.row.quarter ? scope.row.quarter.internal_quarter_id + ' ' + scope.row.quarter.name : ''}}
                </template>
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.relation.type.label')"
                prop="type"
            >
                <template slot-scope="scope">
                    {{translateUnitType(scope.row.unit.type)}}
                </template>
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.unit.name')"
                v-if="!hideUnit"
                prop="unit.name"
                :min-width="120"
            >
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.status.label')"
                :width="30"
            >
                <template slot-scope="scope">
                     <el-tooltip
                        :content="$t('models.resident.relation.status.' + constants.relations.status[scope.row.status])"
                        class="item" effect="light" 
                        placement="top-end">
                        <!-- <span class="status-icon" :style="{ background: constants.relations.status_colorcode[scope.row.status], border: '2px solid ' + getLightenDarkenColor(constants.relations.status_colorcode[scope.row.status], 200)}" >&nbsp;</span> -->
                        <span class="status-icon" :style="{ background: constants.relations.status_colorcode[scope.row.status], border: '2px solid #ffffffe7'}" >&nbsp;</span>
                        <!-- <i class="icon-circle" :class="[constants.relations.status[scope.row.status] === 'active' ? 'icon-active' : (constants.relations.status[scope.row.status] === 'inactive' ? 'icon-inactive' : 'icon-canceled')]"></i> -->
                     </el-tooltip>
                    <!-- {{ constants.relations.status[scope.row.status] ? $t('models.resident.relation.status.' + constants.relations.status[scope.row.status]) : ''}} -->
                </template>
            </el-table-column>
            <!-- <el-table-column
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
                    <el-tooltip
                        :content="$t('general.actions.delete')"
                        class="item" effect="light" 
                        placement="top-end">
                            <el-button @click="$emit('delete-relation', scope.$index)" icon="ti-trash" size="mini" type="danger" round/>
                    </el-tooltip>
                </template>
            </el-table-column> -->
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
    import globalFunction from "helpers/globalFunction";

    export default {
        name: 'RelationListTable',
        mixins: [
            ResponsiveMixin,
            globalFunction
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
            },
            translateUnitType(type) {
                return this.$t(`models.unit.type.${this.constants.units.type[type]}`);
            },
            translateRelationType(type) {
                return this.$t(`models.resident.relation.type.${this.constants.relations.type[type]}`);
            },
            handleRowDblClick(row, col, e) {
                let i = 0
                for(i = 0; i < this.items.length;i ++) {
                    if(this.items[i].id == row.id)
                        break;
                }
                this.$emit('edit-relation', i)
            }
        },
        mounted() {
            this.totalLength = this.items.length
            this.showLength = this.totalLength < 5 ? this.totalLength : 5
        },
        created() {
            document.documentElement.style.setProperty('--active-color', this.constants.relations.status_colorcode[1])
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
        cursor: pointer;
        .clickable {
            display: block;
            cursor: pointer;
            width: 100%;
        }

        :global(.el-table__body-wrapper) {
            :global(table) {
                display: block;
                max-height: 300px;
                overflow-y: auto;
                &::-webkit-scrollbar{
                    width: 8px;
                }
                &::-webkit-scrollbar-thumb{
                    background-color: var(--color-text-placeholder);
                    border: 1px solid transparent;
                    border-radius: 11px;
                    background-clip: content-box;
                }
                &::-webkit-scrollbar * {
                    background: transparent;
                }
            }
        }
        .status-icon {
            width: 13px;
            height: 13px;
            border-radius: 50%;
            display: block;
        }

        .icon-active {
            color: #6b0036;
            background: #6b0036;
            border: 3px solid #f5c3dc;
            border-radius: 50%;
        }
        .icon-inactive {
            color: #878810;
            background: #878810;
            border: 3px solid #f4f5bc;
            border-radius: 50%;
        }
        .icon-canceled {
            color: #c8a331;
            background: #c8a331;
            border: 3px solid #f8edcd;
            border-radius: 50%;
        }
    }
</style>
