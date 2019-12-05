<template>
    <div class="residents-edit mb20 residents-edit-new" v-loading.fullscreen.lock="loading.state">
        <div class="main-content">
            <heading :title="$t('models.resident.edit_title')" icon="icon-group" class="bg-transparent">
                <template slot="description" v-if="model.resident_format">
                    <div class="subtitle">{{model.resident_format}}</div>
                </template>
                <edit-actions :saveAction="submit" :deleteAction="deleteResident" route="adminResidents" :editMode="editMode" @edit-mode="handleChangeEditMode" ref="editActions"/>
            </heading>
            <el-row :gutter="20" class="crud-view">
                <el-col :span="12">
                    <el-form :model="model" label-position="top" label-width="192px" ref="form">
                        <el-card class="left-pane">
                            <el-row :gutter="20">
                                <el-col :span="12" class="resident_avatar">
                                    <div class="status">
                                        <el-tag 
                                            :style="{'color':$constants.status_colorcode[status.index],}"
                                            effect="plain"
                                        >
                                            {{ $t(`models.resident.status.${status.text}`) }}
                                        </el-tag>
                                    </div>
                                    <div class="image-container" :class="{'hide-action-icon': !editMode}">
                                        <img v-if="!editMode" 
                                            :src="!avatar.length && user.avatar
                                                    ? '/'+user.avatar_variations[3]
                                                    : model.avatar==null && model.title == 'mr'
                                                        ? '/images/man.png'
                                                        : model.avatar==null && model.title == 'mrs'
                                                            ? '/images/woman.png'
                                                            : model.avatar==null && model.title == 'company'
                                                                ? '/images/company.png'
                                                                : ''"/>
                                        <cropper
                                            v-if="model.title && editMode"
                                            :boundary="{
                                                width: 150,
                                                height: 160
                                            }"
                                            :viewport="{
                                                width: 150,
                                                height: 150
                                            }"
                                            :resize="false"
                                            :defaultAvatarSrc="
                                                !avatar.length && user.avatar
                                                    ? '/'+user.avatar_variations[3]
                                                    : model.avatar==null && model.title == 'mr'
                                                        ? '/images/man.png'
                                                        : model.avatar==null && model.title == 'mrs'
                                                            ? '/images/woman.png'
                                                            : model.avatar==null && model.title == 'company'
                                                                ? '/images/company.png'
                                                                : ''
                                            "
                                            :showCamera="model.avatar==null"
                                            @cropped="cropped"/>
                                    </div>
                                     <el-form-item v-if="editMode" :rules="validationRules.title"
                                              prop="title"
                                              class="label-block salutation-select">
                                        <el-select :disabled="!editMode" :placeholder="$t('general.placeholders.select')" style="display: block" v-model="model.title">
                                            <el-option
                                                    :key="title.value"
                                                    :label="title.name"
                                                    :value="title.value"
                                                    v-for="title in titles">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <div 
                                        v-if="!editName" 
                                        class="first_name"
                                        @dblclick="editName=editMode"
                                    >
                                        {{ model.first_name }}
                                    </div>
                                    <el-form-item 
                                        v-if="editMode && editName"
                                        :rules="validationRules.first_name"
                                        prop="first_name"
                                        class="edit-name-input"
                                    >
                                        <el-input autocomplete="off" type="text" v-model="model.first_name" ></el-input>
                                    </el-form-item>
                                    <div 
                                        v-if="!editName" 
                                        class="last_name"
                                        @dblclick="editName=editMode"
                                    >
                                        {{ model.last_name }}
                                    </div>
                                    <el-form-item 
                                        v-if="editMode && editName"
                                        :rules="validationRules.last_name"
                                        prop="last_name"
                                        class="edit-name-input"
                                    >
                                        <el-input autocomplete="off" type="text" v-model="model.last_name" ></el-input>
                                    </el-form-item>

                                    <span class="type">{{ status.type }}</span>
                                
                                </el-col>
                                <el-col :span="12">
                                    <div v-if="!editMode" class="user-info-item">
                                        <span>{{ $t('models.resident.mobile_phone') }}</span>
                                        <span>+{{ model.mobile_phone }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.resident.mobile_phone')" prop="mobile_phone">
                                        <el-input autocomplete="off" type="text"
                                                v-model="model.mobile_phone"></el-input>
                                    </el-form-item>
                                    <div v-if="!editMode" class="user-info-item">
                                        <span>{{ $t('models.resident.private_phone') }}</span>
                                        <span>+{{ model.private_phone }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.resident.private_phone')" prop="private_phone">
                                        <el-input autocomplete="off" type="text"
                                                v-model="model.private_phone"></el-input>
                                    </el-form-item>
                                    <div v-if="!editMode" class="user-info-item">
                                        <span>{{ $t('general.email') }}</span>
                                        <span>{{ model.email }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('general.email')" :rules="validationRules.email" prop="email">
                                        <el-input autocomplete="off" type="email" v-model="model.email"></el-input>
                                    </el-form-item>
                                    <div v-if="!editMode" class="user-info-item">
                                        <span>{{ $t('models.resident.birth_date') }}</span>
                                        <span>{{ birthDate }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.resident.birth_date')"
                                                :rules="validationRules.birth_date"
                                                prop="birth_date">
                                        <el-date-picker
                                                :placeholder="$t('models.resident.birth_date')"
                                                format="dd.MM.yyyy"
                                                style="width: 100%;"
                                                type="date"
                                                v-model="model.birth_date"
                                                :picker-options="birthDatePickerOptions"
                                                value-format="yyyy-MM-dd"/>
                                    </el-form-item>
                                    <div v-if="!editMode" class="user-info-item">
                                        <span>{{ $t('models.resident.nation') }}</span>
                                        <span>{{ nation }}</span>
                                    </div>
                                    <el-form-item v-if="editMode" :label="$t('models.resident.nation')"
                                                prop="nation">
                                        <el-select 
                                            filterable
                                            clearable
                                            v-model="model.nation"
                                        >
                                            <el-option :key="country.id"
                                                    :label="country.name"
                                                    :value="country.id"
                                                    v-for="country in countries"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item v-if="editMode" :label="$t('general.password')" :rules="validationRules.password"
                                                prop="password">
                                        <el-input autocomplete="off"
                                                type="password"
                                                v-model="model.password"
                                                class="dis-autofill"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                        ></el-input>
                                    </el-form-item>
                                    <div class="actions" v-if="!editMode">
                                        <el-button
                                            class="action-download"
                                            icon="el-icon-download"
                                            @click="downloadCredentials"
                                            circle
                                        >
                                        </el-button>
                                        <el-button
                                            class="action-email"
                                            icon="el-icon-message"
                                            @click="sendCredentials"
                                            circle
                                        >
                                        </el-button>
                                        <el-button
                                            class="action-chat"
                                            icon="el-icon-chat-round"
                                            @click="sendCredentials"
                                            circle
                                        >
                                        </el-button>
                                    </div>
                                </el-col>
                            </el-row>
                        </el-card>
                    </el-form>

                </el-col>
                <el-col :span="12">
                    <el-tabs type="border-card" v-model="activeTab1">
                        <el-tab-pane name="relation" :class="{'view-mode': !editMode}">
                            <span slot="label">
                                {{ $t('general.relations') }}
                                <!-- <el-badge :value="auditCount" :max="99" class="admin-layout">{{ $t('general.audits') }}</el-badge> -->
                            </span>
                            <relation-list-table
                                :items="model.relations"
                                :hide-avatar="true"
                                @edit-relation="editRelation"
                                @delete-relation="deleteRelation">
                            </relation-list-table>
                            <el-button
                                v-if="editMode"
                                style="float:right" 
                                type="primary" 
                                @click="showRelationDialog" 
                                icon="icon-plus" 
                                size="mini" >
                                {{ $t('models.resident.relation.add') }}
                            </el-button>
                        </el-tab-pane>
                        <el-tab-pane name="files" :class="{'view-mode': !editMode}">
                            <span slot="label">
                                {{ $t('general.box_titles.files') }}
                                <!-- <el-badge :value="fileCount" :max="99" class="admin-layout">{{ $t('general.box_titles.files') }}</el-badge> -->
                            </span>
                            <relation-list
                                :actions="mediaActions"
                                :columns="mediaColumns"
                                :show-header="false"
                                :filterValue="model.id"
                                fetchAction="getResidentMedia"
                                filter="resident_id"
                                v-if="model.id"
                                ref="mediaList"
                                @delete-media="deleteMedia"
                            />
                            <el-button 
                                v-if="editMode"
                                style="float:right" 
                                type="primary" 
                                @click="showMediaDialog" 
                                icon="icon-plus"
                                size="mini" >
                                {{ $t('models.resident.relation.add_files') }}
                            </el-button>
                        </el-tab-pane>

                    </el-tabs>
                    <el-tabs type="border-card" v-model="activeTab2">
                        <el-tab-pane name="tab_3_1">
                            <span slot="label">
                                {{ $t('models.resident.request') }}
                            </span>
                        </el-tab-pane>
                        <el-tab-pane name="tab_3_2">
                            <span slot="label">
                                {{ $t('models.resident.pinboard_contribution') }}
                            </span>
                        </el-tab-pane>
                        <el-tab-pane name="audit">
                            <span slot="label">
                                {{$t('general.audits')}}
                            </span>
                            <audit v-if="model.id" :id="model.id" type="resident" ref="auditList" showFilter/>
                        </el-tab-pane>
                    </el-tabs>
                </el-col>
            </el-row>
        </div>
        <el-dialog :close-on-click-modal="true" :title="editingRelation ? $t('models.resident.relation.edit') : $t('models.resident.relation.new')"
                :visible.sync="visibleRelationDialog"
                v-loading="loading.state" width="30%" class="w-50">
            <div class="content" v-if="visibleRelationDialog">
                <relation-form v-if="editingRelation" 
                            :hide-building-and-units="false" 
                            mode="edit" 
                            :data="editingRelation" 
                            :resident_type="model.type" 
                            :resident_id="model.id" 
                            :visible.sync="visibleRelationDialog" 
                            :edit_index="editingRelationIndex" 
                            @update-relation="updateRelation"
                            @delete-relation="deleteRelation"
                            :used_units="used_units"/>
                <relation-form v-else 
                            mode="add" 
                            :resident_type="model.type" 
                            :resident_id="model.id" 
                            :visible.sync="visibleRelationDialog" 
                            @add-relation="addRelation" 
                            @delete-relation="deleteRelation"
                            :used_units="used_units"/>
            </div>
            <span class="dialog-footer" slot="footer">
                <!-- <el-button @click="closeModal" size="mini">{{$t('models.building.cancel')}}</el-button>
                <el-button @click="assignManagers" size="mini" type="primary">{{$t('models.building.assign_managers')}}</el-button> -->
            </span>
        </el-dialog>

        <el-dialog :close-on-click-modal="true" 
                :title="$t('general.box_titles.files')"
                :visible.sync="visibleMediaDialog"
                :destroy-on-close="true"
                v-loading="loading.state" width="30%" class="w-50">
            <div class="content" v-if="visibleMediaDialog">
                

                <!-- <el-alert
                    :title="$t('models.resident.relation.pdf_only_desc')"
                    type="info"
                    show-icon
                    :closable="false"
                >
                </el-alert> -->
                <div class="media-box">
                    <upload-relation @fileUploaded="addPDFtoRelation" class="upload-custom" acceptType=".jpg,.jpeg,.png,.gif,.bmp,.pdf,.JPG,.JPEG,.PBG,.GIF,.BMP,.PDF" drag multiple/>

                    <relation-file-table :media="model.media" @delete="deletePDFfromRelation"></relation-file-table>
                   
                </div>
                <!-- <ui-divider style="margin-top: 16px;"></ui-divider> -->
                <div class="relation-form-actions">
                    <div class="button-group">
                        <el-button type="default" @click="closeMediaDialog" icon="icon-cancel"  >{{$t('general.actions.close')}}</el-button>
                        <el-button type="primary" @click="uploadMedia" icon="icon-floppy">{{$t('general.actions.save')}}</el-button>
                        
                    </div>
                </div>
            </div>
            <span class="dialog-footer" slot="footer">
                <!-- <el-button @click="closeModal" size="mini">{{$t('models.building.cancel')}}</el-button>
                <el-button @click="assignManagers" size="mini" type="primary">{{$t('models.building.assign_managers')}}</el-button> -->
            </span>
        </el-dialog>
        
         <edit-close-dialog 
            :centerDialogVisible="visibleDialog"
            @clickYes="submit(), visibleDialog=false, $refs.editActions.goToListing()"
            @clickNo="visibleDialog=false, $refs.editActions.goToListing()"
            @clickCancel="visibleDialog=false"
        ></edit-close-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import CircularProgressStatisticsCard from 'components/CircularProgressStatisticsCard';
    import ColoredStatisticsCard from 'components/ColoredStatisticsCard.vue';
    import ProgressStatisticsCard from 'components/ProgressStatisticsCard.vue';
    import AdminResidentsMixin from 'mixins/adminResidentsMixin';
    import RelationForm from 'components/RelationForm';
    import RelationListTable from 'components/RelationListTable';
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import Cropper from 'components/Cropper';
    import EditActions from 'components/EditViewActions';
    import SelectLanguage from 'components/SelectLanguage';
    import RelationList from 'components/RelationListing';
    import UploadRelation from 'components/UploadRelation';
    import { EventBus } from '../../../event-bus.js';
    import EditCloseDialog from 'components/EditCloseDialog';
    import RelationFileTable from 'components/RelationFileTable';

    import {
        format, parse
    } from 'date-fns';

    const mixin = AdminResidentsMixin({
        mode: 'edit'
    });

    export default {
        mixins: [mixin],
        components: {
            Heading,
            Card,
            RawGridStatisticsCard,
            CircularProgressStatisticsCard,
            ColoredStatisticsCard,
            ProgressStatisticsCard,
            Cropper,
            EditActions,
            SelectLanguage,
            RelationForm,
            RelationListTable,
            RelationList,
            UploadRelation,
            EditCloseDialog,
            RelationFileTable
        },
        data() {
            return {
                mediaCount: 0,
                editMode: false,
                editName: false,
                activeTab1: 'relation',
                activeTab2: 'audit',
                visibleDialog: false,
                mediaColumns: [{
                    type: 'residentMediaName',
                    prop: 'name',
                    label: 'general.name'
                }, {
                    width: '100px',
                    prop: 'created_by',
                    label: 'general.date'
                }],
                mediaActions: [{
                    width: 40,
                    dropdowns: [{
                        key: 'delete-media',
                        title: 'general.actions.delete'
                    }],
                }]
            }
        },
        methods: { 
            ...mapActions([
                'deleteResident',
            ]),
            handleChangeEditMode() {
                if(!this.editMode) {
                    this.editMode = !this.editMode;
                    this.old_model = _.clone(this.model, true);
                } else {
                    if(JSON.stringify(this.old_model) !== JSON.stringify(this.model)) {
                        this.visibleDialog = true;
                    } else {
                        this.$refs.editActions.goToListing();
                    }
                }
            },
            addPDFtoRelation(file) {
                //let toUploadRelationFile = {...file, url: URL.createObjectURL(file.raw)}
                let toUploadRelationFile = {media : file.src, name: file.raw.name}
                this.new_media.push(toUploadRelationFile)
            },
            deletePDFfromRelation(index) {
                this.new_media.splice(index, 1)
            },
            pickFile(){
                this.$refs.userImage.click()
            },
            onFilePicked(e){
                const files = e.target.files
                if(files[0]!==undefined){
                    this.model.avatar = files[0]
                    const fr = new FileReader()
                    fr.readAsDataURL(files[0])
                    fr.addEventListener('load', () => {
                        this.avatar = fr.result
                    })

                }
            },
            cropped(d) {
                this.avatar = d
            },
            ...mapActions(['deleteMediaFile', 'downloadResidentCredentials', 'sendResidentCredentials', 'uploadMediaFile']),
            async deleteMedia(index) {
                try {
                    let res = await this.deleteMediaFile({
                        id: this.model.id,
                        media_id: this.model.media[index].id
                    })
                    if(res.success) {
                        displaySuccess(res);
                        this.model.media.splice(index, 1);
                        this.$refs.mediaList.fetch();
                    }
                } catch( err ) {
                    displayError(err);
                }
                // this.deleteMediaFile({
                //     id: this.model.id,
                //     media_id: this.lastMedia.id
                // }).then(r => {
                //     displaySuccess(r);

                //     this.model.media.splice(-1, 1);
                // }).catch(err => {
                //     displayError(err);
                // });
            },
            requestEditView(request) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: request.id
                    }
                })
            },
            pinboardEditView(pinboard) {
                this.$router.push({
                    name: 'adminPinboardEdit',
                    params: {
                        id: pinboard.id
                    }
                })
            },
            listingEditView(listing) {
                this.$router.push({
                    name: 'adminListingsEdit',
                    params: {
                        id: listing.id
                    }
                })
            },
            async downloadCredentials() {
                this.loading.state = true;
                try {
                    const resp = await this.downloadResidentCredentials({id: this.model.id});
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
                    this.loading.state = false;
                }
            },
            async sendCredentials() {
                this.loading.state = true;
                try {
                    const resp = await this.sendResidentCredentials({id: this.model.id});
                    if (resp && resp.data) {
                        displaySuccess(resp.data);
                    }
                } catch (e) {
                    displayError(e);
                } finally {
                    this.loading.state = false;
                }
            },
            requestStatusBadge(status) {
                const colorObject = {
                    1: '#bbb',
                    2: '#ebb563',
                    3: '#ebb563',
                    4: '#67C23A',
                    5: '#ebb563',
                    6: '#67C23A'
                };

                return colorObject[status];
            },
            requestStatusLabel(status) {
                return this.$t(`models.request.status.${this.requestStatusConstants[status]}`)
            }
        },
        mounted() {
            this.$root.$on('changeLanguage', () => this.getCountries());

            EventBus.$on('resident-media-counted', media_count => {
                this.mediaCount = media_count;
            });
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            lastMedia() {
                return this.model.media[this.model.media.length - 1];
            },
            requestStatusConstants() {
                return this.constants.requests.status
            },
            status() {
                let result = '';
                let role = [];
                
                result = this.model.relations.find((relation) => {
                    return relation.status === 1;
                });
                if(result === undefined)
                    result = 'not_active';
                else
                    result = 'active';

                this.model.relations.forEach((item) => {
                    let type = this.$t(`models.resident.relation.type.${this.constants.relations.type[item.type]}`);
                    if(!role.includes(type)) {
                        role.push(type);
                    }
                });
                role = role.join(', ');
                return {
                    text: result,
                    index: result === 'active'?1:2,
                    type: role,
                }
            },
            birthDate() {
                return format(new Date(this.model.birth_date), 'DD.MM.YYYY')
            },
            nation() {
                let result = '';
                result = this.countries.find((country) => {
                    return country.id === this.model.nation;
                });
                if(result === undefined)
                    result = '';
                else    
                    result = result.name;
                return result;
            }
            
        }
    }
</script>
<style lang="scss">
    .el-tabs--border-card {
        border-radius: 6px;
        .el-tabs__header {
            border-radius: 6px 6px 0 0;
        }
        .el-tabs__nav-wrap.is-top {
            border-radius: 6px 6px 0 0;
        }
    }

    .last-form-row {
        margin-bottom: -22px;
    }

    .residents-edit-new{

        .chart-card{

            .left-col{
                padding-left: 10px!important;
            }

            .right-col{
                padding-right: 10px!important;
            }        
        
        }

        .user-image{
            max-width: 170px;
        }

        .chart-card-header{
            font-size: 16px;
            font-weight: 400;
            padding: 0 20px 16px;
            margin: -4px -10px 10px;
            border-bottom: 1px solid #EBEEF5;

            h3 {
                font-size: 24px;
                font-weight: 500;
            }
        }
    
        .el-card {
            margin: 0 10px 40px;
            box-shadow: none !important;
            border: 1px solid var(--border-color-base);
            border-radius: 6px;
            .el-card__body {
                padding: 0 !important;
            }
        }

        .el-input__inner {
            font-weight: bold;
        }
        .el-input__prefix {
            left: 11px;
            font-weight: 900;
            font-size: 9px;
            color: var(--color-black);

        }

        .left-pane{
            background-color: #f6f5f7;
            .el-col:first-child {
                padding: 40px 10px 20px 40px !important;
                
                .image-container {
                    margin-bottom: 15px;
                }
                .salutation-select {
                    margin: 0 0 3px;
                    & .el-input__inner {
                        font-size: 20px;
                        color: var(--color-text-primary);
                    }
                }
                .first_name, .last_name {
                    padding-left: 10px;
                    text-transform: capitalize;
                    font-size: 32px;
                    font-weight: 900;
                    color: var(--color-text-primary);
                    line-height: 1;
                }
                .edit-name-input {
                    margin: 0px;
                    .el-input__inner {
                        font-size: 32px;
                        font-family: 'Radikal';
                    }
                    &:nth-type-of(1) {
                        margin-top: 10px !important;
                        margin-bottom: 40px;  
                    }
                }
                .last_name {
                    margin-top: 10px;
                    margin-bottom: 20px;
                }
            }
            .el-col:last-child {
                padding: 30px 20px 20px !important;
                background-color: var(--color-white);
            }
            .user-info-item {
                display: flex;
                justify-content: space-between;
                margin-bottom: 25px;
                padding: 0 20px;
                span {
                    font-size: 15px;
                    &:first-child {
                        color: var(--color-text-secondary);
                        text-align: left;
                    }
                    &:nth-child(2) {
                        text-align: right;
                    }
                }
            }
            .status {
                margin-bottom: 40px;
                margin-left: 10px;
                .el-tag {
                    font-weight: 700;
                    border: transparent;
                    padding: 0 15px;
                    font-size: 13px;
                    height: 30px;
                    line-height: 30px;
                }
            }
            .type {
                margin-left: 10px;
                margin-top: 10px;
                font-size: 16px;
                color: var(--color-text-secondary);
            }
            .actions {
                text-align: center;
                margin-top: 100px;
                margin-bottom: 40px;
                .el-button {
                    font-size: 20px;
                    &:not(:last-of-type) {
                        margin-right: 20px;
                    }
                }
                .action-download {
                    color: #8E8F26;
                    background-color: rgba(#8e8f26, 0.2);
                }
                .action-email {
                    color: #317085;
                    background-color: rgba(#317085, 0.2);
                }
                .action-chat {
                    color: #640032;
                    background-color: rgba(#640032, 0.2);
                }
            }
        } 

    }
</style>

<style lang="scss" scoped>
    
    .residents-edit {
        overflow: hidden;
        flex: 1;

        .main-content { 
            overflow-x: hidden;
            overflow-y: scroll;
            height: 100%;
        }

        .heading {
            margin-bottom: 20px;
        }

        .chart-card{
            margin-bottom: 1em !important;
            background-color: #fff;
            border: 1px solid #ededed;
            border-radius: 4px;
            box-shadow: 0 1px 1px 0 transparentize(#000, .2);
        }
        // .user-info {
        //     border-left: 2px dashed #ccc;
        // }
        > .el-row > .el-col {
            &:first-of-type .el-card:not(:last-of-type) {
                margin-bottom: 2em;
            }

            &:last-of-type {
                > *:not(:last-of-type) {
                    margin-bottom: 1em;
                }
            }
        }
        .edit-user-image{
            position: absolute;
            right: 0;
            bottom: 5px;
            font-size: 18px;
            background-color: transparentize(#000, .5);
            color: white;
            padding: 5px 10px;
            cursor: pointer;
        }
        #language_select {
            margin-left: 0px !important;
            margin-right: 0px !important;
        }
        .resident_avatar {
            .image-container {
                position: relative;
                display: inline-block;

                img {
                    border-radius: 50%;
                    width: 120px;
                    height: 120px;
                }
                
                .edit-icon {
                    position: absolute;
                    right: 0;
                    top: 0;
                    z-index: 999;
                }
                &.hide-action-icon {
                    :global(.avatar-box__btn) {
                        display: none;
                    }
                }
                /deep/ .avatar-box {
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;

                    .el-avatar {
                        width: 120px !important;
                        height: 120px !important;
                        line-height: 120px !important;
                        border-radius: 50%;
                        img {
                            border-radius: 50%;
                        }
                    }
                    .avatar-box__btn {
                        right: 0;
                        top: 0;
                        left: unset;
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        border: 4px solid var(--color-white);
                        background-color: var(--color-text-primary);

                        .el-icon-camera-solid {
                            position: absolute;
                            right: 2px;
                            top: 2px;
                            transform: rotate(90deg);
                            font-size: 16px;
                            &:before {
                                content: "\270E" !important;
                            }
                        }
                    }
                }
            }
        }

        .el-select {
            display: block;
        }

        .ui-drawer {
            .ui-divider {
                margin: 32px 16px 0 16px;
                i {
                    padding-right: 0;
                }

                /deep/ .ui-divider__content {
                    left: 0;
                    z-index: 1;
                    padding-left: 0;
                    font-size: 16px;
                    font-weight: 700;
                    color: var(--color-primary);
                }
            }

            .content {
                height: calc(100% - 70px);
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                padding: 16px;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                -ms-flex-direction: column;
                flex-direction: column;
                position: relative;

            }
        }
        
        .el-tab-pane {
            height: 300px;
            :global(.el-table .el-table__body) {
                max-height: 250px;
            }
            &.view-mode {
                :global(.el-table .el-table__body) {
                    max-height: 300px;
                }
            }
            & > div {
                &::-webkit-scrollbar{
                    width: 8px;
                }
                &::-webkit-scrollbar-thumb{
                    background-color: var(--color-text-placeholder);
                    border: 1px solid transparent;
                    border-radius: 11px;
                    background-clip: content-box;
                }
                &::-webkit-scrollbar * {
                    background: transparent;
                }
            }
        }
        #pane-relation, #pane-files {
            & > div {
                min-height: 235px;
            }
            & > .el-button {
                position: absolute;
                bottom: 20px;
                right: 30px;
                padding: 12px 15px;
                background-color: #878810;
                border: transparent;
                &:hover {
                    color: var(--color-white);
                    box-shadow: 0px 0px 5px#878810;
                }
            }
        }
        :global(#pane-relation .el-button) {
            margin-top: 0px !important;
            margin-bottom: 0px !important;
        }

        

        :global(.el-table td) {
            border: none;
        }
    }
    .w-50 {

        /deep/ .el-dialog {
            width: 50% !important;  

            .el-dialog__header {
                padding-left: 30px;
            }

            .el-dialog__body {
                padding-top: 0;
                padding-bottom: 20px;
            }

            .el-dialog__footer {
                padding: 0;
            }
        }
    }

    /deep/ .el-table {
        .el-button {
            padding: 0;
        }
    }

    @media only screen and (max-width:992px){
        /deep/ .el-dialog {
            width: 70% !important;
        }
    }

    .media-box {
        display: flex;
    }

    
    /deep/ .relation-form-actions {
        margin-top: 30px;

        .button-group {
            display: flex;
            justify-content: flex-end;

        }
        
    }
</style>
