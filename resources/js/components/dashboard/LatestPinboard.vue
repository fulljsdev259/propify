<template>
    <div class="latest-pinboard dashboard-table">
        <div class="link-container">
            <router-link :to="{name: 'adminPinboard'}">
                <span class="title">{{ $t('dashboard.pinboard.go_to_pinboard') }} </span>
                <i class="icon-right icon"/>
            </router-link>
        </div>
        <list-table
            :header="header"
            :items="latestPinboard"
            :loading="{state: loading}"
            @selectionChanged="selectionChanged"
        >
        </list-table>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import DashboardListMixin from 'mixins/DashboardListMixin';
    
    const mixin = DashboardListMixin();

    export default {
        mixins: [mixin],
        props: {
          type: {
            type: String
          }
        },
        data() {
            return {
                header: [{
                    type: 'pinboard-title',
                    label: 'models.pinboard.title',
                    props: ['content', 'user'],
                    minWidth: '300px'
                }, {
                    type: 'tag',
                    width: 120,
                    label: 'models.pinboard.status.label',
                    prop: 'status_label',
                    classSuffix: 'status'
                }, {
                    type: 'counts',
                    label: 'dashboard.pinboard.counts',
                    counts: [{
                            prop: 'comments_count',
                            background: '#bbb',
                            color: '#fff',
                            label: 'models.pinboard.comments'
                        }, {
                            prop: 'likes_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: 'models.pinboard.likes'
                        }
                    ]
                }, {
                    type: 'actions',
                    label: 'dashboard.actions',
                    width: 130,
                    actions: [ 
                        {
                            type: 'default',
                            title: 'general.actions.edit',
                            editUrl: 'adminPinboardEdit',
                            permissions: [
                                this.$permissions.update.pinboard
                            ]
                        }
                    ]
                }],
                items: []
            };
        },
        computed: {
            ...mapGetters(['latestPinboard']),
        },
        methods: {
            ...mapActions(['getPinboards']),
            async fetchData() {
                const pinboards = await this.getPinboards({
                    per_page: 5
                });
                this.items = pinboards.data.data
            },
         },
        created() {
            this.fetchData();
        }
    }
</script>
