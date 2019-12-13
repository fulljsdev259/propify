<template>
    <el-table
            :data="items"
            style="width: 100%"
            v-if="items"
            :show-header="false"
    >
        <el-table-column>
            <template slot-scope="scope">
                <strong>{{scope.row.name}}</strong>
            </template>
        </el-table-column>
        <el-table-column
                width="180"
                align="right"
        >
            <template slot-scope="scope">
                <el-tooltip
                        :content="$t('general.actions.view')"
                        class="item" effect="light" placement="top-end"
                >
                    <el-button icon="el-icon-view" round @click="showPlan(scope.row.media[0].url)" size="mini"/>
                </el-tooltip>
                <el-tooltip
                        :content="$t('general.actions.edit')"
                        class="item" effect="light" placement="top-end"
                >
                    <el-button icon="el-icon-edit" round @click="editPlan(scope.row.id)" size="mini"/>
                </el-tooltip>
                <el-tooltip
                        :content="$t('general.actions.delete')"
                        class="item" effect="light" placement="top-end"
                >
                    <el-button icon="icon-trash-empty" type="danger" round @click="deletePlan(scope.row.id)" size="mini"/>
                </el-tooltip>
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
    export default {
        name: "UnitPlanListTable",
        props: {
            items: {
                type: Array,
                default: () => {
                    return [];
                }
            },
        },
        methods: {
            deletePlan(id) {
                this.$emit('delete-plan', id);
            },
            showPlan(url) {
                this.$emit('show-plan', url);
            },
            editPlan(id) {
                this.$emit('edit-plan', id);
            }
        }
    }
</script>

<style scoped>

</style>
