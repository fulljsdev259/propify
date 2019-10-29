<template>
    <div>
        <el-table
            :data="items"
            style="width: 100%"
            v-if="items && items.length"
            :show-header="false"
            >
            <el-table-column
                prop="collection_name"
            >
                <template slot-scope="scope">
                    <strong>{{$t(`models.building.media_category.${scope.row.collection_name}`)}}</strong>
                </template>
            </el-table-column>
            <el-table-column
                align="right"
            >
                <template slot-scope="scope">
                    <a :href="scope.row.url" class="file-name" target="_blank">
                        {{scope.row.name}}
                    </a>
                </template>
            </el-table-column>
            <el-table-column
                align="right"
            >
                <template slot-scope="scope">
                    <el-tooltip
                        :content="$t('general.actions.delete')"
                        class="item" effect="light" placement="top-end"
                    >
                        <el-button icon="ti-trash" type="danger" round @click="$emit('delete-document', 'media', scope.$index)" size="mini"/>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>

    import uuid from 'uuid/v1'
    import {mapActions, mapGetters} from 'vuex';

    import {ResponsiveMixin} from 'vue-responsive-components'
    

    export default {
        name: 'BuildingFileListTable',
        mixins: [
            ResponsiveMixin
        ],
        
        props: {
            items: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            hideContractId: {
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
