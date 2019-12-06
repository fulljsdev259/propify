<template>  
    <div class="services list-view" v-loading.fullscreen.lock="isDownloading">
        <heading :title="$t('models.request.title')" icon="icon-chat-empty" shadow="heavy" :searchBar="true" @search-change="search=$event">
            <template v-if="$can($permissions.create.request)">
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini"
                    class="el-button--transparent"
                >
                    {{$t('models.request.add_title')}}
                </el-button>
            </template>
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event" @order-changed="header=$event"></list-field-filter>
            </template>
            <template>
                <el-dropdown placement="bottom" trigger="click" @command="handleMenuClick">
                    <el-button size="mini" class="el-button--transparent more-actions">
                        <i class="el-icon-more" style="transform: rotate(90deg)"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item
                            command="save_filter"
                            icon="el-icon-plus"
                        >
                            {{ $t('models.request.save_filter') }}
                        </el-dropdown-item>
                        <el-dropdown-item 
                            :disabled="!selectedItems.length" 
                            :command="option.key"
                            :key="option.key"
                            :icon="option.icon"
                            v-for="option in massEditOptions">
                            {{$t('models.request.mass_edit.options.' + option.key)}}
                        </el-dropdown-item>
                        <el-dropdown-item
                                v-if="$can($permissions.delete.request)"
                                :disabled="!selectedItems.length"
                                icon="ti-trash"
                                command="delete"
                        >
                            {{$t('general.actions.delete')}}
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </template>

        </heading>
        <!-- <request-list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="formattedItems"
            :loading="{state: loading}"
            :isLoadingFilters="{state: isLoadingFilters}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            :searchText="search"
            :withCheckSelection="false"
            @selectionChanged="selectionChanged"
            @pdf-download="downloadPDF($event)"
        >
        </request-list-table> -->
        <list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="headerFilter"
            :items="formattedItems"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            :searchText="search"
            @selectionChanged="selectionChanged"
            @update-row="updateRow"
        >
        </list-table>
        <el-dialog :close-on-click-modal="false" :title="$t('models.building.assign_managers')"
                   :visible.sync="batchEditVisible"
                   v-loading="processAssignment" width="30%">
            <span slot="title">
                <template v-for="option in massEditOptions">
                    <span :key="option.key" v-if="activeMassEditOption == option.key">
                        {{$t('models.request.mass_edit.' + option.key + '.modal.heading_title')}}
                    </span>
                </template>
            </span>
            <el-form :model="servicesForm"  v-if="activeMassEditOption == 'service_provider'">
                <el-form-item :label="$t('models.request.mass_edit.service_provider.modal.content_label')">
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('general.placeholders.search')"
                        :remote-method="remoteSearchPartners"
                        class="custom-remote-select"
                        filterable
                        multiple
                        remote
                        reserve-keyword
                        style="width: 100%;"
                        v-model="toAssign"
                    >
                        <div class="custom-prefix-wrapper" slot="prefix">
                            <i class="el-icon-search custom-icon"></i>
                        </div>
                        <el-option
                            :key="service.id"
                            :label="`${service.first_name} ${service.last_name}`"
                            :value="service.id"
                            v-for="service in toAssignList"/>
                    </el-select>
                </el-form-item>
                <div class="switch-wrapper">
                    <el-form-item :label="$t('models.request.mass_edit.service_provider.modal.switcher_label')">
                        <el-switch v-model="send_email_service_provider"/>
                    </el-form-item>
                    <div class="switcher__desc">
                        {{ $t('models.request.mass_edit.service_provider.modal.switcher_desc') }}
                    </div>
                </div>
            </el-form>

            <el-form :model="managersForm" v-else-if="activeMassEditOption == 'property_manager'">
                <el-form-item :label="$t('models.request.mass_edit.property_manager.modal.content_label')">
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('general.placeholders.search')"
                        :remote-method="remoteSearchManagers"
                        class="custom-remote-select"
                        filterable
                        multiple
                        remote
                        reserve-keyword
                        id="manager-select"
                        style="width: 100%;"
                        v-model="toAssign"
                    >
                        <div class="custom-prefix-wrapper" slot="prefix">
                            <i class="el-icon-search custom-icon"></i>
                        </div>
                        <el-option
                            :key="manager.id"
                            :label="`${manager.first_name} ${manager.last_name}`"
                            :value="manager.id"
                            v-for="manager in toAssignList"/>
                    </el-select>
                </el-form-item>
            </el-form>
            
            <el-form :model="statusForm" v-else-if="activeMassEditOption == 'change_status'">
                <el-form-item :label="$t('models.request.mass_edit.change_status.modal.content_label')">
                    <el-select :placeholder="$t('general.placeholders.select')"
                            class="custom-select"
                            v-model="massStatus">
                        <el-option
                            :key="k"
                            :label="$t(`models.request.status.${status}`)"
                            :value="parseInt(k)"
                            v-for="(status, k) in $constants.requests.status">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <span class="dialog-footer" slot="footer">
                <el-button @click="closeModal" size="mini">{{$t('general.actions.close')}}</el-button>
                <template
                        v-for="option in massEditOptions">
                    <el-button 
                            v-if="activeMassEditOption == option.key" 
                            @click="massEditAction(option.key)" 
                            size="mini" 
                            type="primary"
                            :key="option.key"
                            >
                        {{$t('models.request.mass_edit.' + option.key + '.modal.footer_button')}}
                    </el-button>
                </template>
            </span>
        </el-dialog>
        <el-dialog
            :visible.sync="visibleSaveDialog"
            width="30%"
            center
        >
            <h4 class="filter-title">{{ $t('validation.attributes.title') }}</h4>
            <el-input v-model="saveTitle"/>
            <span slot="footer" class="dialog-footer">
                <el-button type="danger" @click="visibleSaveDialog = false">{{ $t('general.cancel') }}</el-button>
                <el-button type="primary" @click="saveFilter" :disabled="saveTitle===''">{{ $t('general.actions.save') }}</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import {mapActions, mapState, mapGetters} from 'vuex';
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
            pagination: 'requestsMeta',
            user: ''
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
                // header: [{
                //     label: '',
                //     withOneCol: true,
                //     editAction: this.edit,
                //     onChange: this.listingSelectChangedNotify,
                //     downloadPDF: this.downloadPDF
                // }],
                header: [ 
                    {
                        label: 'general.id',
                        withReuqestIDAndTitle: true
                    },
                    {
                        label: 'general.filters.status',
                        withRequestStatusSign: true,
                        prop: 'status',
                        width: 150
                    }, {
                        label: 'models.request.assigned_property_managers',
                        withRequestUsers: true,
                        prop: 'property_managers'
                    }, {
                        label: 'general.category',
                        prop: 'category',
                        i18n: this.translateCategory
                    }, {
                        label: 'general.filters.services',
                        withRequestUsers: true,
                        prop: 'service_providers'
                    }, {
                        label: 'models.request.created_by',
                        prop: 'creator.name',
                        withRequestCreator: true,
                        width: 200
                    }, {
                        label: 'models.request.visible',
                        withRequestVisible: true,
                        width: 85,
                        align: 'center',
                        prop: 'visibility'
                    }, {
                        label: 'models.unit.appendix',
                        withRequestAppendix: true,
                        width: 120,
                        align: 'center',
                        prop: 'appendix'
                    }
                ],
                search: '',
                categories:[],
                quarters:[],
                buildings:[],
                propertyManagers:[],
                residents: [],
                services: [],
                creators: [],
                isLoadingFilters: false,
                isDownloading: false,
                batchEditVisible: false,
                processAssignment: false,
                toAssignList: '',
                toAssign: [],
                saveTitle: '',
                visibleSaveDialog: false,
                send_email_service_provider: true,
                remoteLoading: false,
                managersForm: {},
                servicesForm: {},
                statusForm: {},
                activeMassEditOption: 'service_provider',
                massStatus: '',
                massEditOptions : [
                    {
                        key : 'service_provider',
                        icon : 'icon-tools'
                    },
                    {
                        key : 'property_manager',
                        icon : 'icon-users'
                    },
                    {
                        key : 'change_status',
                        icon : 'icon-publish'
                    }
                ]
            }
        },
        computed: {
            ...mapGetters({
                user: 'loggedInUser',
            }),
            ...mapState("application", {
                requestConstants(state) {
                    return state.constants.requests;
                }
            }),
            formattedItems() {
                return this.items.map((request) => {
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
                        name: this.$t('models.request.status.label'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters("status"),
                    },
                    {
                        name: this.$t('general.filters.property_managers'),
                        type: 'select',
                        key: 'property_manager_id',
                        data: this.propertyManagers,
                    },
                    {
                        name: this.$t('general.filters.categories'),
                        type: 'select',
                        key: 'category_id',
                        data: this.categories,
                    },
                    {
                        name: this.$t('general.filters.services'),
                        type: 'select',
                        key: 'service_provider_id',
                        data: this.services,
                    },
                    {
                        name: this.$t('models.request.created_by'),
                        type: 'select',
                        key: 'creator_user_id',
                        data: this.creators,
                    },
                    // {
                    //     name: this.$t('models.request.internal_priority.label'),
                    //     type: 'select',
                    //     key: 'internal_priority',
                    //     data: this.prepareFilters("internal_priority"),
                    // },
                    // {
                    //     name: this.$t('general.filters.resident'),
                    //     type: 'select',
                    //     key: 'resident_id',
                    //     data: this.residents,
                    // },
                    {
                        name_from: this.$t('general.filters.created_from'),
                        name_to: this.$t('models.request.closed_date'),
                        type: 'daterange',
                        key_from: 'created_from',
                        key_to: 'solved_date',
                        format: 'dd.MM.yyyy'
                    },
                    // {
                    //     name: this.$t('general.filters.created_from'),
                    //     type: 'date',
                    //     key: 'created_from',
                    //     format: 'dd.MM.yyyy'
                    // },
                    // {
                    //     name: this.$t('general.filters.created_to'),
                    //     type: 'date',
                    //     key: 'created_to',
                    //     format: 'dd.MM.yyyy'
                    // },
                    // {
                    //     name: this.$t('models.request.closed_date'),
                    //     type: 'date',
                    //     key: 'solved_date',
                    //     format: 'dd.MM.yyyy'
                    // },
                    {
                        name: this.$t('general.filters.phase'),
                        type: 'select',
                        key: 'phase_id',
                        data: this.prepareFilters("capture_phase"),
                    },
                    {
                        name: this.$t('general.filters.more_filters'),
                        type: 'toggle',
                        key: 'saved_filter',
                        data: []
                    }
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
            ...mapActions(['updateRequest', 
                'getQuarters', 
                'getServices', 
                'getBuildings', 
                'getResidents', 
                'downloadRequestPDF', 
                'getUsers', 
                'getPropertyManagers',
                'massEdit']),
            handleMenuClick(command) {
                if(command == 'delete')
                    this.batchDeleteWithIds();
                else if(command == 'save_filter')
                    this.visibleSaveDialog = true;
                else  {
                    this.massStatus = ''
                    this.resetToAssignList();
                    this.activeMassEditOption = command
                    this.batchEditVisible = true
                }
            },
            saveFilter() {
                this.visibleSaveDialog = false;
                let fields_data = {};
                this.header.forEach((item) => {
                    fields_data[item.label] = !this.fields.includes(item.label);
                });
                this.axios.post('/userFilters', {
                    user_id: this.user.id,
                    title: this.saveTitle,
                    menu: this.$route.name,
                    options_url: this.$route.fullPath,
                    fields_data: fields_data,
                });
            },
            translateCategory(category) {
                return this.$t(`models.request.category_list.${category.name}`)
            },
            async getFilterBuildings() {
                let buildings = await this.getBuildings({
                    get_all: true
                });
                buildings.data.map(building => {
                    building.name = building.address ? building.address.street + ' ' + building.address.house_num : ''
                })
                return buildings.data;
            },
            async getFilterCategories() {
                this.categories = this.$constants.requests.categories_data.tree

                return this.categories;
            },
            async getFilterServices() {
                let services = await this.getServices({get_all: true});
                services.data.map(service => service.name = service.first_name + ' ' + service.last_name)
                return services.data;
            },
            async fetchRemoteQuarters(search = '') {
                const quarters = await this.getQuarters({get_all: true, search});

                return quarters.data
            },
            async fetchRemoteBuildings(search = '') {
                const buildings = await this.getBuildings({get_all: true, search});
                buildings.data.map(building => {
                    building.name = building.address ? building.address.street + ' ' + building.address.house_num : ''
                })
                return buildings.data
            },
            async fetchRemoteManagers(search = '') {
                const managers = await this.getPropertyManagers({get_all: true, search});

                return managers.data.map(manager => {
                    return {
                        id: manager.id,
                        name: manager.user.name
                    }
                });
            },
            async fetchRemoteServices(search = '') {
                let services = await this.getServices({get_all: true, search});
                services.data.map(service => service.name = service.first_name + ' ' + service.last_name)

                return services.data;
            },
            async fetchRemoteCreators(search = '') {
                let creators = await this.getUsers({get_all: true, exclude_roles: ['provider']});

                return creators.data;
            },
            async fetchRemoteResidents(search = '') {
                const residents = await this.getResidents({get_all: true, search});

                return residents.data.map((resident) => {
                    return {
                        name: `${resident.first_name} ${resident.last_name}`,
                        id: resident.id
                    };
                });
            },
            updateRow(index, data) {
                // this.$set(this.items, index, data)
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
            },
            batchEdit() {
                this.batchEditVisible = true;
            },
            closeModal() {
                this.batchEditVisible = false;
                this.toAssign = [];
                this.toAssignList = [];
                this.massStatus = ''
            },
            async massEditAction( command ) {
                if(command == 'service_provider') {
                    this.massAssignProviders()
                }
                else if(command == 'property_manager') {
                    this.massAssignManagers()
                }
                else if(command == 'change_status') {
                    this.massChangeStatus()
                }
            },
            async massAssignProviders() {
                let request_ids = this.selectedItems.map(request => request.id)
                let service_provider_ids = this.toAssign
                let send_email_service_provider = this.send_email_service_provider
                const resp = await this.massEdit({
                    request_ids, 
                    service_provider_ids,
                    send_email_service_provider
                })

                this.processAssignment = false;
                this.closeModal();
                this.fetchMore();
                if(resp.data.success)
                    displaySuccess(resp.data);

            },
            async massAssignManagers() {
                let request_ids = this.selectedItems.map(request => request.id)
                let property_manager_ids = this.toAssign
                
                const resp = await this.massEdit({
                    request_ids, 
                    property_manager_ids
                })

                this.processAssignment = false;
                this.closeModal();
                this.fetchMore();
                if(resp.data.success)
                    displaySuccess(resp.data);

            },
            async massChangeStatus() {
                let request_ids = this.selectedItems.map(request => request.id)
                let status = this.massStatus
                
                const resp = await this.massEdit({
                    request_ids, 
                    status
                })

                this.processAssignment = false;
                this.closeModal();
                this.fetchMore();
                if(resp.data.success)
                    displaySuccess(resp.data);
                
            },
            async remoteSearchManagers(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getPropertyManagers({
                            get_all: true,
                            search
                        });

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async remoteSearchPartners(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getServices({
                            get_all: true,
                            search
                        });

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = [];
            },
            disableMassEditButton(flag) {
                let element = document.getElementsByClassName("mass-edit-dropdown");
                if(element.length == 0)
                    return
                let buttons = element[0].getElementsByTagName("button");
                for(let i = 0; i < 2; i ++) {
                    if(flag) {
                        buttons[i].disabled = true;
                        buttons[i].classList.add("is-disabled");
                        element[0].classList.add("is-disabled");
                    }
                    else {
                        buttons[i].disabled = false;
                        buttons[i].classList.remove("is-disabled");
                        element[0].classList.remove("is-disabled");
                    }
                }
            }
        },
        async mounted(){
            this.disableMassEditButton(true)

            this.isLoadingFilters = true;
            
            const states = await this.axios.get('states?filters=true')
            this.states = states.data.data;

            this.categories = await this.getFilterCategories();
            this.isLoadingFilters = false;
            
            this.quarters = await this.fetchRemoteQuarters();
            this.buildings = await this.fetchRemoteBuildings();
            this.propertyManagers = await this.fetchRemoteManagers();
            this.services = await this.fetchRemoteServices();
            this.residents = await this.fetchRemoteResidents();
            this.creators = await this.fetchRemoteCreators();
            
        },  
        watch: {
            selectedItems: function(items) {   
                this.disableMassEditButton(items.length == 0)
            },
        },
    }
</script>
<style>
    .vue-recycle-scroller__item-view {
        min-height: 41px !important;
    }
</style>

<style lang="scss" scoped>

    .el-dropdown.round {
        border-radius: 20px;
        margin-left: 20px;
        margin-right: 20px;
        /deep/ .el-button-group {
            border-radius: 20px;

            .el-button:first-child {
                border-radius: 20px;
                padding-right: 30px;
                z-index: -1;
                border-right: none;
            }

            .el-dropdown__caret-button {
                position: absolute;
                right: -9px;
                border-radius: 20px;
                border-left: none;
                border-top-left-radius: unset;
                border-bottom-left-radius: unset;
            }

            .el-dropdown__caret-button::before {
                content: '';
                position: absolute;
                display: block;
                width: 1px;
                top: 0;
                bottom: 5px;
                left: 0;
                height: 100%;
                background: rgba(255,255,255,.5);
            }
        }

        &.is-disabled {
            cursor: not-allowed;
        }
    }

    /deep/ .el-dialog {
        /deep/ .el-dialog__body {
            padding: 10px 20px;
            word-break: break-word;
        }

        .el-form-item {
            margin-bottom: 0;

            .el-form-item__label {
                line-height: 20px;
                text-align: left;
            }
        }
        .el-select {
            width: 100%;
        }
        
        .edit-type-select {
            margin-bottom: 15px;
        }

        .switch-wrapper {
            margin-top: 10px;
            margin-bottom: 0;

            .switcher__desc {
                display: block;
                font-size: 0.9em;
            }
        }
    }

    .filter-title {
        padding: 0 15px;
        margin-bottom: 10px !important;
    }
</style>