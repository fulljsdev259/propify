<template>
    <div :class="['likes', {[`${layout}-layout`]: true}]" v-if="data.length">
        <div class="users">
            <ui-avatar v-for="user in this.sortedUsers.slice(0, limit)" :key="user.id" :src="user.avatar" :name="user.name" :size="28" />
            <div class="more" v-if="data.length > limit">{{'+' + (data.length - limit)}}</div>
        </div>
        <div class="content">
            {{text}}
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: 'p-likes',
        props: {
            type: {
                type: String,
                required: true,
                validator: value => ['pinboard', 'listing'].includes(value)
            },
            data: {
                type: Array,
                default: [],
                required: true
            },
            layout: {
                type: String,
                default: 'row',
                validator: value => ['row', 'column'].includes(value)
            },
            limit: {
                type: Number,
                default: 3,
                validator: value => value >= 1
            },
            suffix: {
                type: String,
                default: 'resident.liked_this'
            }
        },
        computed: {
            ...mapGetters(['loggedInUser']),

            sortedUsers () {
                if (this.data.length) {
                    return [...this.data].sort((a, b) => {
                        if (a.id === this.loggedInUser.id) {
                            return -1
                        }

                        return 0
                    })
                }
            },
            text () {
                if (this.data.length) {
                    return [[...this.sortedUsers.slice(0, this.limit)], [...this.sortedUsers.slice(this.limit)]].reduce((text, users, index) => {
                        if (index === 0) {
                            text += users.map(({id, name}) => id === this.loggedInUser.id ? this.$t('resident.you') : name.split(' ')[0]).join(', ')
                        } else if (users.length > 1) {
                            text += ` ${this.$t('resident.and')} ${users.length} ` + this.$t('resident.welcome_others')
                        } else if (users.length) {
                            text += ` ${this.$t('resident.and')} ${users.length} ` + this.$t('resident.welcome_other')
                        } else {
                            text = text.replace(/,(?=[^,]*$)/, ' ' + this.$t('resident.and'))
                        }

                        return text
                    }, '') + ` ${this.$t(this.suffix)}`
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .likes {
        display: flex;
        align-items: center;
        color: grey;
        padding: 10px;
        
        &.row-layout {
            flex-direction: row;

            .content {
                margin-left: 4px;
            }
        }

        &.column-layout {
            flex-direction: column;
            justify-content: center;

            .content {
                margin-top: 4px;
            }
        }

        .users {
            display: flex;
            align-items: center;

            .ui-avatar {
                border: 2px #fff solid;

                &:not(:first-child) {
                    margin-left: -12px;
                }
            }

            .more {
                width: 28px;
                height: 28px;
                background-color: #fff;
                color: var(--color-primary);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                margin-left: -12px;
            }
        }
    }
</style>