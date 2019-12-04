<template>
    <div class="custom-select" ref="multiSelect">
       <el-dropdown trigger="click" placement="bottom" @visible-change="handleVisibleChange">
            <el-button @click="handleDropdownClick" :class="[{'selected-button': findSelectedOne.count && !showMultiTag}]" :style="{'background-color':findSelectedOne.count?bgColor:'#f6f5f7'}" :disabled="disabled">
                <span v-if="findSelectedOne.count === 0">{{ name }}</span>
                <el-tag 
                    v-else-if="type !== 'language'"
                    :key="item.id"
                    size="mini"
                    :style="{'background-color': tagColor}"
                    :closable="!disabled && (findSelectedOne.count !== 1 || showMultiTag)"
                    @close="selectItem(item.index, true)"
                    v-for="(item, index) in findSelectedOne.items"
                >
                    <span v-if="items.length > 1 && name && !showMultiTag">{{ `${name}: ` }}</span>
                    {{ ` ${getLanguageStr(item.name)}` }}
                </el-tag>
                <el-tag 
                    v-else
                    size="mini"
                    closable
                    @close="selectItem(findSelectedOne.index, true)"
                >
                    {{ `${name}:` }}
                    <span :class="items[findSelectedOne.index].flag" ></span>
                </el-tag>
                <el-tag
                    v-if="findSelectedOne.items.length > 0 && findSelectedOne.count > findSelectedOne.items.length"
                    size="mini"    
                    class="select-count"
                    :class="{'multi-tag': showMultiTag}"
                    :style="{'background-color': tagColor}"
                    >
                        +{{ findSelectedOne.count - findSelectedOne.items.length }}
                </el-tag>
            </el-button>
            <el-button 
                v-if="findSelectedOne.count != 0 && !showMultiTag" 
                icon="el-icon-close"
                class="close-button" 
                @click.prevent.stop="handleReset(true)">
            </el-button>
            <el-dropdown-menu slot="dropdown" :class="{'is-hide': items.length <= 1, 'group-show': showGroup}">
                <template  v-if="items.length > 1">
                    <el-input
                        v-if="items.length > 9 || searchBox !== undefined && searchBox === true"
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
                    <div class="dropdown-container" v-if="items.length && !showGroup">
                        <div
                            :key="`${name}${item.name}${item.id}${index}selected` + index"
                            class="dropdown-item" 
                            :class="[{'selected': item.selected == true, 'hide-unmatching': !isContained(getLanguageStr(item.name))}]"
                            @click="selectItem(index)"
                            v-for="(item, index) in items" 
                        >
                            <span v-if="type !== 'language'" v-html="filterSearch(getLanguageStr(item.name))"></span>
                            <span v-else><span :class="item.flag"></span>&nbsp;&nbsp;{{ $t(`general.languages.`+item.symbol) }}</span>
                            <span class="el-icon-check"></span>
                        </div>
                    </div>
                    <div class="dropdown-container" v-else-if="showGroup">
                        <template v-for="(item, index) in items" >
                            <div
                                v-if="item.id === -999"
                                :key="`${name}${item.name}${item.id}${index}selected` + index"
                                class="dropdown-item-label" 
                                :class="[{'hide-unmatching': !isContained(item.name)}]"
                            >
                                <span>{{ item.label }}</span>
                            </div>
                            <div
                                v-else
                                :key="`${name}${item.name}${item.id}${index}selected` + index"
                                class="dropdown-item" 
                                :class="[{'selected': item.selected == true, 'hide-unmatching': !isContained(getLanguageStr(`${item.name}${item.type}`))}]"
                                @click="selectItem(index)" 
                            >
                                <span v-html="filterSearch(getLanguageStr(item.name))"></span>
                                <span class='item-type'>{{ item.type }}</span>
                                <span class="el-icon-check"></span>
                            </div>
                        </template>
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
        <span :id="type + name" style="visibility: hidden; position: absolute" ></span>
    </div>
</template>
<script>
    export default {
        data() {
            return {
               originItems: [],
               originSelectedOptions: [],
               selectClicked: false,
               search: '',
               selecting: false,
               items: [],
               options: [],
               forceRecompute: false,
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
              default: () => []
          },
          selectedOptions: {
              type: Array,
              default: () => []
          },
          searchBox: {
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
          },
          showMultiTag: {
              type: Boolean,
              default: () => false
          },
          showGroup: {
              type: Boolean,
              default: () => false
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
                let divWidth = 0;
                this.forceRecompute; //Force Recompute this property
                this.selectedOptions;
                    
                this.items.forEach((item, index) => {
                    if(item.selected) {
                        let label = this.getLanguageStr(item.name);
                        let width = this.getTextWidth(label);
                        count ++;
                        if(!this.showMultiTag && count <= 1) {
                            result.push({
                                index: index,
                                name:label
                            });
                        } else if(this.showMultiTag && this.$refs.multiSelect !== undefined && (width + totalWidth < this.$refs.multiSelect.clientWidth)) {
                            result.push({
                                index: index,
                                name:label
                            });
                            totalWidth += width;
                        }
                    }
                });
                if(result.length >=1 && result.length < count && this.showMultiTag) {
                    if(totalWidth > this.$refs.multiSelect.clientWidth - this.getTextWidth('+' + count-result.length))
                        result.pop();
                }
                this.forceRecompute = false;
                return { 
                    items: result.length >0 ? result : [],
                    count: count
                };
            },
        },
        methods: {
            isContained(str) {
                if(str === undefined)
                    return false;
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
                    if(!this.showGroup) {
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
                    }
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
                } 
                return result;
            },
            getTextWidth(text, closable = true) { 
  
                var spanText = document.getElementById(this.type + this.name);
                var width = 0;
                if(spanText && spanText !== undefined) {
                    spanText.style.position = 'absolute'; 
                    spanText.style.left = '0';
                    spanText.style.whiteSpace = 'no-wrap'; 
                    spanText.style.fontSize = '12px';
                    spanText.style.fontFamily = 'Arial';
                    spanText.innerHTML = text; 
        
                    width = Math.ceil(spanText.clientWidth) + 10 + 5 + (!closable ? 0 : 18); //letter length + padding + margin + close icon
                } 
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
                if(this.showGroup) {
                    this.options.forEach((group) => {
                        let groupStr = '';
                        let index = this.items.push({
                            id: -999,
                            label: group.label,
                            name: '',
                        });
                        group.options.forEach((option) => {
                            let id = option.id !== undefined? option.id:option.value;
                            groupStr = `${groupStr}, ${option.name}`;
                            this.items.push({
                                id: id,
                                name: option.name,
                                type: this.$t(`models.unit.type.${this.$constants.units.type[option.type]}`),
                                selected: this.selectedOptions? this.selectedOptions.includes(id): false,
                            });
                        });
                        this.items[index - 1].name = groupStr; 
                    });
                    this.originItems = _.clone(this.items, true);
                } else if(this.type == 'language') {
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
                            selected: this.selectedOptions ? this.selectedOptions.includes(index+1) : false,
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
                        let id = option.id !== undefined? option.id:option.value;
                        this.items.push({
                            id: id,
                            name: option.name,
                            selected: this.selectedOptions? this.selectedOptions.includes(id): false,
                        });
                    });
                    this.originItems = _.clone(this.items, true);
                }
                this.originSelectedOptions = _.clone(this.selectedOptions, true);
                this.forceRecompute = true;
            }
        },
        created() {
            this.initFilter();
        },
        mounted() {
            this.forceRecompute = true;
        },
        updated() {
            if((JSON.stringify(this.options) !== JSON.stringify(this.data)) )
                this.initFilter();
        },
    }
</script>
<style lang="scss" scoped>
    .el-dropdown {
        width: 100%;
        position: relative;
        height: 40px;
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
                padding: 0 5px!important;
                &:not(:last-of-type) {
                    margin-right: 5px;
                }
                border-color: transparent;
                background-color: var(--color-primary);
                color: var(--color-white);
                line-height: 30px;
                height: 30px;
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
                    margin-right: 5px;
                    height: 20px;
                    line-height: 20px;
                    text-align: center;
                    border-radius: 4px;
                    border: 1px solid var(--color-white);
                }
                &.select-count.multi-tag {
                    position: absolute;
                    right: 5px;
                    top: 5px;
                    padding: 0 7px !important;
                    margin-right: 0px;
                    height: 30px;
                    line-height: 30px;
                    text-align: center;
                    border-radius: 4px;
                    border: 1px solid var(--color-white);
                }
            }
            &.multi-tag {
                padding: 0 5px;
            }
            &.selected-button {
                background-color: var(--color-primary);
                padding: 0 45px 0 5px;
                .el-tag {
                    padding: 0px;
                }
            }

        }
        .close-button {
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
            margin: 0;
            padding: 0 10px;
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
        &.group-show {
            width: 320px;
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
                display: flex;
                justify-content: space-between;

                span {
                    user-select: none;
                }
                span.el-icon-check {
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
                    span.item-type {
                        padding-right: 25px;
                    }
                    span.el-icon-check {
                        display: block;
                    }
                }
                &.hide-unmatching {
                    display: none;
                }
            }
            .dropdown-item-label {
                padding: 0 4px;
                margin-bottom: 5px;
                span {
                    color: var(--color-text-placeholder);
                }
                &:not(:nth-of-type(1)) {
                    margin-top: 15px;
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