<template>  
    <div class="services" v-loading.fullscreen.lock="isDownloading">
        <heading :title="$t('models.request.title')" icon="icon-chat-empty" shadow="heavy">
            <template v-if="$can($permissions.create.request)">
                <el-button @click="add" icon="ti-plus" round size="mini" type="primary">
                    {{$t('models.request.add_title')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.request)">
                <el-button :disabled="!selectedItems.length" @click="batchDeleteWithIds" icon="ti-trash" round size="mini"
                           type="danger">
                    {{$t('general.actions.delete')}}
                </el-button>
            </template>
        </heading>
        <request-list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="formattedItems"
            :loading="{state: loading}"
            :isLoadingFilters="{state: isLoadingFilters}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            :withCheckSelection="false"
            @selectionChanged="selectionChanged"
            @pdf-download="downloadPDF($event)"
        >
        </request-list-table>
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterPropertyManager from 'mixins/methods/getFilterPropertyManager';
    import PrepareCategories from 'mixins/methods/prepareCategories';
    import getFilterQuarters from 'mixins/methods/getFilterQuarters';
    import RequestListTable from 'components/RequestListTable';


    const mixin = ListTableMixin({
        actions: {
            get: 'getRequests',
            delete: 'deleteRequestWithIds'
        },
        getters: {
            items: 'requests',
            pagination: 'requestsMeta'
        }
    });

    export default {
        name: 'AdminRequests',
        mixins: [mixin, getFilterPropertyManager, PrepareCategories, getFilterQuarters],
        components: {
            Heading,
            RequestListTable
        },
        data() {
            return {
                header: [{
                    label: '',
                    withOneCol: true,
                    editAction: this.edit,
                    onChange: this.listingSelectChangedNotify,
                    downloadPDF: this.downloadPDF
                }],
                categories:{},
                quarters:{},
                buildings:{},
                propertyManagers:{},
                residents: {},
                services: {},
                isLoadingFilters: false,
                isDownloading: false
            }
        },
        computed: {
            ...mapState("application", {
                requestConstants(state) {
                    return state.constants.requests;
                }
            }),
            formattedItems() {
                return this.items.map((request) => {
                    request.qualification_label = request.qualification_label != "" ? this.$t(`models.request.qualification.${request.qualification_label}`) : "";

                    if(request.contract)
                        request.building = request.contract.building
                    return request;
                });
            },
            routeName() {
                return this.$route.name;
            },
            filters() {
                
                let filters = [
                    {
                        name: this.$t('general.placeholders.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    },
                    {
                        name: this.$t('general.filters.categories'),
                        type: 'select',
                        key: 'category_id',
                        data: this.categories,
                    },
                    {
                        name: this.$t('models.request.status.label'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters("status"),
                    },
                    {
                        name: this.$t('models.request.internal_priority.label'),
                        type: 'select',
                        key: 'internal_priority',
                        data: this.prepareFilters("internal_priority"),
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
                        name: this.$t('general.filters.property_managers'),
                        type: 'select',
                        key: 'property_manager_id',
                        data: this.propertyManagers,
                    },
                    {
                        name: this.$t('general.filters.services'),
                        type: 'select',
                        key: 'service_provider_id',
                        data: this.services,
                    },
                    {
                        name: this.$t('general.filters.resident'),
                        type: 'remote-select',
                        key: 'resident_id',
                        data: this.residents,
                        remoteLoading: false,
                        fetch: this.fetchRemoteResidents
                    },
                    {
                        name: this.$t('general.filters.created_from'),
                        type: 'date',
                        key: 'created_from',
                        format: 'dd.MM.yyyy'
                    },
                    {
                        name: this.$t('general.filters.created_to'),
                        type: 'date',
                        key: 'created_to',
                        format: 'dd.MM.yyyy'
                    },
                    {
                        name: this.$t('models.request.closed_date'),
                        type: 'date',
                        key: 'solved_date',
                        format: 'dd.MM.yyyy'
                    },
                ];
                if(this.routeName == 'adminUnassignedRequests') {
                    filters.splice(6, 2);
                } 
                else if(this.routeName == 'adminAllpendingRequests') {
                    filters.splice(2, 1);
                }
                else if(this.routeName == 'adminMyRequests') {
                    filters.splice(6, 1);
                }
                else if(this.routeName == 'adminMypendingRequests') {
                    filters.splice(6, 1);
                    filters.splice(2, 1);
                }

                return filters;
            }
        },
        methods: {
            ...mapActions(['updateRequest', 'getRequestCategoriesTree', 'getServices', 'getBuildings', 'getResidents', 'downloadRequestPDF']),
            async getFilterBuildings() {
                const buildings = await this.getBuildings({
                    get_all: true
                });

                return buildings.data;
            },
            async getFilterCategories() {
                const {data: categories} = await this.getRequestCategoriesTree({get_all: true});

                this.categories = categories.filter(category => {
                    return category.parent_id !== 1;
                });

                return this.categories;
            },
            async getFilterServices() {
                const services = await this.getServices({get_all: true});

                return services.data;
            },
            async fetchRemoteResidents(search) {
                const residents = await this.getResidents({get_all: true, search});

                return residents.data.map((resident) => {
                    return {
                        name: `${resident.first_name} ${resident.last_name}`,
                        id: resident.id
                    };
                });
            },
            add() {
                this.$router.push({
                    name: 'adminRequestsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id
                    }
                });
            },
            prepareFilters(property) {
                return Object.keys(this.requestConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.request.${property}.${this.requestConstants[property][id]}`)
                    };
                });
            },
            listingSelectChangedNotify(row) {
                this.$confirm(this.$t(`general.swal.confirm_change.title`), this.$t('general.swal.confirm_change.warning'), {
                    confirmButtonText: this.$t(`general.swal.confirm_change.confirm_btn_text`),
                    cancelButtonText: this.$t(`general.swal.confirm_change.cancel_btn_text`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading = true;
                        const resp = await this.updateRequest(row);
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
            async listingSelectChanged(row) {
                try {
                    this.loading = true;
                    const resp = await this.updateRequest(row);
                    await this.fetchMore();
                    displaySuccess(resp);
                } catch (err) {
                    displayError(err);
                } finally {
                    this.loading = false;
                }
            },
            managersMapper(propertyManagers) {
                return propertyManagers.map((propertyManager) => {
                    return {
                        id: propertyManager.user.id,
                        name: propertyManager.user.name
                    }
                })
            },
            async downloadPDF(id) {
                this.isDownloading = true;
                try {
                    const resp = await this.downloadRequestPDF({id: id});
                    if (resp && resp.data) {
                        const url = window.URL.createObjectURL(new Blob([resp.data], {type: resp.headers['content-type']}));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', resp.headers['content-disposition'].split('filename=')[1]);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    }
                } catch (e) {
                    displayError(e)
                } finally {
                    this.isDownloading = false;
                }
            }
        },
        async created(){
            this.isLoadingFilters = true;
            const quarters = await this.axios.get('quarters')
            this.quarters = quarters.data.data.data;

            const propertyManagers = await this.axios.get('propertyManagers?get_all=true')
            this.propertyManagers = propertyManagers.data.data.map((propertyManager) => {
                return {
                    id: propertyManager.id,
                    name: propertyManager.user.name
                }
            });

            const states = await this.axios.get('states?filters=true')
            this.states = states.data.data;

            this.buildings = await this.getFilterBuildings()
            this.categories = await this.getFilterCategories()
            this.services = await this.getFilterServices()
            this.residents = await this.fetchRemoteResidents()

            this.isLoadingFilters = false;

            //const routeName = this.$route.name;
        },
        async mounted() {
        },
    }
</script>
<style>
    .vue-recycle-scroller__item-view {
        min-height: 41px !important;
    }
</style>