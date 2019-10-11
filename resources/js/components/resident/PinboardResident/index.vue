<template>
    <div class="user">
        <ui-avatar :name="data.user.name" :size="42" :src="data.user.avatar" />
        <div class="name">
            {{data.user.name}}
            <small>
                {{formatDatetime(data.created_at)}}
            </small>
        </div>
        <div class="actions" v-if="showActions && loggedInUser.id == data.user_id && !data.announcement">
            <el-tooltip :content="$t('resident.tooltips.edit_pinboard')">
                <el-button size="mini" icon="icon-pencil" @click="$emit('edit-pinboard')" plain round></el-button>
            </el-tooltip>
            <el-tooltip :content="$t('resident.tooltips.delete_pinboard')">
                <el-button size="mini" icon="icon-trash-empty" @click="$emit('delete-pinboard')" plain round></el-button>
            </el-tooltip>
            
        </div>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex'
    import {format, isSameDay} from 'date-fns'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'

    export default { 
        mixins: [
            FormatDateTimeMixin,
        ],
        props: {
            data: {
                type: Object,
                required: true
            },
            showActions : {
                type: Boolean,
                default: true
            },
        },
        data() {
            return {
              
            }
        },
        methods: {
        },
        computed: {
            ...mapGetters(['loggedInUser']),

      
        },
        async mounted () {
          
        }
    }
</script>

<style lang="scss" scoped>
    .user {
        display: flex;
        align-items: center;

        .name {
            font-size: 16px;
            line-height: 1.32;
            margin-left: 1em;

            small {
                font-size: 80%;
                display: block;
                color: darken(#fff, 48%);
            }
        }

        .actions {
            flex-grow: 1;
            display: flex;
            justify-content: flex-end;

            button {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                padding: 0;
            }
        }
    }

</style>
