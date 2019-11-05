<template>
    <div>
        <el-table
            :data="items"
            style="width: 100%"
            v-if="items && items.length"
            :show-header="false"
            >
            <el-table-column
                prop="collection_name"
            >
                <template slot-scope="scope">
                    <strong>{{$t(`models.building.media_category.${scope.row.collection_name}`)}}</strong>
                </template>
            </el-table-column>
            <el-table-column
                align="left"
            >
                <template slot-scope="scope" >
                    <a :href="scope.row.url" class="file-name" target="_blank">
                        {{scope.row.name}}
                    </a>
                </template>
            </el-table-column>
            <el-table-column
                width="70"
                align="right"
            >
                <template slot-scope="scope">
                    <el-tooltip
                        :content="$t('general.actions.delete')"
                        class="item" effect="light" placement="top-end"
                    >
                        <el-button icon="icon-trash-empty" type="danger" round @click="deleteFile(scope.$index)" size="mini"/>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>

    import uuid from 'uuid/v1'
    import {mapActions, mapGetters} from 'vuex';

    import {ResponsiveMixin} from 'vue-responsive-components'
    

    export default {
        name: 'BuildingFileListTable',
        mixins: [
            ResponsiveMixin
        ],
        props: {
            items: {
                type: Array,
                default: () => {
                    return [];
                }
            },
        },
        data () {
            return {
                clicks: [],
                delay: 700
            }
        },
        methods: {
            deleteFile( index ) {
                this.clicks[index] ++
                if(this.clicks[index] === 1) {
                    this.$emit('delete-document', 'media', index)
                    this.timer = setTimeout(() => {
                        this.clicks[index] = 0
                    }, this.delay);
                }
            }
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
        },
        mounted() {
            this.clicks = this.items.map(item => 0)
        },
        watch: {
            'items': {
                immediate: false,
                handler () {
                    // TODO - auto blur container if visible is true first
                    this.clicks = this.items.map(item => 0)
                }
            }
        },
    }
</script>

<style lang="scss" scoped>

    /deep/ .file-name {
        color: var(--primary-color);
        text-decoration: none;
    }

    /deep/ .file-name:hover {
        text-decoration: none;
        color: var(--primary-color-lighter);
    }
    
</style>
