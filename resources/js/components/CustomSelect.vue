<template>
    <div class="custom-select">
       <el-dropdown trigger="click" placement="bottom-start">
            <el-button type="primary">
                {{ placeholder }}
            </el-button>
            <el-dropdown-menu slot="dropdown">
                <el-input
                    v-model="search"
                    placeholder="Search..."
                >
                    <i 
                        v-if="search === ''"
                        slot="prefix" 
                        class="el-input__icon el-icon-search">
                    </i>
                    <i 
                        v-else
                        slot="prefix" 
                        class="el-input__icon el-icon-close" 
                        @click="clearSearch()">
                    </i>
                </el-input>
                <div class="dropdown-container">
                    <div
                        :key="placeholder + option.id"
                        class="dropdown-item" 
                        :class="[{'selected': selected(index) !== -1}]"
                        @click="selectItem(index)"
                        v-for="(option, index) in options" 
                        :style="{display: option.name.includes(search)? 'flex':'none'}"
                    >
                        <span v-html="filterSearch(option.name)"></span>
                        <span
                            v-if="selected(index) !== -1" 
                            class="el-icon-check"
                        >
                        </span>
                    </div>
                </div>
                <div class="actions">
                    <el-button type="text" @click="handleReset()">Reset</el-button>
                    <el-button type="primary" @click="handleSelect()">Select</el-button>
                </div>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>
<script>
    import {Avatar} from 'vue-avatar';

    export default {
        name: 'ContractCount',
        data() {
            return {
               selectedItems: [],
               search: '',
            }
        },
        props: {
          options: {
              type: Array,
              default: () => []
          },
          placeholder: {
              type: String,
              default: () => ''
          }
        },
        components: {
           
        },
        computed: {
            filteredOptions() {
                return this.options.filter((option) => this.search ==='' || this.search !== '' && option.name.includes(this.search));
            }
        },
        methods: {
            clearSearch() {
                this.search = '';
            },
            hasSearch() {
                return this.options.filter((option) => this.search ==='' || this.search !== '' && option.name.includes(this.search));
            },
            selectItem(index) {
                let pos = this.selected(index);
                if(pos === -1)
                    this.selectedItems.push(index);
                else   
                    this.selectedItems.splice(pos, 1);
            },
            selected(index) {
                return this.selectedItems.indexOf(index);
            },
            filterSearch(name) {
                let result = name;
                if(this.search !== '') {
                    let pos = name.indexOf(this.search);
                    if(pos !== -1) {
                        result = `${name.slice(0, pos)}<b>${this.search}</b>${name.slice(pos + this.search.length)}`;
                    }
                }
                return result;
            },
            handleReset() {
                this.selectedItems = [];
            },
            handleSelect() {
                this.$emit('select-changed', this.selectedItems);
            }
        }
        
    }
</script>
<style lang="scss" scoped>
    .el-dropdown-menu {
        .el-input {
            margin: 16px;
            width: auto;
            :global(.el-input__inner) {
                border-color: transparent;
                color: var(--color-text-regular);
                background-color: var(--background-color-base);
            }
            :global(.el-input__icon) {
                font-size: 16px;
            }
        }
        .dropdown-container {
            padding: 0 16px;
            .dropdown-item {
                padding: 6.5px;
                margin-top: 5px;
                margin-bottom: 5px;
                border: 1px solid var(--border-color-base);
                border-radius: 4px;
                cursor: pointer;

                &:hover {
                    background-color: var(--background-color-base);
                }
                &.selected {
                    border: 1px solid var(--color-black);
                    font-weight: 700;
                    display: flex;
                    justify-content:space-between;
                }
            }
        }
        .actions {
            padding: 16px;
            text-align: right;
            .el-button--text {
                &:hover {
                    color: var(--primary-color);
                }
            }
            .el-button {
                &:first-child {
                    width: 40%;
                }
                &:last-child {
                    width:50%;
                }
            }
        }
    }

</style>