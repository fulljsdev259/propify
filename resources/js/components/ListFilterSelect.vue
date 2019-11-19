<template>
    <div class="custom-select">
       <el-dropdown trigger="click" placement="bottom" @visible-change="handleVisibleChange">
            <el-button @click="handleDropdownClick" :class="[{'selected-button': findSelectedOne.count}]">
                <span v-if="findSelectedOne.count === 0">{{ filter.name }}</span>
                <el-tag 
                    v-else-if="this.filter.key !== 'language'"
                    size="mini"
                    :closable="findSelectedOne.count !== 1"
                    @close="selectItem(findSelectedOne.index, true)"
                >
                    <span v-if="items.length > 1">{{ `${filter.name}: ` }}</span>{{ `${getLanguageStr(findSelectedOne.label)}` }}
                </el-tag>
                <el-tag 
                    v-else
                    size="mini"
                    closable
                    @close="selectItem(findSelectedOne.index, true)"
                >
                    {{ `${filter.name}:` }}
                    <span :class="items[findSelectedOne.index].flag" ></span>
                </el-tag>
                <el-tag
                    v-if="findSelectedOne.count > 1"
                    size="mini"    
                    class="select-count"
                >
                    +{{ findSelectedOne.count - 1 }}
                </el-tag>
            </el-button>
            <el-button 
                v-if="findSelectedOne.count != 0" 
                icon="el-icon-close"
                class="close-button" 
                @click.prevent.stop="handleReset(true)">
            </el-button>
            <el-dropdown-menu slot="dropdown" :class="{'is-hide': items.length <= 1}">
                <template  v-if="items.length > 1">
                    <el-input
                        v-if="items.length > 9 || filter.searchBox !== undefined && filter.searchBox === true"
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
                    <div class="dropdown-container" v-if="items.length">
                        <div
                            :key="`${filter.name}${item.name}${item.id}selected`"
                            class="dropdown-item" 
                            :class="[{'selected': item.selected == true, 'hide-unmatching': !isContained(getLanguageStr(item.name))}]"
                            @click="selectItem(index)"
                            v-for="(item, index) in items" 
                        >
                            <span v-if="filter.key !== 'language'" v-html="filterSearch(getLanguageStr(item.name))"></span>
                            <span v-else><span :class="item.flag"></span>&nbsp;&nbsp;{{ $t(`general.languages.`+item.symbol) }}</span>
                            <span class="el-icon-check"></span>
                        </div>
                        
                    </div>
                    <el-divider></el-divider>
                    <div class="actions">
                        <el-button type="text" @click="handleReset()"><el-dropdown-item >{{ $t('general.reset') }}</el-dropdown-item ></el-button>
                        <el-button v-if="!isChanged()" type="text" :disabled="true">{{ $t('general.placeholders.select') }}</el-button>
                        <el-button v-else type="primary"><el-dropdown-item >{{ $t('general.placeholders.select') }}</el-dropdown-item></el-button>
                    </div>
                </template>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>
<script>
    export default {
        data() {
            return {
               originItems: [],
               selectClicked: false,
               search: '',
               selecting: false,
               items: [],
               options: ''
            }
        },
        props: {
          filter: {
              type: Object,
              default: () => {}
          },
          selectedOptions: {
              type: Array,
              default: () => []
          },
          searchBar: {
              type: Boolean,
              default: () => true
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
            isContained(str) {
                return str.toLowerCase().includes(this.search.toLowerCase());
            },
            handleDropdownClick() {
                this.clearSearch();
                if(this.items.length == 1 && this.findSelectedOne.count == 0) {
                    this.selectItem(0);
                    this.handleSelect();
                } 
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
                } else
                    this.handleSelect();
            },
            clearSearch() {
                this.search = '';
            },
            isChanged() {
                let result = false;
                if(this.selecting) {
                    for(let i = 0; i < this.items.length; i++)
                        if(this.items[i].selected != this.originItems[i].selected)
                            result = true;
                }
                return result;
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
                this.handleVisibleChange(true);
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
            getLanguageStr(str) {
                let result = str;
                if(this.filter.key == 'category_id') {
                    result = this.$t(`models.request.category_list.${str}`);
                } else if(this.filter.key == 'role') {
                    result = this.$t(`general.roles.${str}`)
                }
                return result;
            },
            initFilter() {
                this.items = [];
                this.originItems = [];
                this.options = this.filter.data;
                if(this.filter.key == 'language') {
                    let languagesObject = this.$constants.app.languages;
                    let languagesArray = Object.keys(languagesObject).map(function(key) {
                        return [String(key), languagesObject[key]];
                    });
                
                    this.items = languagesArray.map((item, index) => { 
                        let flag_class = 'flag-icon flag-icon-';
                        let flag = flag_class + item[0];
                        if( item[0] == 'en')
                        {
                            flag = flag_class + 'us'
                        }
                        return {
                            id: index + 1,
                            name: item[1],
                            symbol: item[0],
                            flag: flag,
                            selected: this.selectedOptions? this.selectedOptions.includes(index+1): false,
                        }
                    });
                    this.items.forEach((item) => {
                        this.originItems.push({
                            id: item.id,
                            name: item.name,
                            symbol: item.symbol,
                            flag: item.flag,
                            selected: item.selected,
                        });
                    });
                } else if(this.options.length) {
                    this.options.forEach((option) => {
                        this.items.push({
                            id: option.id,
                            name: option.name,
                            selected: this.selectedOptions? this.selectedOptions.includes(option.id): false,
                        });
                        this.originItems.push({
                            id: option.id,
                            name: option.name,
                            selected: this.selectedOptions? this.selectedOptions.includes(option.id): false,
                        })
                    });
                }
            }
        },
        created() {

        },
        updated() {
            if(JSON.stringify(this.options) !== JSON.stringify(this.filter.data))
                this.initFilter();
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
            border-radius: 6px;

            &:hover, &:focus {
                background-color: var(--color-main-background-base);
            }
            &.selected {
                border-color: 1px solid var(--color-text-primary);
                background-color: var(--color-text-primary);
            }

            span.el-tag {
                padding: 0 !important;
                background-color: transparent;
                border-color: transparent;
                color: var(--color-white);
                :global(i.el-tag__close) {
                    right: 0;
                    line-height: 1.4;
                    font-size: 14px;
                    font-weight: 700;
                    color: var(--color-white);
                    &:hover {
                        border-radius: 50%;
                        color: var(--color-primary);
                        background-color: var(--color-white);
                    }
                }
                &.select-count {
                    padding: 0 4px !important;
                    text-align: center;
                    border-radius: 4px;
                    border: 1px solid var(--color-white);
                }
            }
            &.selected-button {
                background-color: var(--color-primary);
                padding-right: 45px;
            }

        }
        .close-button {
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
            margin: 0;
            padding: 0 7px;
            font-size: 20px;
            border-radius: 0 4px 4px 0;
            border-left: 1px solid var(--color-white);
            background-color: transparent;
            color: white;
            &:hover {
                background-color: transparent;
            }
        }
    }
    .el-dropdown-menu {
        margin-bottom: 0px;
        padding: 0px;
        width: 288px;
        border-radius: 12px;
        &.is-hide {
            visibility: hidden;
        }
        .el-input {
            margin: 16px 16px 0;
            width: calc(100% - 32px);
            :global(.el-input__inner) {
                border-color: transparent;
                color: var(--color-text-regular);
                background-color: var(--background-color-base);
                height: 36px;
            }
            :global(.el-input__icon) {
                font-size: 16px;
                line-height: 36px;
            }
        }
        .dropdown-container {
            padding: 16px 16px 0;
            max-height: 310px;
            overflow-y: auto;
            
            &::-webkit-scrollbar{
                width: 8px;
            }
            &::-webkit-scrollbar-thumb{
                background-color: var(--color-text-placeholder);
                border: 1px solid transparent;
                border-radius: 11px;
                background-clip: content-box;
            }
            &::-webkit-scrollbar * {
                background: transparent;
            }

            .dropdown-item {
                padding: 4px;
                &:not(:last-child) {
                    margin-bottom: 6px;
                }
                border: 1px solid var(--border-color-base);
                border-radius: 4px;
                cursor: pointer;
                position: relative;

                span {
                    user-select: none;
                }
                span:not(:first-child) {
                    display: none;
                    position: absolute;
                    right: 8px;
                    top: 8px;
                }
                &:hover {
                    background-color: var(--background-color-base);
                }
                &.selected {
                    border: 1px solid var(--color-black);
                    display: flex;
                    justify-content:space-between;
                    align-items: center;
                    span {
                        font-weight: 700;
                        &:first-child {
                            margin-right: 15px;
                        }
                    }
                    span:not(:first-child) {
                        display: block;
                    }
                }
                &.hide-unmatching {
                    display: none;
                }
            }
        }
        .el-divider {
            margin: 16px 0px 0px;
        }
        .actions {
            padding: 8px 16px;
            text-align: right;
            .el-button--text {
                &:hover {
                    color: var(--primary-color);
                }
            }
            .el-button {
                width: 50%;
                padding: 0;
                &:first-child {
                    width: 40%;
                    .el-dropdown-menu__item:hover {
                        background-color: transparent;
                    }
                }
                &:last-child {
                    .el-dropdown-menu__item {
                        color: inherit;
                    }
                }
            }
        }

        &.el-popper {
            :global(&[x-placement^=bottom] .popper__arrow) {
                top: -12px;
                &::after {
                    margin-left: -10px;  
                }
            }
            :global(.popper__arrow) {
                border-bottom-width: 12px;
                border-left-width: 10px;
                border-right-width: 10px;
                border-top-width: 0;      
                &::after {
                    border-width: 12px;
                    border-left-width: 10px;
                    border-right-width: 10px;
                    border-top-width: 0;
                }
            }
        }
    }

</style>