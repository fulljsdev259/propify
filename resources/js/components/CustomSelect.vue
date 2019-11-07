<template>
    <div class="custom-select">
       <el-dropdown trigger="click" placement="bottom-start" @visible-change="handleVisibleChange">
            <el-button @click="handleDropdownClick" :class="[{'selected-button': findSelectedOne.count}]">
                <span v-if="findSelectedOne.count === 0">{{ placeholder }}</span>
                <el-tag 
                    v-else
                    size="mini"
                    closable
                    @close="selectItem(findSelectedOne.index, true)"
                >
                    {{ $t(`models.request.category_list.${findSelectedOne.label}`) }}
                </el-tag>
                <el-tag
                    v-if="findSelectedOne.count > 1"
                    size="mini"    
                >
                    +{{ findSelectedOne.count - 1 }}
                </el-tag>
                <span v-if="findSelectedOne.count != 0" class="dropdown-icon el-icon-close" @click.prevent.stop="handleReset(true)"></span>
            </el-button>
            <el-dropdown-menu slot="dropdown">
                <el-input
                    v-model="search"
                    :placeholder="`${$t('general.placeholders.search')}...`"
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
                        :key="placeholder + item.id + 'selected'"
                        class="dropdown-item" 
                        :class="[{'selected': item.selected == true, 'hide-unmatching': !isContained($t(`models.request.category_list.${item.name}`))}]"
                        @click="selectItem(index)"
                        v-for="(item, index) in items" 
                    >
                        <span v-html="filterSearch($t(`models.request.category_list.${item.name}`))"></span>
                        <span class="el-icon-check"></span>
                    </div>
                    
                </div>
                <el-divider></el-divider>
                <div class="actions">
                    <el-button type="text" @click="handleReset()">{{ $t('general.reset') }}</el-button>
                    <el-button v-if="!isChanged()" type="text" :disabled="true">{{ $t('general.placeholders.select') }}</el-button>
                    <el-button v-else type="primary"><el-dropdown-item >{{ $t('general.placeholders.select') }}</el-dropdown-item></el-button>
                </div>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>
<script>
    export default {
        name: 'CustomSelect',
        data() {
            return {
               originItems: [],
               selectClicked: false,
               search: '',
               selecting: false,
               items: [],
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
          },
          selectedOptions: {
              type: Array,
              default: () => []
          },
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
            isContained(str) {
                return str.toLowerCase().includes(this.search.toLowerCase());
            },
            handleDropdownClick() {
                this.clearSearch();
            },
            handleVisibleChange(visible) {
                let selected = [], unselected = [];
                if(visible) {
                    this.selecting = visible;
                    this.items.forEach((item) => {
                        if(item.selected) {
                            selected.push(item);
                        } else {
                            unselected.push(item);
                        }
                    });
                    unselected.sort((a ,b ) => {
                        return a.id - b.id;
                    });
                    this.items = selected.concat(unselected);
                    this.originItems = [];
                    this.items.forEach((item) => {
                        this.originItems.push({id:item.id, name:item.name, selected:item.selected});
                    });
                }
                if(!visible)
                    this.handleSelect();
            },
            clearSearch() {
                this.search = '';
            },
            isChanged() {
                let result = false;
                console.log(this.selecting);
                console.log(this.originItems, this.items);
                if(this.selecting) {
                    for(let i = 0; i < this.items.length; i++)
                        if(this.items[i].selected != this.originItems[i].selected)
                            result = true;
                }
                console.log(result);
                return result;
            },
            hasSearch() {
                return this.options.filter((option) => this.search ==='' || this.search !== '' && option.name.includes(this.search));
            },
            selectItem(index, notifyChange = false) {
                this.items[index].selected = !this.items[index].selected;
                if(notifyChange)
                    this.handleSelect();
            },
            selected(index) {
                return this.items[index].selected;
            },
            filterSearch(name) {
                let result = name;
                if(this.search !== '') {
                    let pos = name.toLowerCase().indexOf(this.search.toLowerCase());
                    if(pos !== -1) {
                        result = `${name.slice(0, pos)}<b>${name.slice(pos, pos + this.search.length)}</b>${name.slice(pos + this.search.length)}`;
                    }
                }
                return result;
            },
            handleReset(notifyChange = false) {
                this.items.forEach((item)=> {
                    item.selected = false;
                    this.originItems.push({id:item.id, name:item.name, selected:item.selected});
                });
                this.handleSelect();
            },
            handleSelect() {
                let result = [];
                this.selectClicked = true;
                this.items.forEach((item) => {
                    if(item.selected) {
                        result.push(item.id);
                    } 
                });
                this.$emit('select-changed', result);
            },
        },
        created() {
            this.options.forEach((option) => {
                this.items.push({
                    id: option.id,
                    name: option.name,
                    selected: this.selectedOptions.includes(option.id),
                })
            });

        }
    }
</script>
<style lang="scss" scoped>
    .el-dropdown {
        width: 100%;
        position: relative;
        .el-button {
            padding: 0 10px;
            width: 100%;
            text-align: left;
            color: var(--color-text-primary);
            height: 40px;
            position: relative;
            background-color: var(--background-color-base);
            border-color: transparent;

            &:hover, &:focus {
                background-color: var(--color-main-background-base);
            }
            &.selected {
                background-color: var(--color-text-primary);
            }

            .dropdown-icon {
                position: absolute;
                right: 6px;
                top: 13px;
                color: var(--color-text-placeholder);
            }
            span.el-tag {
                background-color: var(--color-white);
            }
            &.selected-button {
                background-color: var(--color-primary);
            }

        }
    }
    .el-dropdown-menu {
        margin-bottom: 0px;
        padding: 0px;
        width: 288px;
        .el-input {
            margin: 16px 16px 10px;
            width: calc(100% - 32px);
            :global(.el-input__inner) {
                border-color: transparent;
                color: var(--color-text-regular);
                background-color: var(--background-color-base);
                height: 36px;
            }
            :global(.el-input__icon) {
                font-size: 16px;
            }
        }
        .dropdown-container {
            padding: 0 16px;
            .dropdown-item {
                padding: 5.5px;
                margin-top: 8px;
                border: 1px solid var(--border-color-base);
                border-radius: 4px;
                cursor: pointer;

                span:last-child {
                    display: none;
                }
                &:hover {
                    background-color: var(--background-color-base);
                }
                &.selected {
                    border: 1px solid var(--color-black);
                    display: flex;
                    justify-content:space-between;
                    span {
                        font-weight: 700;
                    }
                    span:last-child {
                        display: block;
                    }
                }
                &.hide-unmatching {
                    display: none;
                }
            }
        }
        .el-divider {
            margin: 25px 0px 0px;
        }
        .actions {
            padding: 10px 16px;
            text-align: right;
            .el-button--text {
                &:hover {
                    color: var(--primary-color);
                }
            }
            .el-button {
                width: 50%;
                &:first-child {
                    width: 40%;
                }
                &:last-child {
                    padding: 0;
                    .el-dropdown-menu__item {
                        color: inherit;
                    }
                }
            }
        }
    }

</style>