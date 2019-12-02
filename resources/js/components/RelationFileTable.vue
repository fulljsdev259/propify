<template>
    <el-table
        :data="media"
        v-if="media.length"
        class="relation-file-table"
        :show-header="false"
        >
        <el-table-column
            :label="$t('models.resident.relation.filename')"
            prop="name"
        >
            <template slot-scope="scope">
                <a v-if="scope.row.url" :href="scope.row.url" class="normal" target="_blank"><strong>{{scope.row.name}}</strong></a>
                <span v-else ><strong>{{scope.row.name}}</strong></span>
            </template>
        </el-table-column>
        <el-table-column
            align="right"
            width="30"
        >
            <template slot-scope="scope">
                <el-tooltip
                    :content="$t('general.actions.delete')"
                    class="item" effect="light" 
                    placement="top-end">
                        <el-button @click="$emit('delete', scope.$index)" icon="ti-trash" size="mini" />
                </el-tooltip>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
    export default {
        name: 'RelationFileTable',
        props: {
            media: {
                type: Array,
                default: () => {
                    return [];
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    .relation-file-table {
        margin-bottom: 10px;
        padding-left: 10px;

        &::before {
            height: 0;
        }

        table {
            width: 100%;
            td, th {
                padding: 4px 0;
            }
        }

        
        .normal {
            text-decoration: none;
            color: var(--primary-color);
        }

        .el-button.el-button--default {
            border: none;
            &:hover {
                background: none;
                border: none;
            }
        }

        
    }
</style>
