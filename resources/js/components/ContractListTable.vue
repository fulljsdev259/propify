<template>
    <div>
        <el-table
            :data="items"
            style="width: 100%"
            class="contract-table"
            >
            <el-table-column
                :label="$t('models.resident.contract.contract_id')"
                prop="id"
            >
                <template slot-scope="scope">
                    <span class="clickable" @click="$emit('edit-contract', scope.$index)">{{scope.row.contract_format}}</span>
                </template>
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.building.name')"
                prop="building.name"
            >
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.unit.name')"
                prop="unit.name"
            >
            </el-table-column>
            <el-table-column
                :label="$t('models.resident.status.label')"
            >
                <template slot-scope="scope">
                    <i class="icon-dot-circled" :class="[constants.contracts.status[scope.row.status] === 'active' ? 'icon-success' : 'icon-danger']"></i>
                    {{ constants.contracts.status[scope.row.status] ? $t('models.resident.contract.rent_status.' + constants.contracts.status[scope.row.status]) : ''}}
                </template>
            </el-table-column>
            <el-table-column
                align="right"
            >
                <template slot-scope="scope">
                    <el-tooltip
                        :content="$t('general.actions.edit')"
                        class="item" effect="light" 
                        placement="top-end">
                            <el-button @click="$emit('edit-contract', scope.$index)" icon="ti-pencil" size="mini" type="success"/>
                    </el-tooltip>
                    <el-tooltip
                        :content="$t('general.actions.delete')"
                        class="item" effect="light" 
                        placement="top-end">
                            <el-button @click="$emit('delete-contract', scope.$index)" icon="ti-trash" size="mini" type="danger"/>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>

        <!-- <el-table
            v-loading="loading.state"
            :data="items"
            :element-loading-background="loading.background"
            :element-loading-spinner="loading.icon"
            :element-loading-text="$t(loading.text)"
            :empty-text="emptyText"
            @selection-change="handleSelectionChange"
            @row-click="editLink">

            <el-table-column
                :key="'OneCol'"
                :width="column.width"
                v-for="(column, key) in headerWithOneCol"
            >
                <template slot-scope="scope">
                    <request-detail-card
                        :item="scope.row"
                        :loading="{state: loading}"
                        @selectionChanged="handleRequestSelectionChange"
                        @editAction="column.editAction(scope.row)"
                        @onChange="scope.row['status']=$event,column.onChange(scope.row)"
                        @pdf-download="column.downloadPDF($event)"
                        :categories="categories"
                    >

                    </request-detail-card>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
            :current-page.sync="page.currPage"
            :page-size.sync="page.currSize"
            :page-sizes="pagination.pageSizes"
            :total="pagination.total"
            @current-change="onCurrentPageChange"
            @size-change="onSizeChange"
            layout="total, sizes, prev, pager, next, jumper"
            v-if="pagination.total"/> -->
    </div>
</template>

<script>

    import uuid from 'uuid/v1'
    import {mapActions, mapGetters} from 'vuex';
    
    import {ResponsiveMixin} from 'vue-responsive-components'

    export default {
        name: 'ContractListTable',
        mixins: [
            ResponsiveMixin
        ],
        props: {
            header: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            items: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            
        },
        data() {
            return {
                search: '',
                filterModel: {},
                uuid,
                selectedItems: [],
                categories: []
            }
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            })
        },
        methods: {
           
        },
    }
</script>

<style lang="scss" scoped>
    .contract-table {
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
