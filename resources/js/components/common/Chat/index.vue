<template>
    <div class="chat">        
        <comments ref="comments" :newStyle="newStyle" :id="id" :type="type" :limit="limit" reversed with-scroller :show-children="false" :style="{height: height, maxHeight: maxHeight}" />
        <add-comment ref="addComment" :id="id" :type="type" :show-templates="showTemplates" :newStyle="newStyle"/>
    </div>
</template>

<script>
    import ErrorFallback from 'components/common/Chat/Error'

    export default {
        props: {
            id: {
                type: Number,
                required: true
            },
            type: {
                type: String,
                required: true,
                validator: type => ['pinboard', 'listing', 'request', 'conversation', 'internalNotices'].includes(type)
            },
            height: {
                type: String,
                default: '100%'
            },
            maxHeight: {
                type: String,
                default: '320px'
            },
            limit: {
                type: Number,
                default: 50
            },
            autofocus: {
                type: Boolean,
                default: false
            },
            showTemplates: {
                type: Boolean,
                default: false
            },
            newStyle: {
                type: Boolean,
                default: false,
            }
        },
        data () {
            return {
                errorFallback: ErrorFallback
            }
        },
        methods: {
            focusOnAddComment() {
                this.$refs.addComment.focus()
            }
        },
        mounted () {
            if (this.autofocus) {
                this.focusOnAddComment()
                
            }
        }
    }
</script>

<style lang="sass" sccoped>
    .chat
        height: 100%
        display: flex
        flex-direction: column

        .comments-list 
            /deep/ .vue-recycle-scroller
                padding-bottom: 8px

                // /deep/ .vue-recycle-scroller__slot)
                //     :global(.el-divider) 
                //         margin-top: 8px

        .add-comment
            width: auto

    .switcher
        .el-form-item__content
            display: flex
            align-items: center
        &__label
            line-height: 1.4em
            color: #606266
        &__desc
            margin-top: 0.5em
            display: block
            font-size: 0.9em
        .el-switch
            margin-left: auto

    .card-boxs span.switcher__desc
        text-align: left
        font-weight: normal
        margin-top: 10px
        line-height: 20px
        font-size: 13px
        color: #333

    .switcher-frist .el-switch
        margin-top: 10px
</style>

<style lang="scss" scoped>
    // .chat {
    //     height: 100%;
    //     color: lighten(#000, 32%);
    //     display: flex;
    //     flex-direction: column;

    //     .comments-list {
    //         :global(.vue-recycle-scroller) {
    //             padding-bottom: 8px;

    //             :global(.vue-recycle-scroller__slot) {
    //                 :global(.el-divider) {
    //                     margin-top: 8px;
    //                 }
    //             }
    //         }
    //     }
    //     .add-comment {
    //         width: auto;
    //     }
    // }
</style>
