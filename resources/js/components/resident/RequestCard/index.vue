<template>
    <ui-card class="request-card" shadow="always" v-resize:debounce="onResize">
        <el-tabs type="card" v-model="idState.activeTab" stretch v-on="$listeners">
            <el-tab-pane name="overview">
                <div slot="label">
                    <i class="el-icon-tickets"></i>
                    {{$t('resident.overview')}}
                </div>
                <slot name="tab-overview-before" />
                <div class="statuses">
                    <div class="item">
                        {{$t('resident.status')}}:
                        <div class="label">
                            {{$t(`models.request.status.${$constants.requests.status[data.status]}`)}}
                        </div>
                    </div>
                    <!-- <div class="item">
                        {{$t('resident.priority')}}:
                        <div class="label">
                            {{$t(`models.request.priority.${$constants.requests.priority[data.priority]}`)}}
                        </div>
                    </div> -->
                </div>
                <!-- <div class="statuses" v-if="this.data.sub_category && this.data.payer">
                    <div class="item">
                        {{$t('resident.cost_impact')}}:
                        <div class="label">
                            {{$t(`models.request.payer.${$constants.requests.payer[data.payer]}`)}}
                        </div>
                    </div>
                </div> -->
                <div class="category">
                    {{ $t(`models.request.category_list.${data.category.name}`) }}
                    {{ data.sub_category ? " > " + $t(`models.request.sub_category.${data.sub_category.name}`) : ""}}
                </div>
                <div class="title" @click="$emit('toggle-drawer')">{{data.title}}</div>
                <ui-readmore class="description" :text="data.description" :max="512" />
                <div class="assignees" v-if="assignees.length" v-resize:debounce="onResize">
                    {{$t('resident.assignees')}}
                    <div :key="assignee.id" class="assignee" v-for="assignee in visibleAssignees">
                        <ui-avatar :name="assignee.name" :size="32" :src="assignee.avatar" />
                        <div class="content">
                            {{assignee.name}}
                            <small>{{assignee.email}}</small>
                        </div>
                    </div>
                    <div class="more" v-if="!idState.showAllAssginees && assignees.length > 4">
                        <ui-avatar :key="assignee.user.id" :name="assignee.user.name" :size="32" :src="assignee.user.avatar" v-for="assignee in assignees.slice(3)" />
                        <el-link @click="showRestAssignees" type="success">and {{assignees.slice(3).length}} more</el-link>
                    </div>
                </div>
                
                <ui-divider />
                <div class="user">
                    <ui-avatar :name="data.resident.user.name" :size="32" :src="data.resident.user.avatar" />
                    <div class="content">
                        {{data.resident.user.name}}
                        <small>
                            {{splitDatetime(data.created_at)}}
                            <!-- <template v-if="$constants.requests.status[data.status] === 'done'"> -->
                                <!-- and solved on {{formatDatetime(data.solved_date)}} -->
                            <!-- </template> -->
                        </small>
                    </div>
                </div>
                <slot name="tab-overview-after-for-mobile" />
                <slot name="tab-overview-after" />
            </el-tab-pane>
            <el-tab-pane name="media">
                <div slot="label">
                    <i class="el-icon-picture-outline"></i>
                    {{$t('resident.media')}}
                </div>
                <slot name="tab-media-before" />
                <ui-media-gallery :files="data.media.slice(0, 3).map(({url}) => url)">
                    <div slot="after" key="view-all" class="ui-media-gallery__more_item" @click="$emit('more-media')" v-if="data.media.length">
                        <div class="ui-media-gallery__more_item__content">
                            <div>
                            <i class="icon-picture"></i>
                            {{$t('resident.actions.view_all')}}
                            </div>
                        </div>
                    </div>
                </ui-media-gallery>
                <div class="mobile-ui-media-gallery__more_item" @click="$emit('more-media')" v-if="data.media.length">
                    <div class="mobile-ui-media-gallery__more_item__content">
                        <i class="icon-picture"></i>
                        {{$t('resident.actions.view_all')}}
                    </div>
                </div>
                <!-- <gallery-list :media="data.media" :cols="4" /> -->
                <slot name="tab-media-after" />
            </el-tab-pane>
        </el-tabs>
    </ui-card>
</template>

<script>
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin';
    import {IdState} from 'vue-virtual-scroller';
    import resize from 'vue-resize-directive';

    export default {
        mixins: [
            FormatDateTimeMixin,
            IdState({
                idProp: vm => vm.data.id
            })
        ],
        directives: {
            resize
        },
        components: {
        },
        props: {
            data: {
                type: Object,
                default: {
                    media: []
                },
            },
            visibleMediaLimit: {
                type: Number,
                default: 0
            },
            mediaOptions: {
                type: Object,
                default: () => ({})
            },
            categories: {
                type: Array,
                default: () => ([])
            }
        },
        idState () {
            return {
                test: [],
                activeTab: 'overview',
                showAllAssginees: false
            }
        },
        computed: {
            assignees () {
                return [...this.data.assignedUsers]
            },
            visibleAssignees () {
                if (this.idState.showAllAssginees) {
                    return this.assignees
                }

                /*if (this.assignees.length === 4) {
                    return this.assignees.slice(0, 4)
                }

                return this.assignees.slice(0, 3)*/
                return this.assignees;
            }
        },
        methods: {
            showRestAssignees () {
                this.idState.showAllAssginees = true;
                this.$emit('tab-click');
            },
            onResize() {
                this.$emit('update-dynamic-scroller');
            }
        }
    }
</script>

<style lang="sass" scoped>
    .ui-card
        /deep/ &__body
            padding: 0

            .el-tabs
                /deep/ .el-tabs__header
                    margin-bottom: 0

                    /deep/ .el-tabs__nav-wrap
                        /deep/ .el-tabs__nav-scroll
                            /deep/ .el-tabs__nav
                                border: 0

                /deep/ .el-tabs__content
                    padding: 16px
                    height: 100%
                    display: flex
                    flex-direction: column
                    overflow-y: auto

                    #pane-overview
                        .statuses
                            display: flex
                            align-items: center
                            margin-bottom: 12px

                            .item
                                font-weight: 600
                                color: var(--color-text-secondary)
                                display: flex
                                align-items: center

                                &:not(:first-child)
                                    margin-left: 8px

                                .label
                                    font-size: 12px
                                    font-weight: 700
                                    padding: 4px 12px
                                    margin-left: 4px
                                    border-radius: 12px
                                    text-transform: uppercase
                                    background-color: darken(#fff, 4%)
                                    color: var(--color-text-placeholder)
                                    border: 1px var(--border-color-lighter) solid

                        .title, .category
                            text-overflow: ellipsis
                            overflow: hidden
                            white-space: nowrap

                        .category
                            font-size: 15px
                            font-weight: 500
                            color: var(--color-text-placeholder)

                        .title
                            font-size: 20px
                            font-weight: 600
                            margin-top: 12px
                            color: var(--color-primary)
                            cursor: pointer

                        .description
                            margin-top: 16px
                            color: var(--color-text-secondary)

                        .tab-overview-after-for-mobile
                            display: flex
                            margin-top: 12px
                            display: none
                            float: right

                        .tab-overview-after
                            float: right

                        .assignees
                            font-size: 15px
                            font-weight: 500
                            margin-top: 16px
                            color: var(--color-text-placeholder)

                            .assignee
                                font-size: 14px
                                color: var(--color-text-secondary)
                                display: flex
                                align-items: center
                                margin: 8px 0

                                .content
                                    font-weight: 600
                                    line-height: 1.28
                                    margin-left: 8px

                                    small
                                        font-size: 96%
                                        font-weight: 400
                                        display: block
                                        color: var(--color-text-placeholder)

                            .more
                                .ui-avatar
                                    &:not(:first-child)
                                        margin-left: -10px
                                        border: 2px #fff solid

                        .user
                            display: inline-flex
                            align-items: center
                            color: var(--color-text-secondary)

                            .content
                                font-weight: 600
                                line-height: 1.28
                                margin-left: 8px

                                small
                                    font-size: 96%
                                    font-weight: 400
                                    display: block
                                    color: var(--color-text-placeholder)

                    #pane-media
                        .ui-media-gallery
                            display: flex
                            margin: -4px

                            .ui-media-gallery__item
                                width: 25%
                                padding-top: 25%
                                margin: 4px

                            .ui-media-gallery__item
                                font-size: 16px
                                font-weight: 600
                                border-width: 2px
                                border-style: dashed
                                box-shadow: none
                                color: var(--color-text-placeholder)
                                cursor: pointer
                                margin-top: 10px
                                width: 25%
                                padding-top: 25%

                                &:hover
                                    color: var(--color-primary)
                                    border-color: var(--color-primary)

                                .ui-media-gallery_item__content
                                    text-align: center

                                i
                                    font-size: 28px
                                
                            .ui-media-gallery__more_item
                                font-size: 16px
                                font-weight: 600
                                border-width: 2px
                                border-style: dashed
                                box-shadow: none
                                color: var(--color-text-placeholder)
                                cursor: pointer
                                margin-top: 10px
                                width: 25%
                                height: 0
                                padding-top: 25%
                                display: flex
                                justify-content: center
                                align-items: center
                                position: relative
                                overflow: hidden
                                
                                &:hover
                                    color: var(--color-primary)
                                    border-color: var(--color-primary)

                                .ui-media-gallery__more_item__content
                                    position: absolute;
                                    top: 0
                                    right: 0
                                    bottom: 0
                                    left: 0
                                    width: 100%
                                    height: 100%
                                    display: flex
                                    flex-direction: column
                                    align-items: center
                                    justify-content: center

                                    div
                                        display: flex
                                        flex-direction: column
                                        justify-content: center
                                        align-items: center

                                i
                                    font-size: 28px
                                
                        .mobile-ui-media-gallery__more_item
                            font-size: 16px
                            font-weight: 600
                            border-width: 2px
                            border-style: dashed
                            box-shadow: none
                            color: var(--color-text-placeholder)
                            cursor: pointer
                            margin-top: 10px
                            display: none

                            &:hover
                                color: var(--color-primary)
                                border-color: var(--color-primary)

                            .mobile-ui-media-gallery__more_item__content
                                text-align: center

                            i
                                font-size: 28px

    @media only screen and (max-width: 676px)
        .ui-card
            /deep/ &__body
                .el-tabs
                    /deep/ .el-tabs__content
                        #pane-media
                            .ui-media-gallery
                                .ui-media-gallery__item
                                    width: 33.3%
                                    padding-top: 33.3%
                                .ui-media-gallery__more_item
                                    display: none
                            .mobile-ui-media-gallery__more_item
                                display: block

</style>

<style lang="scss" scoped>
    @media only screen and (max-width: 676px) {
        /deep/ .tab-overview-after {
            display: none;
        }

        /deep/ .tab-overview-after-for-mobile {
            display: flex !important;
        }
    }
</style>
