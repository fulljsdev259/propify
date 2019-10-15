<template>
    <div class="pinboard">
        <heading :title="$t('models.pinboard.title')" icon="icon-megaphone-1" shadow="heavy">
            <template v-if="$can($permissions.create.pinboard)">
                <el-button @click="add" icon="ti-plus" round size="mini" type="primary">
                    {{$t('models.pinboard.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.pinboard)">
                <el-button :disabled="!selectedItems.length" @click="batchDeleteWithIds" icon="ti-trash" round size="mini"
                           type="danger">
                    {{$t('general.actions.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="formattedItems"
            :loading="{state: loading}"
            :isLoadingFilters="{state: isLoadingFilters}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
        >
        </list-table>
        <el-dialog :title="$t('general.actions.view')" :visible.sync="pinboardDetailsVisible">
            <pinboard-details :pinboard="pinboard" style="margin-bottom: 15px;"></pinboard-details>
            <el-button @click="changePinboardStatus(pinboard.id, pinboardConstants.published)"
                       type="success"
                       v-if="pinboard.status != pinboardConstants.published"
            >
                {{$t('models.pinboard.publish')}}
            </el-button>
            <el-button @click="changePinboardStatus(pinboard.id, pinboardConstants.unpublished)"
                       type="danger"
                       v-else
            >
                {{$t('models.pinboard.unpublish')}}
            </el-button>
        </el-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {mapActions, mapState} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import ListTableMixin from 'mixins/ListTableMixin';
    import PinboardDetails from "components/PinboardDetails";
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';


    const mixin = ListTableMixin({
        actions: {
            get: 'getPinboards',
            delete: 'deletePinboardWithIds'
        },
        getters: {
            items: 'pinboard',
            pagination: 'pinboardMeta'
        }
    });

    export default {
        components: {
            Heading,
            PinboardDetails
        },
        mixins: [mixin, getFilterQuarters],
        data() {
            return {
                header: [{
                    label: 'models.pinboard.preview',
                    prop: 'preview'
                }, {
                    label: 'general.email',
                    prop: 'user.email'
                }, {
                    label: 'models.pinboard.type.label',
                    prop: 'formatted_type_label'
                }, {
                    label: 'models.pinboard.visibility.label',
                    prop: 'formatted_visibility_label'
                }, {
                    label: 'models.pinboard.status.label',
                    prop: 'status',
                    i18nPath: 'models.pinboard.status',
                    class: 'rounded-select',
                    select: {
                        icon: 'ti-pencil',
                        data: [],
                        getter: "pinboard",
                        onChange: this.listingSelectChangedNotify
                    }
                }, {
                    // width: 170,
                    width: 120,
                    actions: [
                        // {
                        //     type: 'primary',
                        //     title: this.$t('general.actions.view'),
                        //     onClick: this.show,
                        //     permissions: [
                        //         this.$permissions.view.pinboard
                        //     ],
                        //     hidden: this.checkPinboardType
                        // }, 
                        {
                            type: '', 
                            title: 'general.actions.edit',
                            editUrl: 'adminPinboardEdit',
                            permissions: [
                                this.$permissions.update.pinboard
                            ],
                            hidden: this.checkPinboardType
                        }
                    ]
                }],
                pinboardDetailsVisible: false,
                quarters:{},
                buildings:{},
                residents:{},
                isLoadingFilters: false,
            };
        },
        computed: {
            ...mapState("application", {
                pinboardConstants(state) {
                    state.constants.pinboard.published = 2;
                    state.constants.pinboard.unpublished = 3;
                    state.constants.pinboard.not_approved = 4;
                    return state.constants.pinboard;
                }
            }),
            formattedItems() {                
                const storeConstants = this.$constants.pinboard;

                return this.items.map((pinboard) => {
                    // pinboard.formatted_status_label = this.$t(`models.pinboard.status.${pinboard.status_label}`);
                    pinboard.formatted_visibility_label = this.$t(`models.pinboard.visibility.${storeConstants.visibility[pinboard.visibility]}`);
                    pinboard.formatted_type_label = this.$t(`models.pinboard.type.${storeConstants.type[pinboard.type]}`);
                    return pinboard;
                });
            },
            filters() {
                return [
                    {
                        name: this.$t('general.placeholders.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    },
                    {
                        name: this.$t('models.pinboard.status.label'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters("status"),
                    },
                    {
                        name: this.$t('models.pinboard.type.label'),
                        type: 'select',
                        key: 'type',
                        data: this.prepareFilters("type"),
                    },
                    {
                        name: this.$t('general.filters.quarters'),
                        type: 'select',
                        key: 'quarter_id',
                        data: this.quarters,
                    },
                    {
                        name: this.$t('general.filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: this.buildings,
                    },
                    {
                        name: this.$t('general.filters.resident'),
                        type: 'remote-select',
                        key: 'resident_id',
                        data: this.residents,
                        remoteLoading: false,
                        fetch: this.fetchRemoteResidents
                    },
                ];
            }
        },
        methods: {
            ...mapActions(['changePinboardPublish', 'updatePinboard', 'getBuildings', 'getResidents']),
            async getFilterBuildings() {
                const buildings = await this.getBuildings({
                    get_all: true
                });

                return buildings.data;
            },
            checkPinboardType(pinboard) {
                return pinboard.type === 2;
            },
            add() {
                this.$router.push({
                    params: {
                        type: 'new'
                    },
                    name: 'adminPinboardAdd'
                });
            },
            show(pinboard) {
                this.pinboard = pinboard;
                this.pinboardDetailsVisible = true;
            },
            changePinboardStatus(id, status) {
                this.changePinboardPublish({id, status}).then((resp) => {
                    this.fetchMore();
                    this.pinboardDetailsVisible = false;
                    displaySuccess(resp);
                }).catch((error) => {
                    displayError(error);
                });
            },
            listingSelectChangedNotify(row) {
                this.$confirm(this.$t(`general.swal.confirmChange.title`), this.$t('general.swal.confirmChange.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirmChange.confirmBtnText`),
                    cancelButtonText: this.$t(`general.swal.confirmChange.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading = true;                    
                        const resp = await this.changePinboardStatus(row.id, row.status);
                        await this.fetchMore();                        
                        displaySuccess(resp);
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading = false;
                    }
                }).catch(async () => {
                    this.loading = true;
                    await this.fetchMore();
                    this.loading = false;
                });
            },
            prepareFilters(property) {
                return Object.keys(this.pinboardConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.pinboard.${property}.${this.pinboardConstants[property][id]}`)
                    };
                });
            },
            async fetchRemoteResidents(search) {
                if(search) {
                    const residents = await this.getResidents({get_all: true, search});

                    return residents.data.map((resident) => {
                        return {
                            name: `${resident.first_name} ${resident.last_name}`,
                            id: resident.id
                        };
                    });
                }
            },
        },
        async created(){
            this.isLoadingFilters = true;
            this.buildings = await this.getFilterBuildings();
            this.residents = this.fetchRemoteResidents();
            const quarters = await this.axios.get('quarters')
            this.quarters = quarters.data.data.data;
            this.isLoadingFilters = false;
        }
    }
</script>
