<template>
    <div class="profile">
        <heading :title="$t('general.profile.pageTitle')" class="custom-heading" icon="ti-user" shadow="heavy" />
        <div class="crud-view">
            <el-tabs v-model="active">
                <el-tab-pane :label="$t('general.profile.profile')" :lazy="true" name="profile">
                    <profile/>
                </el-tab-pane>
                <el-tab-pane :label="$t('general.account')" :lazy="true" name="account">
                    <accounts/>
                </el-tab-pane>
                <el-tab-pane :label="$t('general.notifications')" :lazy="true" name="notifications">
                    <notifications/>
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Profile from './Profile';
    import Accounts from './Account';
    import Notifications from './Notifications';


    export default {
        name: 'AdminSettings',
        components: {
            Heading,
            Profile,
            Accounts,
            Notifications
        },
        data() {
            return {
                active: 'profile'
            }
        }
    }
</script>

<style lang="scss" scoped>
    .profile {
        height: 77vh !important;

        .custom-heading {
            margin-bottom: 2em;
        }

        .el-tabs {
            .el-tab-pane {
                padding: 2px;
            }

            // :global(.el-tabs__header) {
            //     background-color: #fff;
            //     box-shadow: 0 1px 3px transparentize(#000, .88),
            //                 0 1px 2px transparentize(#000, .76);
            // }
        }
    }
</style>
