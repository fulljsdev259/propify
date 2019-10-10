<template>
    <el-dialog  :close-on-click-modal="true" 
                :title="statusChangeModalType=='done' ? $t(`general.swal.to_done.title`) : $t(`general.swal.reactivate.title`)"
                :visible="statusChangeModalVisible"
                width="40%"
                class="request-status-change-modal"
                @close="closeModal()"
                :modalAppendToBody="false">
        <el-form ref="form" :model="model" label-position="top">
            <el-row>
                <el-col :span="24" v-if="statusChangeModalType=='done'">
                    <p class="description">{{ $t(`general.swal.to_done.desc`) }}</p>
                    <br/>
                    <p class="description">{{ $t(`general.swal.to_done.message`) }}</p>
                </el-col>
                <el-col :span="24" v-if="statusChangeModalType=='reactivate'">
                    <p class="description">{{ $t(`general.swal.reactivate.text`) }}</p>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24">
                    <el-form-item prop="message">
                        <el-input type="textarea" :autosize="{ minRows: 3}" v-model="model.message" ></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
            <span class="dialog-footer" slot="footer">
                <el-button @click="close" size="mini">{{$t('resident.cancel')}}</el-button>
                <el-button v-if="statusChangeModalType=='done'" @click="changeStatus(statusChangeModalType, model.message)" size="mini" type="primary">{{$t('resident.close_request')}}</el-button>
                <el-button v-else :disabled="model.message == null || model.message == ''" @click="changeStatus(statusChangeModalType, model.message)" size="mini" type="primary">{{$t('resident.actions.to_reactivated')}}</el-button>
            </span>
        
    </el-dialog>
</template>
<script>
    export default {
        name: 'p-request-status-change-modal',
        props: {
            statusChangeModalVisible: {
                type: Boolean,
                required: true
            },
            statusChangeModalType: {
                type: String,
                required: true
            },
            changeStatus: {
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
                this.model.message = null;
                this.closeModal();
            }
        },
        watch: {
            'statusChangeModalVisible': {
                immediate: false,
                handler (state) {
                    // TODO - auto blur container if visible is true first
                    if (!state) {
                        this.model.message = null
                    }
                }
            },
        },
    };
</script>
<style lang="scss">
    .request-status-change-modal {        

        .el-row {
            margin: 0 0 22px 0;
        }

        .el-row:last-child {
            margin-bottom: 0;
        }

        .description {
            margin: 0;
        }

        .el-dialog__header {
            border-bottom: 1px solid lightgrey;
        }

        .el-dialog__body {
            word-break: break-word;
            padding-bottom: 0;
        }
    }
</style>
