<template>
    <div class="like">
        <template v-if="readonly">
            <div>
                <i :class="icons.like"></i> {{status.likes_count}}
            </div>
        </template>
        <template v-else>
            <el-button ref="button" type="text" :icon="status.liked ? icons.unlike : icons.like" :class="{active: status.liked}" :loading="loading" @click="handleLike">
                <template v-if="showText">
                    <template v-if="status.liked">{{$t('resident.unlike')}}</template>
                    <template v-else>{{$t('resident.like')}}</template>
                </template>
                <template v-if="showWelcomeText">
                    <template v-if="status.liked">{{$t('resident.unwelcome')}}</template>
                    <template v-else>{{$t('resident.welcome',{name})}}</template>
                </template>
            </el-button>
        </template>
        <slot />
    </div>
</template>

<script>
    export default {
        name: 'p-like',
        props: {
            id: {
                type: Number,
                required: true
            },
            name: {
                type: String
            },
            type: {
                type: String,
                required: true,
                validator: type => ['pinboard', 'listing'].includes(type)
            },
            icons: {
                type: Object,
                default: () => ({
                    like: 'icon-thumbs-up',
                    unlike: 'icon-thumbs-down'
                })
            },
            showText: {
                type: Boolean,
                default: true
            },
            showWelcomeText: {
                type: Boolean,
                default: false
            },
            readonly: {
                type: Boolean,
                default: false
            }
        },
        data () {
            return {
                loading: false,

            }
        },
        methods: {
            async handleLike () {
                this.loading = true

                switch (this.type) {
                    case 'pinboard':
                        await this.$store.dispatch(`newPinboard/${this.status.liked ? 'unlike' : 'like'}`, {id: this.id})

                        break
                    case 'listing':
                        await this.$store.dispatch(`newListings/${this.status.liked ? 'unlike' : 'like'}`, {id: this.id})

                        break
                }

                this.loading = false
            }
        },
        computed: {
            status () {
                const {liked = false, likes_count = 0} = this.$store.getters[{
                    pinboard: `newPinboard/getById`,
                    listing: `newListings/getById`
                }[this.type]](this.id) || {}

                return {liked, likes_count}

                // return {
                //     liked: false,
                //     likes_count: 0
                // }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .like {
        display: flex;
        align-items: center;

        > :global(div) {
            color: var(--color-primary);
            display: flex;
            align-items: center;

            :global([class*=icon]) {
                vertical-align: middle;
                margin-right: 4px;
            }

            &:not(:last-of-type) {
                margin-right: 4px;

                &:after {
                    content: '\2022';
                    margin-left: 4px;
                }
            }
        }

        .el-button {
            padding: 0;
            position: relative;

            :global([class*=icon]) {
                filter: opacity(.8);
                transition: filter .48s ease;

                & + :global(span) {
                    margin-left: 5px;
                }
            }

            &:hover :global([class*=icon]) {
                filter: opacity(1);
            }
        }
    }
</style>