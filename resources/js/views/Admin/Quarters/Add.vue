<template>
    <div class="quarters-edit" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.quarter.add')" icon="icon-share" shadow="heavy" bgClass="bg-transparent">
            <add-actions :saveAction="submit" route="adminQuarters" editRoute="adminQuartersEdit"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :header="$t('general.box_titles.details')">                    
                    <el-form :model="model" ref="form" class="add-form">
                        <el-row :gutter="20">
                             <el-col :md="12">
                                <el-form-item :label="$t('general.internal_quarter_id')" :rules="validationRules.internal_quarter_id"
                                                prop="internal_quarter_id">
                                    <el-input type="text" v-model="model.internal_quarter_id"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('resident.name')" :rules="validationRules.name"
                                              prop="name">
                                    <el-input type="text" v-model="model.name"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.quarter.url')" :rules="validationRules.url"
                                                prop="url">
                                    <el-input type="text" v-model="model.url"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.quarter.types.label')" :rules="validationRules.types"
                                        class="label-block"
                                        prop="type">
                                    <!-- <el-select
                                            :placeholder="$t('general.placeholders.select')"
                                            style="display: block"
                                            v-model="model.types"
                                            multiple
                                            filterable>
                                        <el-option
                                                :key="type.value"
                                                :label="type.name"
                                                :value="type.value"
                                                v-for="type in types">
                                        </el-option>
                                    </el-select> -->
                                    <multi-select
                                        :name="$t('general.placeholders.select')"
                                        :data="types"
                                        showMultiTag
                                        tagColor="#9E9FA0"
                                        @select-changed="model.types=$event"
                                    ></multi-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                            <!-- <el-col :md="12">
                                <el-form-item class="label-block" :label="$t('models.quarter.count_of_buildings')"
                                              prop="title">
                                    <el-select style="display: block" 
                                            clearable
                                            v-model="model.count_of_buildings">
                                        <el-option
                                                :key="building"
                                                :value="building"
                                                v-for="building in buildingsCount">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col> -->
                        <el-row :gutter="20">
                            <el-col :md="6">
                                <el-form-item :label="$t('general.zip')" :rules="validationRules.zip"
                                                prop="zip">
                                    <el-input type="text" v-model="model.zip"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.city')" :rules="validationRules.city"
                                                prop="city">
                                    <el-input type="text" v-model="model.city"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="6">
                                <el-form-item :label="$t('general.state')"
                                              :rules="validationRules.state_id"
                                              prop="state_id"
                                              class="label-block">
                                    <el-select 
                                        filterable
                                        clearable
                                        :placeholder="$t('general.state')" 
                                        style="display: block"
                                        v-model="model.state_id">
                                        <el-option :key="state.id" :label="state.name" :value="state.id"
                                                   v-for="state in states"></el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                </card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import QuartersMixin from 'mixins/adminQuartersMixin';
    import {displayError} from "helpers/messages";
    import AddActions from 'components/EditViewActions';
    import MultiSelect from 'components/Select';


    export default {
        name: 'AdminQuartersEdit',
        mixins: [QuartersMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            AddActions,
            MultiSelect
        },
    };
</script>

<style lang="scss" scoped>

    .quarters-edit {
        .crud-view {
            margin: 0 10px !important;

            /deep/ .label-block .el-form-item__label {
                display: block;
                float: none;
                text-align: left;
            }
        }
    }
    .el-card .el-card__body {
        padding: 20px !important;
    }
    .el-card .el-card__header {
        padding-left: 20px !important;
        padding-right: 20px !important;
    }
</style>


