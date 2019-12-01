<template>
    <el-row id="managerAssignBox">
        <el-col id="managerSelect">
            <el-select
                clearable
                :loading="remoteLoading"
                :placeholder="$t('general.placeholders.search')"
                :remote-method="remoteSearch"
                class="custom-remote-select"
                filterable
                remote
                multiple
                reserve-keyword
                style="width: 100%;"
                :value="toAssign" @input="$emit('update:toAssign', $event)"
            >
                <div class="custom-prefix-wrapper" slot="prefix">
                    <i class="el-icon-search custom-icon"></i>
                </div>
                <el-option
                        :key="assignee.id"
                        :label="assignee.name"
                        :value="assignee.id"
                        v-for="assignee in toAssignList">
                    <span style="float: left">{{ assignee.name }}</span>
                    <span style="float: right; color: #8492a6; font-size: 13px">
                        {{assignee.roles[0].name == "provider" ? $t(`models.service.category.${assignee.function}`)  : ''}}
                        {{assignee.roles[0].name != "provider" ? $t(`general.roles.${assignee.function}`) : ''}} 
                    </span>
                </el-option>
            </el-select>
        </el-col>
        <el-col id="managerAssignBtn">
            <el-button :disabled="!toAssign.length" @click="assign" class="full-button el-button--assign"
                        icon="ti-save">
                &nbsp;{{$t('general.assign')}}
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
        }
    }
</script>
<style lang="less" scoped>
    #managerAssignBox {
        display: flex;
        margin-bottom: 20px;

        #managerSelect {
            width: 100%;
            margin-right: 20px;
        }

        #managerAssignBtn {
            flex: 1;
        }
    }
</style>
