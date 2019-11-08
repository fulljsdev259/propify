<template>
    <placeholder :size="256" :src="require('img/5c98a90bb5c05.png')" v-if="!this.loader.visible && isEmpty">
        {{$t('resident.no_data.document')}}
        <small>{{$t('resident.heading_info.document_listed_by_category')}}</small>
    </placeholder>
    <div class="documents" v-else-if="!isEmpty">
        <ui-heading icon="ti-book" :title="$t('resident.my_documents')" :description="$t('resident.heading_info.my_document')">
        </ui-heading>
        <ui-divider />
        <el-row>
            <el-col :span="24" v-if="quarter_media.length">
                <div class="title">
                    {{ $t('resident.quarter') }}
                    <small>{{ quarter_media.length > 1 ? $t('resident.doc_available.multiple',{num: quarter_media.length}) : $t('resident.doc_available.single',{num: quarter_media.length}) }}</small>
                </div>
                <document-gallery-list :media="quarter_media" :cols="4" />
                <el-divider />
            </el-col>
            <el-col :span="24" v-if="building_media.length">
                <div class="title">
                    {{ $t('resident.building') }}
                    <small>{{ building_media.length > 1 ? $t('resident.doc_available.multiple',{num: building_media.length}) : $t('resident.doc_available.single',{num: building_media.length}) }}</small>
                </div>
                <document-gallery-list :media="building_media" :cols="4" />
                <el-divider />
            </el-col>
            <el-col :span="24" v-if="unit_media.length">
                <div class="title">
                    {{ $t('resident.unit') }}
                    <small>{{ unit_media.length > 1 ? $t('resident.doc_available.multiple',{num: unit_media.length}) : $t('resident.doc_available.single',{num: unit_media.length}) }}</small>
                </div>
                <document-gallery-list :media="unit_media" :cols="4" />
                <el-divider />
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {displayError} from "helpers/messages";
    import Placeholder from 'components/Placeholder'
    import DocumentGalleryList from 'components/DocumentGalleryList'
    import VueSticky from 'vue-sticky'

    export default {
        components: {
            Heading,
            Placeholder,
            DocumentGalleryList
        },
        directives: {
            sticky: VueSticky
        },
        data() {
            return {
                documents: {},
                building_media: [],
                unit_media: [],
                quarter_media: [],
                loader: {
                    visible: true
                },
                imageIdx: null,
            }
        },
        computed: {
            isEmpty () {
                // return !Object.keys(this.documents).length
                return !this.building_media.length && !this.unit_media.length && !this.quarter_media.length
            }
        },
        async mounted () {
            this.loader = this.$loading({
                target: this.$el.parentElement,
                text: this.$t('resident.fetching_message.document')
            })

            try {

                const { data } = await this.$store.dispatch('getMyDocuments')
                
                this.building_media = data.building
                this.unit_media = data.unit
                this.quarter_media = data.quarter

            } catch (err) {
                displayError(err)
            } finally {
                this.loader.close()
            }
        }
    }
</script>

<style lang="scss" scoped>
   .placeholder {
        height: 100% !important;
        font-size: 20px;
        color: var(--color-main-background-darker);
        small {
            font-size: 72%;
            color: var(--primary-color-lighter);
        }
    }
    .documents {

        &:before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/5ceaf5545afd8.png');
            background-repeat: no-repeat;
            background-position: 100% 100%;
            width: 100%;
            height: 100%;
            opacity: .08;
            pointer-events: none;
        }

        .el-card {
            max-width: 1024px;
            :global(.el-card__body) {
                padding: 12px 16px;
                .heading div {
                    color: darken(#fff, 40%);
                }
            }
        }

        .el-row {
            max-width: 1024px;
            .el-col {
                .title {
                    font-size: 18px;
                    margin-bottom: 24px;
                    small {
                        display: block;
                        color: darken(#fff, 40%);
                    }
                }
                &:last-child .el-divider {
                    display: none;
                }
            }
        }
    }

    @media only screen and (max-width: 676px) {
        .documents {
            /deep/ .ui-heading__content__description {
                display: none
            }
        }
    }
</style>
