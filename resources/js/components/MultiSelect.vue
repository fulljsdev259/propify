<template>
    <div class="custom-select" ref="multiSelect">
       <el-dropdown trigger="click" placement="bottom" @visible-change="handleVisibleChange">
            <el-button @click="handleDropdownClick" :class="[{'selected-button': findSelectedOne.count}]" :style="{'background-color':findSelectedOne.count?bgColor:'#f6f5f7'}" :disabled="disabled">
                <span v-if="findSelectedOne.count === 0">{{ name }}</span>
                <template v-else>
                    <el-tag 
                        :key="item.id"
                        size="mini"
                        :style="{'background-color': tagColor}"
                        :closable="!disabled"
                        @close="selectItem(item.index, true)"
                        v-for="(item, index) in findSelectedOne.items"
                    >
                        {{ ` ${getLanguageStr(item.name)}` }}
                    </el-tag>
                    
                    <el-tag
                        v-if="findSelectedOne.count > findSelectedOne.items.length"
                        size="mini"    
                        class="select-count"
                        :style="{'background-color': tagColor}"
                    >
                        +{{ findSelectedOne.count - findSelectedOne.items.length }}
                    </el-tag>
                </template>
            </el-button>
            <el-dropdown-menu slot="dropdown">
                <el-input
                    v-if="searchBar"
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
                        :key="`${name}${item.name}${item.id}selected` + index"
                        class="dropdown-item" 
                        :class="[{'selected': item.selected == true, 'hide-unmatching': !isContained(getLanguageStr(item.name))}]"
                        @click="selectItem(index)"
                        v-for="(item, index) in items" 
                    >
                        <span v-html="filterSearch(getLanguageStr(item.name))"></span>
                        
                        <span class="el-icon-check"></span>
                    </div>
                    
                </div>
                <el-divider></el-divider>
                <div class="actions">
                    <el-button type="text" @click="handleReset()"><el-dropdown-item >{{ $t('general.reset') }}</el-dropdown-item ></el-button>
                    <el-button v-if="!isChanged()" type="text" :disabled="true">{{ $t('general.placeholders.select') }}</el-button>
                    <el-button v-else type="primary"><el-dropdown-item >{{ $t('general.placeholders.select') }}</el-dropdown-item></el-button>
                </div>
            </el-dropdown-menu>
        </el-dropdown>
        <span :id="type + name" style="visibility: hidden" ></span>
    </div>
</template>
<script>
    export default {
        name: 'MultiSelect',
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
          type: {
              type: String,
              default: () => ''
          },
          name: {
              type: String,
              default: () => ''
          },
          data: {
              type: Array,
              default: () => [],
          },
          selectedOptions: {
              type: Array,
              default: () => []
          },
          searchBar: {
              type: Boolean,
              default: () => true
          },
          maxSelect: {
              type: Number
          },
          disabled: {
              type: Boolean,
              default: () => false,
          },
          tagColor: {
              type: String,
              default: () => ''
          },
          bgColor: {
              type: String,
              default: () => ''
          }
        },
        components: {
           
        },
        computed: {
            filteredOptions() {
                return this.options.filter((option) => this.search ==='' || this.search !== '' && option.name.includes(this.search));
            },
            findSelectedOne() {
                let result = [];
                let count = 0;
                let totalWidth = 0;
                this.items.forEach((item, index) => {
                    if(item.selected) {
                        let label = this.getLanguageStr(item.name);
                        let width = this.getTextWidth(label);
                        count ++;
                        if(width + totalWidth < this.$refs.multiSelect.clientWidth) {
                            result.push({
                                index: index,
                                name:label
                            });
                            totalWidth += width;
                        }
                    }
                });
                return { 
                    items: result.length >0?result:null,
                    count: count
                };
            },
        },
        methods: {
            isContained(str) {
                str += ""
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
                if(this.maxSelect) {
                    this.items.map(item => item.selected = false)
                    this.items[index].selected = true
                    if(notifyChange)
                        this.items[index].selected = false
                }
                else
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
                    name += ""
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
                if(this.type == 'category_id') {
                    result = this.$t(`models.request.category_list.${str}`);
                } else if(this.type == 'role') {
                    result = this.$t(`general.roles.${str}`)
                }
                return result;
            },
            getTextWidth(text) { 
  
                var spanText = document.getElementById(this.type + this.name);
                spanText.style.position = 'absolute'; 
                spanText.style.left = '0';
                spanText.style.whiteSpace = 'no-wrap'; 
                spanText.innerHTML = text; 
    
                var width = Math.ceil(spanText.clientWidth) + 17; 
                return width;
            },
            fitWidth() {
                this.column.select.data.map((item) => {
                    if(this.vModel == item.id) {
                        this.maxWidth = this.getTextWidth(item.name);
                    }
                });
            },
            initFilter() {
                this.items = [];
                this.originItems = [];
                this.options = this.data;
                if(this.options.length) {
                    this.options.forEach((option) => {
                        let id = option.id !== undefined? option.id:option.value;
                        this.items.push({
                            id: id,
                            name: option.name,
                            selected: this.selectedOptions? this.selectedOptions.includes(id): false,
                        });
                        this.originItems.push({
                            id: id,
                            name: option.name,
                            selected: this.selectedOptions? this.selectedOptions.includes(id): false,
                        })
                    });
                }
            }
        },
        created() {
            this.initFilter();
        },
        updated() {
        },
        watch: {
            selectedOptions() {
                this.initFilter();
            },
            data() {
                this.initFilter();
            }
        }
    }
</script>
<style lang="scss" scoped>
    .el-dropdown {
        width: 100%;
        position: relative;
        .el-button {
            padding: 0 15px;
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
                padding: 0 5px !important;
                &:not(:last-of-type) {
                    margin-right: 2.5px;
                }
                background-color: var(--color-primary);
                border-color: transparent;
                color: var(--color-white);
                line-height: 34px;
                height: 34px;
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
                    position: absolute;
                    right: 0;
                    padding: 0 7px !important;
                    text-align: center;
                    border-radius: 4px;
                    border: 1px solid var(--color-white);
                }
            }
            &.selected-button {
                background-color: var(--color-primary-lighter);
                padding: 0 2.5px;
            }

        }
    }
    .el-dropdown-menu {
        margin-bottom: 0px;
        padding: 0px;
        width: 288px;
        border-radius: 12px;
        .el-input {
            margin: 16px;
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
            padding: 0 16px;
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
                    span {
                        font-weight: 700;
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