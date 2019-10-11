<template>
    <div class="quarters-edit">
        <heading :title="$t('models.quarter.add')" icon="icon-share" shadow="heavy">
            <add-actions :saveAction="submit" route="adminQuarters" editRoute="adminQuartersEdit"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :loading="loading" :header="$t('general.actions.view')">                    
                    <el-form :model="model" ref="form">
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('tenant.name')" :rules="validationRules.name"
                                              prop="name">
                                    <el-input type="text" v-model="model.name"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
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
                            </el-col>
                            <el-col :md="12">
                                <el-row :gutter="10">
                                    <el-col :md="8">
                                        <el-form-item :label="$t('general.zip')" :rules="validationRules.zip"
                                                      prop="zip">
                                            <el-input type="text" v-model="model.zip"></el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="16">
                                        <el-form-item :label="$t('general.city')" :rules="validationRules.city"
                                                      prop="city">
                                            <el-input type="text" v-model="model.city"></el-input>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('general.state')"
                                              :rules="validationRules.state_id"
                                              prop="state_id"
                                              class="label-block">
                                    <el-select :placeholder="$t('general.state')" style="display: block"
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


    export default {
        name: 'AdminQuartersEdit',
        mixins: [QuartersMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            AddActions
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getStates());
        },
    };
</script>

<style lang="scss" scoped>

    .quarters-edit {
        .crud-view {
            margin-top: 1%;

            /deep/ .label-block .el-form-item__label {
                display: block;
                float: none;
                text-align: left;
            }
        }
    }
</style>


