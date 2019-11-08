<template>
    <div class="media-gallery" :style="[cols > 1 ? {margin: '-4px'} : {margin: '0px'}]" v-if="media.length">
        <gallery :images="images" :index="galleryIndex" :options="galleryOptions" @close="galleryIndex = null" />
        <!-- <transition-group tag="div" class="media-list" name="media-item-fade"> -->
            <ul class="filter-list">
                <li :class="filterOption == '' ? 'active' : ''" @click="filterOption = ''">{{$t('resident.media_category.all')}}</li>
                <li v-for="item in $constants.file_categories" 
                        :key="item" 
                        :class="filterOption == item ? 'active' : ''"
                        @click="filterOption = item">
                    {{$t('resident.media_category.' + item)}}
                </li>
            </ul>
            <isotope :options='option' :list="filteredMedia" class="media-list" key="iso-top">
                
                <div class="media-item" v-for="(file, idx) in filteredMedia" :key="file.id" :style="itemStyle">
                    <div class="media-content">
                        <template v-if="isFileImage(file)">
                            <el-image :src="file.url" fit="cover" :alt="file.name" :lazy="lazy" :scroll-container="lazyScrollContainer">
                                <div slot="error" class="error" style="color: red;">
                                    <i class="el-icon-document-delete" />
                                </div>
                                <div slot="placeholder" class="placeholder el-icon-loading"></div>
                            </el-image>
                        </template>
                        <template v-else>
                            <i class="icon-file-pdf" />
                        </template>
                        <div class="media-actions">
                            <div class="el-icon-zoom-in" @click="openFile(file, idx)" v-if="canFileBePreviewed(file)"></div>
                            <div class="el-icon-delete remove" v-if="removeable" @click="removeFile(file)"></div>
                        </div>
                        <div class="badge" v-if="file.collection_name">
                            <span> {{ $t(`resident.media_category.${file.collection_name}`) }}</span>
                        </div>
                    </div>
                </div>
            </isotope>
            <slot :itemStyle="itemStyle" />
        <!-- </transition-group> -->
    </div>
    <placeholder :src="require('img/5c98a90bb5c05.png')" v-else-if="!media.length && usePlaceholder">
        {{$t('resident.no_data.media')}}
        <small>{{$t('resident.media_info')}}</small>
    </placeholder>
</template>

<script>
    import Gallery from './MediaGallery'
    import Placeholder from './Placeholder'
    import isotope from 'vueisotope'
    import {mapActions, mapGetters} from 'vuex';

    export default {
        props: {
            media: {
                type: Array,
                default: () => ([])
            },
            cols: {
                type: Number,
                default: 4
            },
            limit: {
                type: Number,
                default: 0
            },
            galleryOptions: {
                type: Object,
                default: () => ({})
            },
            removeable: {
                type: Boolean,
                default: false
            },
            usePlaceholder: {
                type: Boolean,
                default: true
            },
            lazy: {
                type: Boolean,
                default: false
            },
            lazyScrollContainer: {}
        },
        components: {
            Gallery,
            Placeholder,
            isotope
        },
        data () {
            return {
                galleryIndex: null,
                option: {
                    getSortData: {
                        id: "id"
                    },
                    sortBy : "id"
                },
                filterOption: ''
            }
        },
        methods: {
            openFile (file, idx) {
                if (this.isFileImage(file)) {
                    this.galleryIndex = idx
                } else {
                    if (this.canFileBePreviewed(file)) {
                        window.open(file.url)
                    } else {
                        this.$message.warning('This file cannot be previewed', {
                            duration: 2400,
                            offset: 88
                        })
                    }
                }
            },
            removeFile (file) {
                this.$emit('onRemove', file);
            },
            isFileImage (file) {
                const ext = file.name.split('.').pop()

                return ['jpg', 'jpeg', 'gif', 'bmp', 'png'].includes(ext)
            },
            canFileBePreviewed (file) {
                const ext = file.name.split('.').pop()

                return this.isFileImage(file) || ext === 'pdf' || ext === 'doc' || ext === 'docx' || ext === 'xls' || ext === 'xlsx'
            }
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            filteredMedia() {
                return this.localMedia.filter(item => (item.collection_name == this.filterOption)|| (this.filterOption == '') )
            },
            localMedia () {
                if (this.limit) {
                    return this.media.slice(0, this.limit)
                }

                return this.media
            },
            images () {
                return this.media.filter(file => this.isFileImage(file)).map(file => file.url)
            },
            itemStyle () {
                const width = 100 / this.cols + '%';

                return {
                    width,
                    padding: `calc(${width} - 10px) 0 0 0`,
                    margin: this.cols > 1 ? '4px' : '0px',
                    flexBasis: `calc(${width} - ${this.cols > 1 ? '8px' : '0px'})`,
                };
            }
        }
    }
</script>

<style>
    .media-item-fade-enter-active,
    .media-item-fade-leave-active {
        transition: opacity .5s;
    }
    .media-item-fade-enter,
    .media-item-fade-leave-to {
        opacity: 0;
    }
</style>


<style lang="scss" scoped>
    .filter-list {
        display: flex;
        list-style: none;
        cursor: pointer;

        li {
            margin: 10px;
            transition: 300ms;
            color: #909498;
        }

        li.active, li:hover {
            color: #222328;
            border-bottom: 1px solid #222328;
        }
    }
    .media-list {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        .media-item {
            position: relative;
            will-change: transform;
            .media-content {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
                width: 100%;
                height: 100%;
                box-sizing: border-box;
                background-color: #fff;
                border: 1px darken(#fff, 12%) solid;
                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                overflow: hidden;
                display: flex;
                text-align: center;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
                word-break: break-word;
                hyphens: auto;
                .el-image {
                    width: 100%;
                    .error,
                    .placeholder {
                        font-size: 20px;
                    }
                    .placeholder {
                        background-color: #fff;
                        border-radius: 50%;
                        border: 4px #fff solid;
                    }
                }
                .media-icon {
                    font-size: 24px;
                    margin: 4px;
                }
                .icon-file-pdf{
                    font-size: 72px;
                    margin: 4px;
                }
                .media-filename {
                    overflow: hidden;
                    min-width: 0;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    width: 100%;
                    padding: 4px;
                    box-sizing: border-box;
                }
                .media-actions {
                    background-color: transparentize(#000, .5);
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    border-radius: 6px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    z-index: 1;
                    transition: opacity .32s cubic-bezier(.17,.67,1,1.23);
                    div {
                        color: #fff;
                        font-size: 20px;
                        transition-property: color, font-size;
                        transition-duration: .24s;
                        opacity: .72;
                        cursor: pointer;
                        &:hover {
                            opacity: 1;
                        }
                        &.remove:hover {
                            color: red;
                        }
                    }
                    div + div {
                        margin-left: 8px;
                    }
                }
                &:hover .media-actions {
                    opacity: 1;
                }
                .badge {
                    position: absolute;
                    right: 2px;
                    top: 2px;
                    z-index: 0;
                    overflow: hidden;
                    width: 200px;
                    height: 200px;
                    text-align: right;

                    span {
                        font-size: 10px;
                        font-weight: bold;
                        color: #fff;
                        text-transform: uppercase;
                        text-align: center;
                        line-height: 20px;
                        transform: rotate(45deg);
                        width: 200px;
                        display: block;
                        background: var(--primary-color);
                        //background: linear-gradient(var(--primary-color), var(--primary-color-lighter));
                        box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
                        position: absolute;
                        top: 56px;
                        right: -35px;

                        &:before {
                            content: "";
                            position: absolute;
                            left: 0px;
                            top: 100%;
                            z-index: -1;
                            border-left: 3px solid var(--primary-color);
                            border-right: 3px solid transparent;
                            border-bottom: 3px solid transparent;
                            border-top: 3px solid var(--primary-color);
                        }

                        &:after {
                            content: "";
                            position: absolute;
                            right: 0px;
                            top: 100%;
                            z-index: -1;
                            border-left: 3px solid transparent;
                            border-right: 3px solid var(--primary-color);
                            border-bottom: 3px solid transparent;
                            border-top: 3px solid var(--primary-color);
                        }
                    }
                }
            }
        }
    }
    .placeholder {
        font-size: 16px;
        small {
            font-size: 72%;
            color: darken(#fff, 40%);
        }
    }
</style>
