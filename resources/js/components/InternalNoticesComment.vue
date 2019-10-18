<template>
    <div class="internal-notices-container">
        <el-card :class="['box-card', 'comment', {'is-reversed': reversed}]">
            <div class="header">
                <div class="user">    
                    <ui-avatar ref="avatar" :name="data.user.name" :size="32" :src="data.user.avatar" />
                    <div class="user-info">
                        {{ data.user.name }}
                        <small style="display: block;">{{ago(data.created_at, $i18n.locale)}}</small>
                    </div>
                </div>
                <div class="actions" v-if="hasActions">                                    
                    <el-button type="text" @click="enterEdit" v-if="data.comment">
                        <i class="icon-pencil"></i>
                    </el-button>
                    <el-button type="text" @click="remove">
                        <i class="icon-trash-empty" style="color: red;"></i>
                    </el-button>
                </div>
            </div>
            <div ref="container" class="container">
                <el-input ref="content" 
                    :class="{'is-focused': idState.focused}" 
                    type="textarea" 
                    resize="none" 
                    v-if="idState.editing" 
                    v-model="comment" autosize :disabled="idState.loading._isVue && idState.loading.visible" 
                    :validate-event="false" 
                    @blur="idState.focused = false" 
                    @focus="idState.focused = true" 
                    @keydown.native.enter="$emit('size-changed')" 
                    @keydown.native.alt.enter.exact="update" 
                    @keydown.native.stop.esc.exact="cancelEdit" />
                    
                <div class="content" :class="{'empty': !comment, 'disabled': idState.loading._isVue && idState.loading.visible}" v-else>
                    <div class="text">{{comment || $t('general.components.common.comment.deleted_comment_placeholder')}}</div>                
                </div>
                
                <div class="managers" >
                    <div class="edit-action">
                        <p>{{ $t('general.components.common.internalnotices.shared_with') }} : </p>
                        <template v-if="idState.editing">
                            <i18n path="general.components.common.comment.update_or_cancel" tag="div" class="extra">
                                <el-tooltip :content="$t('general.components.common.comment.update_shortcut', {shortcut: updateKeysShortcut})" placement="bottom-start" place="update">
                                    <el-button type="text" :disabled="idState.loading._isVue && idState.loading.visible" @click="update">
                                        {{$t('general.components.common.comment.update')}}
                                    </el-button>
                                </el-tooltip>
                                <el-tag size="mini" place="esc">{{$t('general.components.common.comment.esc')}}</el-tag>
                                <el-button type="text" :disabled="idState.loading._isVue && idState.loading.visible" @click="cancelEdit" place="cancel">
                                    {{$t('general.components.common.comment.cancel')}}
                                </el-button>
                            </i18n>
                        </template>
                    </div>
                    <div class="shared-with" v-if="data.managers !== undefined && data.managers.length !== 0">
                        <el-tooltip
                            v-for="(item, index) in data.managers"
                            :key="index" 
                            :content="item.user.name" 
                            placement="top" 
                            effect="light"
                        >
                            <avatar :size="32"
                                    :username="item.user.name"
                                    backgroundColor="rgb(223, 200, 243)"
                                    color="#fff"
                                    v-if="!item.user.avatar"></avatar>
                            <avatar :size="32" :src="`/${item.user.avatar}`" v-else></avatar>
                        </el-tooltip>
                    </div>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
    import ErrorFallback from 'components/common/Comment/Error'
    import AgoMixin from 'mixins/agoMixin'
    import {IdState} from 'vue-virtual-scroller'
    import {displaySuccess, displayError} from 'helpers/messages'
    import { EventBus } from '../event-bus.js';
    import { Avatar } from 'vue-avatar';

    export default {
        mixins: [
            AgoMixin,
            IdState({
                idProp: vm => vm.data.id
            })
        ],
        components: {
            Avatar
        },
        props: {
            id: {
                type: Number,
            },
            parentId: {
                type: Number
            },
            type: {
                type: String,
                validator: type => ['pinboard', 'listing', 'request', 'conversation', 'internalNotices'].includes(type)
            },
            data: {
                type: Object,
                default: () => ({
                    user: {
                        name: ''
                    }
                }),
                required: true
            },
            reversed: {
                type: Boolean,
                default: false
            },
            showChildren: {
                type: Boolean,
                default: false
            }
        },
        idState () {
            return {
                loading: {
                    visible: false
                },
                editing: false,
                focused: false,
                observer: null,
                commentProxy: null,
                visibleAddComment: false
            }
        },
        data () {
            return {
                errorFallback: ErrorFallback
            }
        },
        mounted () {
            this.data.height =  this.$refs.container.clientHeight
        },
        methods: {
            enterEdit () {
                this.idState.editing = true

                this.$nextTick(() => {
                    this.$refs.content.focus()

                    this.observer = new MutationObserver(() => this.$emit('size-changed')).observe(this.$refs.content.$el.querySelector('textarea'), {
                        attributes: true,
                        attributeFilter: ['style']
                    })
                })

                this.$emit('enter-edit')
            },
            cancelEdit () {
                this.idState.editing = false
                this.comment = this.data.comment

                this.$emit('cancel-edit')
            },
            async update () {
                if (!this.comment) {
                    return
                }

                if (this.comment === this.data.comment) {
                    return this.cancelEdit()
                }

                let loadingParams = {
                    target: this.$refs.avatar.$el
                }

                this.idState.loading = this.$loading(loadingParams)

                this.$refs.content.blur()

                let params = {
                    id: this.id,
                    commentable: this.type,
                    comment: this.comment,
                    parent_id: this.data.id
                }

                if (this.$parent.$parent.data) {
                    params.child_id = this.data.id;
                    params.parent_id = this.$parent.$parent.data.id
                }

                try {
                    await this.$store.dispatch('comments/update', params)
                } catch (error) {
                    this.comment = this.data.comment

                    displayError(error)
                } finally {
                    this.cancelEdit()

                    this.idState.loading.close()
                }
            },
            async remove () {
                this.idState.loading = this.$loading({
                    target: this.$refs.avatar.$el
                })

                let params = {
                    id: this.id,
                    commentable: this.type,
                    parent_id: this.data.id
                }

                if (this.$parent.$parent.data) {
                    params.child_id = this.data.id;
                    params.parent_id = this.$parent.$parent.data.id
                }

                try {
                    await this.$store.dispatch('comments/delete', params)                    
                } catch (error) {
                    displayError(error)
                } finally {
                    this.idState.loading.close()
                }
            },
            async getChildren() {
                const {
                    current_page,
                    last_page
                } = this.data.children;

                if (current_page && last_page &&
                    current_page == last_page) {
                    return
                }

                let page = current_page || 0

                page++

                this.idState.loading.visible = true

                try {
                    await this.$store.dispatch('comments/get', {
                        id: this.id,
                        parent_id: this.data.id,
                        commentable: this.type,
                        page,
                        per_page: 5,
                        sortedBy: 'desc',
                        orderBy: 'created_at'
                    })
                } catch (err) {
                    displayError(err)
                } finally {
                    this.idState.loading.visible = false
                }
            },
            showAddComment() {
                if (!this.idState.visibleAddComment) {
                    this.idState.visibleAddComment = true
                }

                this.$nextTick(() => this.$refs.addComment.focus())
            }
        },
        computed: {
            comment: {
                get () {
                    return (this.idState.commentProxy === null) ? this.data.comment : this.idState.commentProxy
                },
                set (content) {
                    this.idState.commentProxy = content
                }
            },
            hasActions() {
                if((this.$store.getters.loggedInUser.hasOwnProperty('resident')) && (this.$store.getters.loggedInUser.resident)){
                    return false;
                }
                else{
                    return (this.data.comment || !this.data.children_count) && !this.idState.loading.visible && this.data.user_id === this.$store.getters.loggedInUser.id
                }                
            },
            updateKeysShortcut () {
                if (navigator.platform.toUpperCase().includes('MAC')) {
                    return 'option+enter'
                }

                return 'alt+enter'
            }
        },
        beforeDestroy () {
            if (this.observer) {
                this.observer.disconnect()
            }
        },

    }
</script>

<style lang="scss" scoped>
    .internal-notices-container {
        padding: 1px 0 5px;
        .comment {
            font-size: 14px;
            position: relative;

            .header {
                margin-left: 0px !important;
                display: flex;
                align-items: center;
                justify-content: space-between;
                .user {
                    display: flex;
                    .user-info {
                        margin-left: 10px;
                    }
                }
            }
            .container {
                position: relative;

                .el-textarea {
                    position: relative;
                    margin: 2px 0;

                    &.is-disabled:before {
                        border-bottom-color: #E4E7ED;
                    }

                    &.is-disabled:after {
                        border-bottom-color: #F5F7FB;
                    }

                    &:not(.is-disabled):after {
                        border-bottom-color: #fff;
                    }

                    &:not(.is-disabled).is-focused:before {
                        border-bottom-color: #6AC06F;
                    }

                    &:not(.is-disabled):not(.is-focused):before {
                        border-bottom-color: #DCDFE6;
                    }

                    &:not(.is-disabled):not(.is-focused):hover:before {
                        border-bottom-color: #C0C4CC;
                    }

                    &.is-focused :global(.el-textarea__inner)::-webkit-scrollbar-thumb {
                        background-color: #6AC06F;
                        box-shadow: inset -1px -1px 0px darken(#6AC06F, 4%), inset 1px 1px 0px darken(#6AC06F, 4%);
                    }

                    &:not(.is-focused) :global(.el-textarea__inner) {
                        &:hover::-webkit-scrollbar-thumb {
                            background-color: #C0C4CC;
                            box-shadow: inset -1px -1px 0px darken(#C0C4CC, 4%), inset 1px 1px 0px darken(#C0C4CC, 4%);
                        }

                        &:not(:hover)::-webkit-scrollbar-thumb {
                            background-color: #DCDFE6;
                            box-shadow: inset -1px -1px 0px darken(#DCDFE6, 4%), inset 1px 1px 0px darken(#DCDFE6, 4%);
                        }
                    }

                    :global(.el-textarea__inner) {
                        padding: 6px 8px;
                        max-height: 256px;
                        overflow-y: overlay;
                        overflow-x: hidden;
                        scrollbar-width: thin;
                        overscroll-behavior: contain;
                        -webkit-appearance: none;
                        -webkit-overflow-scrolling: touch;

                        &::-webkit-scrollbar {
                            width: 14px;
                        }

                        &::-webkit-scrollbar-thumb {
                            border: 4px transparent solid;
                            background-clip: padding-box;
                            border-radius: 12px;
                        }

                        &::-webkit-scrollbar-thumb:window-inactive {
                            background-color: lighten(#6AC06F, 16%);
                        }
                    }
                }

                .content {
                    position: relative;

                    &.disabled {
                        &:before {
                            content: '';
                            position: absolute;
                            top: 0;
                            left: -6px;
                            width: calc(100% + 6px);
                            height: 100%;
                            background-color: transparentize(#fff, .1);
                            z-index: 1;
                            pointer-events: none;
                        }
                    }

                    .text {
                        padding: 5px;
                        position: relative;
                        white-space: pre-line;
                        word-break: break-word;
                        margin: 2px 0;
                    }
                }

                .actions {
                    visibility: hidden;
                    display: flex;
                    align-self: center;

                    .el-button {
                        &:first-of-type {
                            margin-left: 10px;
                        }

                        &:last-of-type {
                            margin-right: 10px;
                        }
                    }
                }
                .managers {
                    .edit-action {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        p {
                            margin: 0 0 5px;
                        }

                    }
                    .shared-with {
                        display: flex;
                        align-items: center;
                        .el-tooltip {
                            margin-right: 1px;
                        }
                    }
                }

                &:hover .actions {
                    visibility: visible;
                }
            }

            > * {
                &.extra {
                    padding: 2px 0;
                    color: darken(#fff, 40%);

                    .el-button {
                        padding: 0;
                    }
                }
            }

            .children {
                width: 100%;
                display: flex;
                flex-direction: column;
                padding: 8px;
                border-radius: 6px;

                > .el-button {
                    display: block;
                    margin-top: -4px;
                    color: darken(#fff, 40%);
                }

                :global(.comments-list) {
                    margin-bottom: 4px;
                }
            }

            &.is-reversed {
                flex-direction: row-reverse;

                .container {
                    flex-direction: row-reverse;
                    margin-right: 8px;

                    .el-textarea + small {
                        left: 0;
                        padding-left: 8px;
                    }

                    .content {
                        &.empty .text {
                            color: lighten(#6AC06F, 16%);
                        }

                        .text {
                            border-color: var(--primary-color);
                            background-color: var(--primary-color-lighter);
                            border-bottom-right-radius: 0;

                            &:before,
                            &:after {
                                border-width: 8px 0 0 6px;
                            }

                            &:before {
                                right: -6px;
                                bottom: -1px;
                                border-left-color: var(--primary-color);
                            }

                            &:after {
                                right: -5px;
                                bottom: -1px;
                                border-left-color: var(--primary-color-lighter);
                            }

                            .tag {
                                position: relative;
                                
                                &:before,
                                &:after {
                                    content: '';
                                    position: absolute;
                                    width: 0;
                                    height: 0;
                                    border-width: 0;
                                    border-style: solid;
                                    border-color: transparent;
                                    border-width: 8px 0 0 6px;
                                }

                                &:after {
                                    right: -13px;
                                    bottom: -9px;
                                    border-left-color: #fff;
                                }
                            }

                            .border {
                                position: absolute;
                                height: 100%;
                                width: 80%;
                                top: 0;
                                right: -5px;
                                border-bottom: 1px solid var(--primary-color);
                            }
                        }
                    }
                }

                > *:not(.ui-avatar):not(.container) {
                    margin-right: 48px;
                }

                .children {
                    align-items: flex-end;

                    > .el-button:not(.is-loading):after {
                        content: '—';
                    }
                }
            }

            &:not(.is-reversed) {
                .ui-avatar {
                    &.el-loading-parent--relative {
                        :global(.el-loading-spinner .circular) {
                            margin-left: -5px;
                        }
                    }
                }

                .container {

                    .content {
                        &.empty .text {
                            color: darken(#fff, 40%);
                        }

                        .text {
                            border-color: darken(#fff, 5%);
                            border-bottom-left-radius: 0;

                            &:before,
                            &:after {
                                border-width: 0 0 8px 6px;
                            }

                            &:before {
                                left: -6px;
                                bottom: -1px;
                                border-bottom-color: darken(#fff, 5%);
                            }

                            &:after {
                                left: -4px;
                                bottom: 0;
                                border-bottom-color: darken(#fff, 1%);
                            }
                        }
                    }
                }

                > *:not(.ui-avatar):not(.container) {
                    margin-left: 48px;
                    padding: 2px 0;
                }

                .children {
                    align-items: flex-start;

                    > .el-button:not(.is-loading):before {
                        content: '—';
                    }
                }
            }
        }
    }

</style>
