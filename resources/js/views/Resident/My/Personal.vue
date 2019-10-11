<template>
    <div>
        <placeholder :size="256" :src="require('img/5d0672abb48ed.png')" v-if="!model && !loading.visible">
            {{$t('resident.no_data.personal')}}
            <small>{{$t('resident.no_data_info.personal')}}</small>
        </placeholder>
        <div class="personal" v-else-if="model && !loading.visible">
            <ui-heading icon="ti-book" :title="$t('resident.personal_data')" :description="$t('resident.my_personal_details')">
            </ui-heading>
            <ui-divider />
            <el-card ref="card" v-loading="loading.visible">
                <el-form :label-position="labelPosition" :model="model" label-width="144px" ref="form">
                    <!-- <el-form-item :label="$t('resident.title')" prop="title">
                        <el-select placeholder="Select title" v-model="model.title">
                            <el-option v-for="title in $constants.residents.title" :key="title" :label="$t(`general.salutation_option.${title}`)" :value="title" />
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="$t('resident.company_name')" prop="company" v-if="model.title === 'company'">
                        <el-input type="text" v-model="model.company" />
                    </el-form-item>
                    <el-form-item :label="$t('resident.first_name')" prop="first_name">
                        <el-input type="text" v-model="model.first_name"/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.last_name')" prop="last_name">
                        <el-input type="text" v-model="model.last_name"/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.birth_date')" prop="birth_date">
                        <el-date-picker format="dd.MM.yyyy" type="date" v-model="model.birth_date" value-format="yyyy-MM-dd" />
                    </el-form-item> -->
                    <el-form-item :label="$t('resident.title')" prop="title">
                        <el-input type="text" :value="$t(`resident.salutation_option.${model.title}`)" disabled/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.company_name')" prop="company" v-if="model.title === 'company'">
                        <el-input type="text" v-model="model.company" disabled/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.first_name')" prop="first_name">
                        <el-input type="text" v-model="model.first_name" disabled/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.last_name')" prop="last_name">
                        <el-input type="text" v-model="model.last_name" disabled/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.birth_date')" prop="birth_date">
                        <el-input type="text" :value="model.birth_date_formatted" disabled/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.mobile_phone')" prop="mobile_phone">
                        <el-input type="text" v-model="model.mobile_phone"/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.work_phone')" prop="work_phone">
                        <el-input type="text" v-model="model.work_phone"/>
                    </el-form-item>
                    <el-form-item :label="$t('resident.personal_phone')" prop="private_phone">
                        <el-input type="text" v-model="model.private_phone"/>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" icon="ti-save" :disabled="loading.visible" @click="submit">
                            {{$t('resident.actions.save')}}
                        </el-button>
                    </el-form-item>
                </el-form>
            </el-card>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading'
    import Placeholder from 'components/Placeholder'
    import {displayError, displaySuccess} from 'helpers/messages'
    import {ResponsiveMixin} from 'vue-responsive-components'

    export default {
        mixins: [
            ResponsiveMixin
        ],
        components: {
            Heading,
            Placeholder
        },
        data () {
            return {
                model: null,
                loading: {
                    visible: true
                },
                labelPosition: 'left',
                validationRules: {
                    first_name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.first_name')})
                    }],
                    last_name: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.last_name')})
                    }],
                    birth_date: [{
                        required: true,
                        message: this.$t('validation.required',{attribute: this.$t('models.resident.birth_date')})
                    }]
                }
            }
        },
        methods: {
            submit () {
                this.$refs.form.validate(async valid => {
                    if (!valid) {
                        return false
                    }

                    this.loading.visible = true

                    try {
                        displaySuccess(await this.$store.dispatch('updateMyTenancy', this.model))
                    } catch (error) {
                        displayError(error)
                    } finally {
                        this.loading.visible = false
                    }
                })
            }
        },
        computed: {
            breakpoints () {
                return {
                    sm: el => {
                        if (el.width <= 640) {                            
                            this.labelPosition = 'top'

                            return true
                        } else {                            
                            this.labelPosition = 'left'

                            return false
                        }
                    }
                }
            }
        },
        async mounted () {
            this.loading = this.$loading({
                target: this.$el.parentElement,
                text: this.$t('resident.fetching_message.personal_data')
            });

            try {
                const {data: {
                    title,
                    company,
                    first_name,
                    last_name,
                    birth_date_formatted,
                    mobile_phone,
                    work_phone,
                    private_phone
                }} = await this.$store.dispatch('myTenancy');

                this.model = {
                    title,
                    company,
                    first_name,
                    last_name,
                    birth_date_formatted,                    
                    mobile_phone,
                    work_phone,
                    private_phone
                }
            } catch (error) {
                displayError(error)
            } finally {
                this.loading.close()
            }
        }
    }
</script>

<style lang="scss" scoped>
    .personal {
        &:not(.empty):before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/5d0672abb48ed.png');
            background-repeat: no-repeat;
             background-attachment: fixed;
            background-position: 4em -8em;
            width: 100%;
            height: 100%;
            opacity: .16;
            pointer-events: none;
        }

        .heading {
            margin-bottom: 24px;
            
            .description {
                color: darken(#fff, 40%);
            }
        }

        .placeholder {
            font-size: 20px;
            color: var(--color-main-background-darker);

            small {
                font-size: 72%;
                color: var(--primary-color-lighter);
            }
        }

        .el-card {
            background-color: transparentize(#fff, .2);
            position: relative;
            max-width: 640px;

            .el-form {
                .el-form-item {
                    .el-button,
                    .el-select,
                    .el-date-editor {
                        width: 100%;
                    }
                    
                    :global(.el-input__inner),
                    :global(.el-textarea__inner) {
                        
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 676px) {
        .personal {
            /deep/ .ui-heading__content__description {
                display: none
            }
        }
    }
</style>
