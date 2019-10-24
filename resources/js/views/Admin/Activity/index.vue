<template>
    <div class="activity">
        <heading :title="$t('general.audits')" icon="icon-chat-empty" shadow="heavy" />
        <el-row :gutter="20" class="crud-view">
            <el-col :md="24">
                <el-card class="mt15">                    
                    <audit showFilter type="all" filterPosition="left" />
                </el-card>
            </el-col>            
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';

    export default {
        components: {
            Heading            
        },
    };
</script>