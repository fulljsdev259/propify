<template>
    <div class="custom-select">
       <el-dropdown trigger="click" placement="bottom" @visible-change="handleVisibleChange">
            <el-button @click="handleDropdownClick" :class="[{'selected-button': getSelected.length}]">
                <span >{{ role }}: </span>
                <el-tag 
                    size="mini"
                    closable
                    :key="item + index + 'selected'"
                    v-for="(item, index) in getSelected"
                >
                   <span :class="item.flag" v-if="showFlag"></span>
                </el-tag>
            </el-button>
            <el-button 
                v-if="getSelected.length > 1" 
                icon="el-icon-close"
                class="close-button" 
                @click.prevent.stop="handleReset(true)">
            </el-button>
            <el-dropdown-menu slot="dropdown">
                <div class="dropdown-container">
                    <div
                        :key="`${language.symbol}${index}selected`"
                        class="dropdown-item" 
                        :class="[{'selected': language.selected == true}]"
                        @click="selectItem(index)"
                        v-for="(language, index) in languages" 
                    >
                        <span :class="language.flag" v-if="showFlag">&nbsp;&nbsp;{{$t(`general.languages.`+ language.symbol)}}</span>
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
    </div>
</template>
<script>
    export default {
        data() {
            return {
               origin: [],
               selectClicked: false,
               search: '',
               selecting: false,
               items: [],
               options: '',
               
                languages: [],
                showFlag: true
            }
        },
        props: {
            searchBar: {
                type: Boolean,
                default: () => true
            },
            activeLanguage: {
                required: true
            },
            role: {
                type: String,
                default: ()=> {
                    return `general.placeholders.select`;
                }
            },
            isTable: {
                type: Boolean,
                default: () => {
                    return false;
                }
            }
        },
        components: {
           
        },
        computed: {
            filteredOptions() {
                return this.options.filter((option) => this.search ==='' || this.search !== '' && option.name.includes(this.search));
            },
            getSelected() {
                let result = [];
                this.languages.forEach((item, index) => {
                    if(item.selected) {
                        result.push(item);
                    }
                });
                return result;
            },
            activeLanguages: function () {
                return this.languages.filter((lang) => {
                    return lang.symbol == this.activeLanguage;
                })
            },
        },
        methods: {
            handleDropdownClick() {
                this.clearSearch();
            },
            isChanged() {
                let result = false;
                return false;
                if(this.selecting) {
                    for(let i = 0; i < this.languages.length; i++)
                        if(this.languages[i].selected != this.origin[i].selected)
                            result = true;
                }
                return result;
            },
            handleVisibleChange(notifyChange = false) {

            },
            selectItem(index, notifyChange = false) {
                this.languages[index].selected = !this.languages[index].selected;
                if(notifyChange)
                    this.handleSelect();
            },
            selected(index) {
                return this.languages[index].selected;
            },
            handleReset(notifyChange = false) {
                this.languages.forEach((item)=> {
                    item.selected = false;
                    this.originItems.push({id:item.id, name:item.name, selected:item.selected});
                });
                this.handleVisibleChange(true);
                this.handleSelect();
            },
            handleSelect() {
                let result = [];
                this.selectClicked = true;
                this.languages.forEach((item) => {
                    if(item.selected) {
                        result.push(item.id);
                    } 
                });
                this.$emit('select-changed', result);
            },
        },
        mounted() {
            let languagesObject = this.$constants.app.languages;
            let languagesArray = Object.keys(languagesObject).map(function(key) {
                return [String(key), languagesObject[key]];
            });
        
            this.languages = languagesArray.map(item => { 
                let flag_class = 'flag-icon flag-icon-';
                let flag = flag_class + item[0];
                let selected = false;
                if( item[0] == 'en')
                {
                    flag = flag_class + 'us';
                }
                return {
                    name: item[1],
                    symbol: item[0],
                    flag: flag,
                    selected: selected,
                }
            });
        },
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
                    width: 20px;
                    text-align: center;
                    border-radius: 4px;
                    border: 1px solid var(--color-white);
                }
            }
            &.selected-button {
                background-color: var(--color-primary);
                padding-right: 40px;
                color: var(--color-white);
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
            padding: 16px;
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
                span:last-child {
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
            margin: 0px;
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