<template>
    <div class="list-field-filter">
        <el-dropdown placement="bottom" size="medium" trigger="click">
            <el-button type="primary" round size="mini">
                Field
            </el-button>
            <el-dropdown-menu slot="dropdown">
                <div 
                    class="field-item"
                    :key="field + index"
                    v-for="(field, index) in fields"    
                >
                    <span>{{ getLabel(field) }}</span>
                    <el-switch 
                        v-model="selected[index]"
                        @change="handleChange($event, index)"
                    ></el-switch>
                </div>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                selected: [],
            }
        },
        props: {
            fields: {
                type: Array,
                default: () => []
            }
        },
        components: {
           
        },
        computed: {
            filteredOptions() {
                return this.options.filter((option) => this.search ==='' || this.search !== '' && option.name.includes(this.search));
            },
            findSelectedOne() {
                let first_item = null;
                let count = 0;
                let idx = -1;
                this.items.forEach((item, index) => {
                    if(item.selected) {
                        count ++;
                        if(count == 1) {
                            first_item = item;
                            idx = index;
                        }
                    }
                });
                return { 
                    label:first_item?first_item.name:null,
                    index: idx, 
                    count: count
                };
            },
        },
        methods: {
            handleChange(value, index) {
                this.selected[index] = value;
                let result = [];
                this.selected.forEach((item, index) => {
                    if(item === false)
                        result.push(index);
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
            this.fields.forEach((item) => {
                this.selected.push(true);
            });
        }
    }
</script>
<style lang="scss" scoped>
    .list-field-filter {
        margin: 0 5px;
    }
    .el-dropdown-menu {
        width: 250px;
        padding: 15px;
        border-radius: 12px;
        
        .field-item {
            display: flex;
            justify-content: space-between;
            &:not(:last-child) {
                margin-bottom: 5px;
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