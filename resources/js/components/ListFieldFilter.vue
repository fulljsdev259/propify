<template>
    <div class="list-field-filter">
        <el-dropdown placement="bottom" size="medium" trigger="click">
            <el-button size="mini" class="el-button--transparent">
                {{ $t('general.filters.fields') }}
            </el-button>
            <el-dropdown-menu slot="dropdown">
                <draggable v-model="data" v-bind="dragOptions" @end="onEnd" handle=".el-icon-rank">
                    <transition-group type="transition" name="flip-list">
                        <div 
                            class="field-item"
                            :key="field.label + index"
                            v-for="(field, index) in data"    
                        >
                            <span>
                                <i class="el-icon-rank"></i>
                                &nbsp;
                                {{ getLabel(field) }}
                            </span>
                            <el-switch 
                                v-model="selected[index]"
                                @change="handleChange($event, index)"
                            ></el-switch>
                        </div>    
                    </transition-group>
                </draggable>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>
<script>
    import draggable from 'vuedraggable';

    export default {
        data() {
            return {
                selected: [],
                isFixed: true,
                data: [],
            }
        },
        props: {
            fields: {
                type: Array,
                default: () => []
            }
        },
        components: {
           draggable,
        },
        computed: {
            dragOptions() {
                return {
                    animation: 200,
                    group: "description",
                    disabled: false,
                    ghostClass: "ghost"
                };
            },
        },
        methods: {
            onEnd() {
                this.$emit('order-changed', this.data);
            },
            handleChange(value, index) {
                this.selected[index] = value;
                let result = [];
                this.selected.forEach((item, index) => {
                    if(item === false)
                        result.push(this.fields[index].label);
                });
                this.$emit('field-changed', result);
            },
            getLabel(item) {
                if(item.label == undefined) {
                    if(item.actions != undefined) 
                        return this.$t(item.actions[0].title);
                }
                return this.$t(item.label);
            }
        },
        mounted() {
            this.selected = [];
            this.data = this.fields;
            this.fields.forEach((item, index) => {
                if(item.defaultHide !== undefined && item.defaultHide === true) {
                    this.selected.push(false);
                    this.handleChange(false, index);
                 }  else
                    this.selected.push(true);
            });
        },
    }
</script>
<style lang="scss" scoped>
    // .list-field-filter {
    //     margin: 0 5px;
    // }
    .flip-list-move {
         transition: transform 0.5s;
    }
    .no-move {
         transition: transform 0s;
    }
    .ghost {
        opacity: 0.5;
        background: var(--color-main-background-lighter);
    }
    .el-dropdown-menu {
        width: 250px;
        padding: 15px;
        border-radius: 12px;
        
        .field-item {
            display: flex;
            justify-content: space-between;
            cursor: pointer;
            &:not(:last-child) {
                margin-bottom: 5px;
            }
            span {
                cursor: pointer;
                user-select: none;
                i {
                    cursor: move;
                }
            }
            .el-switch {
                :global(.el-switch__core) {
                    width: 25px !important;
                    height: 14px;
                    &::after {
                        width: 10px;
                        height: 10px;
                    }
                }
                :global(&.is-checked .el-switch__core::after) {
                    margin-left: -11px;
                }
            }
        }
    }
</style>