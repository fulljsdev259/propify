<template>
    <el-dialog  :close-on-click-modal="true" 
                :title="$t(`general.swal.delete_listing.title`)"
                :visible="deleteModalVisible"
                width="30%"
                class="pinboard-delete-modal"
                :modalAppendToBody="false">
            <el-row>
                <el-col :span="24">
                    <p class="description">{{ $t(`general.swal.delete_listing.text`) }}</p>                    
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24">
                    <el-input autocomplete="off" type="text" v-model="model.message"></el-input>
                </el-col>
            </el-row>

            <span class="dialog-footer" slot="footer">
                <el-button @click="close" size="mini">{{$t('models.building.cancel')}}</el-button>
                <el-button @click="deleteSelectedPinboard()" size="mini" type="danger">{{$t('general.actions.delete')}}</el-button>
            </span>
    </el-dialog>
</template>
<script>
    export default {
        name: 'p-pinboard-delete-modal',
        props: {
            deleteModalVisible: {
                type: Boolean,
                required: true
            },
            delPinboardStatus: {
                type: Number,
                required: true
            },
            deleteSelectedPinboard: {
                type: Function,
                required: true
            },
            closeModal: {
                type: Function,
                required: true
            }
        },
        data() {
            return {
                model: {
                    message: null
                }
            }
        },
        methods: {
            close() {
                this.closeModal();
            }            
        },        
    };
</script>
<style lang="scss">
    .pinboard-delete-modal {

        .el-row {
            margin: 0 0 22px 0;
        }

        .description {
            margin: 0;
        }
        .el-dialog__body {
            word-break: break-word;
        }
    }
</style>
