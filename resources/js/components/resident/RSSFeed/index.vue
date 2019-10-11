<template>
    <el-card>
        <template #header v-if="title">{{title}}</template>
        <el-collapse v-model="active" accordion>
            <el-collapse-item v-for="(item, idx) in items" :key="item.guid" :name="idx">
                <div slot="title" class="content">
                    <div class="title">{{item.title}}</div>
                    <div class="date">
                        {{item.pubDate}}
                    </div>
                </div>
                {{item.contentSnippet || 'No description available.'}}
                <el-button size="mini" round @click="readMore(item.link)">{{$t('resident.read_more')}}</el-button>
            </el-collapse-item>
        </el-collapse>
    </el-card>
</template>

<script>
    import {API_BASE_URL} from 'config'
    import axios from 'boot/axios'
    import AgoMixin from 'mixins/agoMixin'
    import {displayError} from 'helpers/messages'
    import Parser from 'rss-parser'

    export default {
        name: 'p-rss-feed',
        mixins: [AgoMixin],
        props: {
            limit: {
                type: Number,
                default: 5
            },
            title: {
                type: String,
                default: ''
            }
        },
        data () {
            return {
                active: 0,
                items: []
            };
        },
        methods: {
            readMore (link) {
                window.open(link, '_blank');
            }
        },
        async created () {
            let parser = new Parser({
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
            });

            try {
                const {items} = await parser.parseURL(API_BASE_URL + 'pinboard/rss.xml')

                this.items = items.filter((item, idx) => {
                    item.pubDate = this.ago(item.pubDate, this.$i18n.locale)

                    if (idx <= this.limit - 1) {
                        return item;
                    }
                });
            } catch (error) {
                displayError(error)
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
         :global(.el-card__header) {
            height: 64px;
            font-size: 20px;
            font-weight: 600;
            color: var(--primary-color);
            flex: auto;
            overflow: hidden;
            min-width: 0;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: flex;
            align-items: center;
        }

        :global(.el-card__body) {
            padding: 0;
            .el-collapse {
                border-style: none;
                .el-collapse-item {
                    :global(.el-collapse-item__header) {
                        background: transparent;

                        .content {
                            display: flex;
                            flex-direction: column;
                            line-height: 1.48;
                            overflow: hidden;
                            padding: 0 16px;

                            .title {
                                color: var(--color-primary);
                                flex: 1;
                                font-weight: bold;
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                            }

                            .date {
                                font-size: 12px;
                            }
                        }
                    }

                    :global(.el-collapse-item__wrap) {
                        background: transparent;

                        :global(.el-collapse-item__content) {
                            color: darken(#fff, 56%);
                            padding: 0 16px 16px 16px;
                            .el-button {
                                width: 100%;
                                margin-top: 1em;
                            }
                        }
                    }
                }
            }
        }
    }
</style>
