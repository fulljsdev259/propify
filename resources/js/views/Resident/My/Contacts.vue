<template>
    <placeholder :size="256" :src="require('img/5ceaae95848f2.png')" v-if="!this.loader.visible && !this.contacts.length">
        {{$t('resident.no_data.contact')}}
        <small>{{$t('resident.no_data_info.contact')}}</small>
    </placeholder>
    <div class="contacts" v-else-if="contacts.length">
        <ui-heading icon="ti-book" :title="$t('resident.my_contacts')">
            <div slot="description">{{$t('resident.heading_info.my_contact')}}</div>
        </ui-heading>
        <ui-divider />
        <el-card>
            <el-tabs tab-position="left">
                <el-tab-pane v-for="contact in contacts" :key="contact.id" :label="contact.name">
                    <div class="title">
                        <small>{{$t('resident.category')}}</small>
                        {{$t(`models.service.${contact.category}`)}}
                    </div>
                    <div class="title">
                        <small>{{$t('resident.name')}}</small>
                        {{contact.name}}
                    </div>
                    <div class="title">
                        <small>{{$t('resident.email')}}</small>
                        {{contact.email}}
                    </div>
                    <div class="title">
                        <small>{{$t('resident.contact_phone')}}</small>
                        {{contact.phone}}
                    </div>
                </el-tab-pane>
            </el-tabs>
        </el-card>
    </div>
</template>

<script>
    import Heading from 'components/Heading'
    import {displayError} from 'helpers/messages'
    import Placeholder from 'components/Placeholder'
    import VueSticky from 'vue-sticky'

    export default {
        components: {
            Heading,
            Placeholder
        },
        directives: {
            sticky: VueSticky
        },
        data() {
            return {
                contacts: [],
                loader: {
                    visible: true
                }
            }
        },
        async mounted () {
            this.loader = this.$loading({
                target: this.$el.parentElement,
                text: this.$t('resident.fetching_message.contact')
            })

            try {
                const {service_providers} = await this.$store.dispatch('getBuilding', {id: this.$store.getters.loggedInUser.resident.building_id})

                this.contacts = service_providers
            } catch (err) {
                displayError(err)
            } finally {
                this.loader.close()
            }
        }
    }
</script>

<style lang="scss" scoped>
   .placeholder {
        height: 100% !important;
        font-size: 20px;
        color: var(--color-main-background-darker);
        small {
            font-size: 72%;
            color: var(--primary-color-lighter);
        }
    }
    .contacts {
         &:before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/5ceac43fdb386.png');
            background-repeat: no-repeat;
            background-position: 100% 100%;
            width: 100%;
            height: 100%;
            opacity: .08;
            pointer-events: none;
        }

        .el-card {
            max-width: 1024px;
            position: relative;
            z-index: 1;

            &.heading {
                :global(.el-card__body) {
                    padding: 12px 16px;
                    .heading div {
                        color: darken(#fff, 40%);
                    }
                }
            }

            .el-tabs {
                .title {
                    color: #000;

                    small {
                        display: block;
                        color: darken(#fff, 48%);
                    }

                    &:not(:last-child) {
                        margin-bottom: 8px;
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 676px) {
        .contacts {
            /deep/ .ui-heading__content__description {
                display: none
            }
        }
    }
</style>
