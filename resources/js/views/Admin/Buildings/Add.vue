<template>
    <div class="buildings-add" v-loading.fullscreen.lock="loading.state">
        <heading :title="$t('models.building.add')" icon="icon-commerical-building" shadow="heavy" bgClass="bg-transparent">
            <add-actions :saveAction="submit" route="adminBuildings" editRoute="adminBuildingsEdit"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" ref="form" class="add-form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <card :header="$t('models.property_manager.details_card')">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.building.quarter')"  :rules="validationRules.quarter_id" prop="quarter_id">
                                        <el-select
                                                :loading="remoteLoading"
                                                :placeholder="$t('general.placeholders.search')"
                                                :remote-method="remoteSearchQuarters"
                                                filterable
                                                remote
                                                reserve-keyword
                                                style="width: 100%;"
                                                v-model="model.quarter_id">
                                            <el-option
                                                    :key="quarter.id"
                                                    :label="quarter.name"
                                                    :value="quarter.id"
                                                    v-for="quarter in quarters"/>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                
                                <el-col :md="8">
                                    <el-form-item :label="$t('general.street')" :rules="validationRules.street" prop="street">
                                        <el-input type="text" v-model="model.street" v-on:change="setBuildingName"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="4">
                                    <el-form-item :label="$t('general.house_num')" :rules="validationRules.house_num"
                                                  prop="house_num">
                                        <el-input type="text" v-model="model.house_num" v-on:change="setBuildingName"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="4">
                                    <el-form-item :label="$t('general.zip')" :rules="validationRules.zip" prop="zip">
                                        <el-input type="text" v-model="model.zip"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="8">
                                    <el-form-item :label="$t('general.city')" :rules="validationRules.city" prop="city">
                                        <el-input type="text" v-model="model.city"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('general.state')"
                                                  class="label-block"
                                                  :rules="validationRules.state_id"
                                                  prop="state_id">
                                        <el-select  
                                            clearable
                                            filterable
                                            :placeholder="$t('general.state')" 
                                            style="display: block"
                                            v-model="model.state_id"
                                        >
                                            <el-option :key="state.id" :label="state.name" :value="state.id"
                                                       v-for="state in states"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="6">
                                    <el-form-item :label="$t('models.building.building_number')"
                                                  :rules="validationRules.internal_building_id"
                                                  prop="internal_building_id" style="max-width: 512px;">
                                        <el-input type="text" v-model="model.internal_building_id"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="6">
                                    <el-form-item :label="$t('models.building.type')"
                                                  class="label-block"
                                                  :rules="validationRules.types"
                                                  prop="types">
                                        <multi-select
                                            :name="$t('models.building.type')"
                                            :data="types"
                                            tagColor="#9E9FA0"
                                            showMultiTag
                                            @select-changed="model.types=$event"
                                        ></multi-select>
                                    </el-form-item>
                                </el-col>
                                <!-- <el-col :md="10">
                                    <el-form-item :label="$t('general.name')" :rules="validationRules.name" prop="name"
                                                  ref="name">
                                        <el-input type="text" v-model="model.name"></el-input>
                                    </el-form-item>
                                </el-col> -->
                            </el-row>
                            
                        </card>
                    </el-col>
                </el-row>

                <el-row :gutter="20" class="mt40">
                    <el-col :md="12">
                        <card :header="$t('models.unit.floor')">
                    <!--<el-form-item prop="description" :label="$t('general.description')" :rules="validationRules.description" style="max-width: 512px;">-->
                    <!--<el-input type="textarea" v-model="model.description"></el-input>-->
                    <!--</el-form-item>-->
                            <el-row type="flex" :gutter="20">
                                <el-col :span="12">
                                    <el-form-item :label="$t('models.building.floor_nr')"
                                                  style="margin-bottom: 10px;"
                                                  :rules="validationRules.floor_nr"
                                                  prop="floor_nr">
                                        <el-input type="number"
                                                  :min="0"
                                                  :max="30"
                                                  v-model.number="model.floor_nr"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <div class="switch-wrapper" style="margin-bottom: 10px;">
                                        <el-form-item :label="$t('models.unit.auto_create_question')" v-if="model.floor_nr > 0">
                                            <el-switch v-model="unitAutoCreate"/>
                                        </el-form-item>
                                    </div>
                                </el-col>
                            </el-row>
                            <div class="switch-wrapper">
                                <div class="switcher__desc">
                                    {{ $t('models.unit.auto_create_description') }}
                                </div>
                            </div>
                            <el-row :gutter="20">
                                <el-col :span="12"
                                        :key="item"
                                        v-for="item in floors">
                                    <el-form-item :label="item === 0
                                                            ? $t('models.building.ground_floor')
                                                            : `${ordinalSuffixFloor(item)} ${$t('models.unit.floor')}`"
                                                  :rules="validationRules.floor"
                                                  :prop="'floor.'+ item"
                                                  v-if="unitAutoCreate">
                                        <el-input type="number"
                                                  :min="0"
                                                  v-model.number="model.floor[item]"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>

                            <el-row type="flex" :gutter="20">
                                <el-col :span="12">
                                    <el-form-item :rules="validationRules.attic">
                                        <label class="attic-label">{{ $t('models.unit.attic') }}</label>
                                        <el-switch v-model="model.attic"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <el-form-item :label="$t('models.building.under_floor')"
                                                  :rules="validationRules.under_floor"
                                                  prop="under_floor">
                                        <el-input type="number"
                                                  :min="0"
                                                  :max="3"
                                                  v-model.number="model.under_floor"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </card>
                    </el-col>
                </el-row>
            </el-form>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import BuildingsMixin from 'mixins/adminBuildingsMixin';
    import AddActions from 'components/EditViewActions';
    import MultiSelect from 'components/Select';

    export default {
        mixins: [BuildingsMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            AddActions,
            MultiSelect,
        },
        data() {
            return {
                types: [],
            }
        },
        methods: {
            setBuildingName() {
                this.model.name = this.model.street + ' ' + this.model.house_num;
            },
            ordinalSuffixFloor(i) {
                let b = i % 10;
                return i + ((~~ (i % 100 / 10) === 1) ? this.$t('general.ordinal_endings.th') :
                    (b === 1) ? this.$t('general.ordinal_endings.st') :
                        (b === 2) ? this.$t('general.ordinal_endings.nd') :
                            (b === 3) ? this.$t('general.ordinal_endings.rd') : this.$t('general.ordinal_endings.th'));
            },
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getStates());
        },
        computed: {
            floors() {
                let arr = [];

                for (let i = 0; i <= this.model.floor_nr-1; i++) {
                    arr.push(i);
                }

                if (this.model.floor_nr !== '' &&
                    this.model.floor_nr < this.model.floor.length
                ) {
                    this.model.floor = this.model.floor.splice(0, this.model.floor_nr);
                }

                if (!this.unitAutoCreate) {
                    this.model.floor = [];
                }

                return arr;
            }
        },
        async created() {
            this.getTypes();
        }
    }
</script>
<style lang="scss">
    .label-block .el-form-item__label {
        display: block;
        float: none;
        text-align: left;
    }

    .switcher__desc {
        margin-top: 0.5em;
        display: block;
        font-size: 0.9em;
    }
</style>

<style lang="scss" scoped>
    .crud-view {
        margin: 0 20px !important;
    }
    
    .el-card .el-card__body {
        padding: 20px !important;
    }
    .el-card .el-card__header {
        padding-left: 20px !important;
        padding-right: 20px !important;
    }
</style>
