<template>
    <div class="services list-view">
        <heading icon="icon-user" :title="$t('general.admin_menu.admins')" shadow="heavy" class="padding-right-300">  
            <template>
                <list-field-filter :fields="header" @field-changed="fields=$event"></list-field-filter>
            </template>          
            <template v-if="$can($permissions.create.user)">
                <el-button 
                    @click="add" 
                    icon="ti-plus" 
                    size="mini" 
                    class="el-button--transparent"
                >
                    {{$t('general.actions.add')}} {{ $t('general.roles.administrator') }}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.user)">
                <el-button 
                    :disabled="!selectedItems.length" 
                    @click="batchDeleteWithIds" 
                    icon="ti-trash" 
                    size="mini"
                    class="el-button--transparent"
                >
                    {{$t('general.actions.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="headerFilter"
            :items="items"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
        >
        </list-table>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';

    const mixin = ListTableMixin({
        actions: {
            get: 'getUsers',
            delete: 'deleteUserWithIds'
        },
        getters: {
            items: 'users',
            pagination: 'usersMeta'
        }
    });

    export default {
        name: 'AdminUsers',
        components: {
            Heading
        },
        mixins: [mixin],
        data() {
            return {
                header: [{
                    label: 'general.name',
                    prop: 'name'
                }, {
                    label: 'general.email',
                    prop: 'email'
                }, {
                    label: 'general.phone',
                    prop: 'phone'
                }, {
                    width: 120,
                    actions: [/*{
                        type: '',
                        icon: 'ti-search',
                        title: 'general.actions.edit',
                        onClick: this.edit,
                        editUrl: 'adminUsersEdit',
                        permissions: [
                            this.$permissions.update.user
                        ]
                    }*/]
                }],
            }
        },
        computed: {
            filters() {
                return [
                    {
                        name: this.$t('general.placeholders.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    }
                ]
            },
        },
        methods: {

            edit({id}) {
                this.$router.push({
                    name: 'adminUsersEdit',
                    params: {
                        id
                    }
                });
            },
            add() {
                this.$router.push({
                    params: {
                        type: 'new'
                    },
                    name: 'adminUsersAdd'
                });
            },
        }
    }
</script>
