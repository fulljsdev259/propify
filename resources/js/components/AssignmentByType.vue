<template>
    <el-row :gutter="20" id="assignment_type">        
        <el-col id="type" v-if="(assignmentTypes.length > 1)">
            <el-select
                clearable 
                @change="resetToAssignList"
                class="custom-select"
                :value="assignmentType" @input="$emit('update:assignmentType', $event)"
            >
                <el-option
                    :key="type"
                    :label="$t(`general.assignment_types.${type}`)"
                    :value="type"
                    v-for="(type) in assignmentTypes">
                </el-option>
            </el-select>
        </el-col>
        <el-col id="search">
            <el-select
                clearable
                :loading="remoteLoading"
                :placeholder="$t('general.placeholders.search')"
                :remote-method="remoteSearch"
                class="custom-remote-select"
                filterable
                remote
                reserve-keyword
                style="width: 100%;"
                :value="toAssign" @input="$emit('update:toAssign', $event)"
            >
                <div class="custom-prefix-wrapper" slot="prefix">
                    <i class="el-icon-search custom-icon"></i>
                </div>
                <el-option
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                    v-for="item in toAssignList"/>
            </el-select>
        </el-col>
        <el-col id="assignBtn" :style="innerBtnWidth">
            <el-button :disabled="!toAssign" @click="assign(toAssign)" type="primary">
                <div id="innerBtn" ref="innerBtn">
                    <i class="ti-save"></i>
                    <span>&nbsp;{{$t('general.assign')}}</span>
                </div>
            </el-button>
        </el-col>
    </el-row>
</template>
<script>
    export default {
        props: {
            resetToAssignList: { 
                type: Function 
            },
            assignmentType: {
                required: true
            },
            assignmentTypes: {
                type: Array
            },
            toAssign: {
                required: true
            },
            assign: { 
                type: Function 
            },
            toAssignList: {
                type: Array
            },
            remoteLoading: {
                type: Boolean
            },
            remoteSearch: {
                type: Function 
            }
        },
        data() {
            return {
                innerBtnWidth: null,
            }
        },
        computed: {
            BtnWidth() {
                return this.innerBtnWidth;
            }
        },
        methods: {
            getBtnWidth() {
                this.innerBtnWidth = this.$refs.innerBtn.clientWidth;
            }
        },
        mounted() {
            this.getBtnWidth();
        },
        created() {            
            this.resetToAssignList();
            this.assign();
        }
    }
</script>
<style lang="less" scoped>
    #assignment_type {
        display: flex;
        #type {
            .custom-select {
                width: 100%;
            }
        }
        #assignBtn {
            flex: 1;
        }
    }
</style>