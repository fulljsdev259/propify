<template>
    <el-dialog
        :title="$t('general.swal.confirm_change.warning')"
        :visible="centerDialogVisible"
        width="30%"
        :show-close="false"
        center>
        <span>{{ $t('general.edit_close_message') }}</span>
        <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="$emit('clickYes')" class="el-button--transparent">{{ $t('general.yes') }}</el-button>
            <el-button type="warning" @click="$emit('clickNo')" class="el-button--transparent">{{ $t('general.no') }}</el-button>
            <el-button type="info" @click="$emit('clickCancel')" class="el-button--transparent">{{ $t('general.cancel') }}</el-button>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            centerDialogVisible: {
                type: Boolean,
                default: () => false
            }
        }
    }
</script>

<style lang="scss" scoped>
   
</style>
