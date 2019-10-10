<template>
    <span class="btn-wrap">
        <el-tooltip
            :content="$t('models.tenant.status.label')"
            class="item"
            effect="light" placement="top">
            <template >
                <el-button :style="{
                    'padding': '2px 5px',
                    'border-color': getRequestStatusColor(setStatus(status), 'name'),
                    'color' : getRequestStatusColor(setStatus(status), 'name')
                    }" plain :type="setStatus(status)" class="btn-priority-badge btn-hover" size="mini" round>{{ $t(('models.request.status.'+ setStatus(status))) }}</el-button>
            </template>
        </el-tooltip>
    </span>
</template>

<script>
import globalFunction from "helpers/globalFunction";
import { mapGetters} from 'vuex';
export default {
    mixins: [globalFunction],
     props: {
        status: {
            type: Number,
            required: true
        }
     },
    computed:{
        ...mapGetters('application', {
                constants: 'constants'
            })
    },
    methods: {
        setStatus(id){
            return this.constants.requests.status[id];
        }
    }
}
</script>

<style lang="scss" scoped>
   .btn-hover:hover{
        text-decoration: none;
        cursor: text;
    }
</style>

