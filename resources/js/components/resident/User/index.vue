<template>
    <div :class="['user', !onlyAvatar && {[`is-aligned-${contentPosition}`]: true}]">
        <ui-avatar :size="40" :src="loggedInUser.avatar" :name="loggedInUser.name" shadow="hover" @click="$emit('avatar-click')" />
        <div class="content" v-if="!onlyAvatar">
            <div class="name">{{loggedInUser.name}}</div>
            <div class="email">{{loggedInUser.email}}</div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: 'p-user',
        props: {
            onlyAvatar: {
                type: Boolean,
                default: false
            },
            contentPosition: {
                type: String,
                default: 'left',
                validator: value => ['left', 'right'].includes(value)
            }
        },
        computed: {
            ...mapGetters(['loggedInUser']),
            ...mapGetters('notifications', {
                unreadNotifications: 'unread'
            })
        }
    }
</script>

<style lang="scss" scoped>
    .user {
        display: flex;
        align-items: center;

        &.is-aligned-left {
            .content {
                order: -1;
                margin-right: 16px;
                text-align: right;
            }
        }

        &.is-aligned-right {
            .content {
                order: 0;
                margin-left: 16px;
                text-align: left;
            }
        }

        .content {
            line-height: 1.48;

            .name {
                color: var(--color-primary);
                font-weight: 600;
            }
            .email {
                color: var(--color-text-placeholder);
            }
        }
    }
</style>