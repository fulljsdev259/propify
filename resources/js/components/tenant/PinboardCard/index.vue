<template>
    <el-card  :class="{announcement: data.announcement}">
        <div ref="container">
            <div class="announcement" v-if="data.announcement && data.type == 3">
                <span> {{$t(`models.pinboard.sub_type.${$constants.pinboard.sub_type[3][data.sub_type]}`)}}</span>
            </div>
            <pinboard-tenant
                :data="data"
                :showActions="showActions"
                @edit-pinboard="$emit('edit-pinboard', $event, data)"
                @delete-pinboard="$emit('delete-pinboard', $event, data)"
                @hook:mounted="$emit('update-dynamic-scroller')"
            />
            <div class="title" v-if="data.announcement && data.type == 3 && data.category">
                <small>{{$t('tenant.category')}}:
                    {{$t(`models.pinboard.category.${$store.getters['application/constants'].pinboard.category[data.category]}`)}}
                </small>
                <strong>{{data.title}}</strong>
            </div>
        
            <div class="category-image" v-if="data.announcement && data.category_image == true">
                <img
                    src="~img/announcement_category/1.png"
                    class="user-image"
                    v-if="data.category == 1"
                    width="50%" 
                    height="50%"/>
                <img
                    src="~img/announcement_category/2.png"
                    class="user-image"
                    v-else-if="data.category == 2"
                    width="50%" 
                    height="50%"/>
                <img
                    src="~img/announcement_category/3.png"
                    class="user-image"
                    v-else-if="data.category == 3"
                    width="50%" 
                    height="50%"/>
                <img
                    src="~img/announcement_category/4.png"
                    class="user-image"
                    v-else-if="data.category == 4"
                    width="50%" 
                    height="50%"/>
                <img
                    src="~img/announcement_category/5.png"
                    class="user-image"
                    v-else-if="data.category == 5"
                    width="50%" 
                    height="50%"/> 
            </div>
            <hr v-if="data.announcement" />
            <read-more class="content" :text="data.content" :max-chars="512" :more-str="$t('tenant.read_more')" :less-str="$t('tenant.read_less')"/>

            <hr v-if="data.announcement"/>
            <div class="execution" v-if="data.announcement">
                {{$t('tenant.execution')}} {{execution}}
            </div>
            <div class="providers" v-if="data.announcement && data.providers && data.providers.length">
                {{$t('tenant.providers')}}: {{data.providers.map(provider => provider.name).join(', ')}}
            </div>
            <div class="gallery" v-if="data.media.length">
                <ui-images-carousel :images="data.media.map(({url}) => url)" :use-placeholder="false" :show-indicator="false" v-if="data.media.length > 0"/>
            </div>
            <!-- <media-gallery-carousel :media="data.media" :use-placeholder="false" height="320px" :autoplay="false" :gallery-options="{container: '#gallery'}" /> -->
            <likes type="pinboard" :data="data.likes" layout="row" />
            <like :id="data.id" type="pinboard">
                <el-button @click="$refs.addComment.focus()" icon="ti-comment-alt" type="text"> &nbsp;{{$t('tenant.comment')}}</el-button>
                <el-button icon="icon-picture" type="text" v-if="data.announcement === false && data.media.length">
                    <template v-if="data.media.length">
                        {{data.media.length}} {{data.media.length > 1 ? $t('tenant.images') : $t('tenant.image')}}
                    </template>
                </el-button>
            </like>
            
            <comments ref="comments" :id="data.id" type="pinboard" :use-placeholder="false" :with-scroller="true" @update-dynamic-scroller="$emit('update-dynamic-scroller')"/>
            <add-comment ref="addComment" :id="data.id" type="pinboard"/>
        </div>
    </el-card>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex'

    import AgoMixin from 'mixins/agoMixin'
    import Card from 'components/Card'
    import Likes from 'components/tenant/Likes'
    // import AddComment from 'components/AddComment'
    import MediaGalleryCarousel from 'components/MediaGalleryCarousel'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'
    import {format, isSameDay} from 'date-fns'
    import {IdState} from 'vue-virtual-scroller'
    import GalleryList from 'components/MediaGalleryList'
    import RequestMedia from 'components/RequestMedia';
    import Loader from 'components/common/AddComment/Loader';

    export default {
        name: 'p-pinboard-card',
        mixins: [
            AgoMixin,
            FormatDateTimeMixin,
            IdState({
                idProp: vm => vm.data.id
            })
        ],
        data () {
            return {
                height: null,
                loading: true
            }
        },
        props: {
            data: {
                type: Object,
                required: true
            },
            showActions : {
                type: Boolean,
                default: true
            },
        },
        idState () {
            return {
            }
        },
        components: {
            Card,
            Likes,
            // AddComment,
            MediaGalleryCarousel,
            GalleryList,
            RequestMedia,
            Loader
        },
        methods: {
            showChildrenAddComment() {
                this.$refs.comments.showChildrenAddComment()
            },
        },
        computed: {
            ...mapGetters(['loggedInUser']),

            isNewNeighbourType() {
                return this.$store.getters['application/constants'].pinboard.type[this.data.type] === 'new_neighbour'
            },
            execution() {
                const {execution_start, execution_end} = this.data

                const start = this.formatDatetime(execution_start)
                const end = isSameDay(execution_start, execution_end) ? format(execution_end, 'HH:mm') : this.formatDatetime(execution_end);
                
                return `${start} - ${end}`
            },
            tenant() {
                const {title, first_name, last_name} = this.data.tenant;

                return `${title}. ${first_name} ${last_name}`
            }
            
        },
        updated () {
            this.data.height =  this.$refs.container.clientHeight;
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        &.announcement {
            :global(.el-card__body) {
                border-width: 8px;
                border-style: solid;
                border-image: linear-gradient(to bottom, darken(#fff, 4%), transparent) 1;
                position: relative;
            }
        }

        hr {
            border-style: solid none none none;
            border-color: darken(#fff, 6%);
        }

        .user {
            display: flex;
            align-items: center;

            .name {
                font-size: 16px;
                line-height: 1.32;
                margin-left: 1em;

                small {
                    font-size: 80%;
                    display: block;
                    color: darken(#fff, 48%);
                }
            }

            .actions {
                flex-grow: 1;
                display: flex;
                justify-content: flex-end;

                button {
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    padding: 0;
                }
            }
        }

        .title {
            font-weight: 500;
            margin: 8px 0;
            line-height: 1.32;

            small {
                font-size: 80%;
                font-weight: normal;
                display: block;
                color: darken(#fff, 48%);
                margin-bottom: 5px;
            }
        }

        .category-image {
            display: flex;
            justify-content: center;
        }

        .execution {
            font-size: 12px;
            color: darken(#fff, 48%);
            margin-bottom: 5px;
        }

        .providers {
            margin-bottom: 5px;
        }

        .gallery {
            padding-bottom: 20px;
        }

        .media-gallery-carousel {
            margin: 12px -16px -17px -16px;
            box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
        }

        .like {
            background: #f2f4fa;
            padding: 10px;
        }
        .likes {
            font-size: 14px;
            margin: 0;
            padding: 0;
            padding-bottom: 16px;
            display: flex;
            align-items: center;

            :global(.avatar) {
                border: 2px #fff solid;

                &:not(:first-of-type) {
                    margin-left: -10px;
                }
            }

            .users {

                line-height: 1.32;

                small {
                    color: darken(#fff, 40%);
                }
            }
        }

        .placeholder {
            padding: 8px;
        }

        :global(.reactions) {
            border-width: 1px;
            border-color: darken(#fff, 6%);
            border-style: solid none;
            margin: 16px -16px;
            padding: 12px 16px;
        }

        :global(.comments-list) {
            margin: 16px 0;
            min-height: 30px;

            :global(.vue-recycle-scroller) {
                min-height: 30px;
            }

            > :global(.el-button) {
                padding-top: 0;
            }
        }

        .announcement {
            position: absolute;
            right: 2px;
            top: 2px;
            z-index: 1;
            overflow: hidden;
            width: 75px;
            height: 75px;
            text-align: right;

            span {
                font-size: 10px;
                font-weight: bold;
                color: #fff;
                text-transform: uppercase;
                text-align: center;
                line-height: 20px;
                transform: rotate(45deg);
                width: 100px;
                display: block;
                background: var(--primary-color);
                //background: linear-gradient(var(--primary-color), var(--primary-color-lighter));
                box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
                position: absolute;
                top: 19px;
                right: -21px;

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

        &:not(.new-neighbour) {
            .users {
                margin-left: 4px;

                small {
                    display: block;
                }
            }

            .content {
                margin: 16px 0;
                :global(p) {
                    margin: 0;
                    white-space: pre-wrap;
                }
                :global(a) {
                    color: #6AC06F;
                    text-decoration: none;
                    transition: color .48s;
                    &:focus,
                    &:hover {
                        color: lighten(#6AC06F, 16%);
                    }
                }
            }
        }

        &.new-neighbour {
            .content {
                margin-bottom: 8px;

                b {
                    text-transform: capitalize;
                }

                small {
                    display: block;
                    color: darken(#fff, 40%);
                }
            }

            .reactions {
                margin-bottom: -17px;
            }
        }
    }
</style>
