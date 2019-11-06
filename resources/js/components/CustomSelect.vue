<template>
    <div class="custom-select">
       <el-dropdown trigger="click" placement="bottom-start" @visible-change="handleVisibleChange">
            <el-button>
                <span v-if="selectedItems.length === 0">{{ placeholder }}</span>
                <el-tag 
                    v-else
                    size="mini"
                    closable
                    @close="selectItem(selectedItems[0], true)"
                >
                    {{ makeEllipis($t(`models.request.category_list.${options[selectedItems[0] - 1].name}`)) }}
                </el-tag>
                <el-tag
                    v-if="selectedItems.length > 1"
                    size="mini"    
                >
                    +{{ selectedItems.length - 1 }}
                </el-tag>
                <span v-if="selectedItems.length" class="dropdown-icon el-icon-close" @click.prevent.stop="handleReset(true)"></span>
                <span v-else-if="!selecting" class="dropdown-icon el-icon-arrow-down"></span>
                <span v-else-if="selecting" class="dropdown-icon el-icon-arrow-up"></span>
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
                        :key="placeholder + option.id + 'selected'"
                        class="dropdown-item" 
                        :class="[{'selected': selected(option.id) !== -1}]"
                        @click="selectItem(option.id)"
                        v-for="option in options" 
                        :style="{display: selected(option.id) !== -1 && isContained($t(`models.request.category_list.${option.name}`))? 'flex':'none'}"
                    >
                        <span v-html="filterSearch($t(`models.request.category_list.${option.name}`))"></span>
                        <span
                            v-if="selected(option.id) !== -1" 
                            class="el-icon-check"
                        >
                        </span>
                    </div>
                    <div
                        :key="placeholder + option.id"
                        class="dropdown-item" 
                        @click="selectItem(option.id)"
                        v-for="option in options" 
                        :style="{display: selected(option.id) === -1 && isContained($t(`models.request.category_list.${option.name}`))? 'flex':'none'}"
                    >
                        <span v-html="filterSearch($t(`models.request.category_list.${option.name}`))"></span>
                    </div>
                </div>
                <el-divider></el-divider>
                <div class="actions">
                    <el-button type="text" @click="handleReset()">Reset</el-button>
                    <el-button v-if="compareArray(originItems, selectedItems)" type="text" :disabled="true">Select</el-button>
                    <el-button v-else type="primary" @click="handleSelect()"><el-dropdown-item>Select</el-dropdown-item></el-button>
                </div>
            </el-dropdown-menu>
        </el-dropdown>
        <span :id="spanKey" style="visibility: hidden" ></span>
    </div>
</template>
<script>
    import {Avatar} from 'vue-avatar';

    export default {
        name: 'ContractCount',
        data() {
            return {
               selectedItems: [],
               originItems: [],
               selectClicked: false,
               search: '',
               selecting: false,
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
          spanKey: {
              type: String,
              default: () => 'span'
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
            isContained(str) {
                return str.toLowerCase().includes(this.search.toLowerCase());
            },
            handleVisibleChange(visible) {
                this.selecting = visible;
                if(visible) {
                    this.originItems = this.selectedItems.slice();
                    this.clearSearch();
                }else if(!this.compareArray(this.originItems, this.selectedItems))
                    this.$emit('select-changed', this.selectedItems);
            },
            clearSearch() {
                this.search = '';
            },
            compareArray(arrA, arrB){
                let result = true;
                arrA.forEach((item) => {
                    if(arrB.indexOf(item) === -1)
                        result = false;
                });
                
                arrB.forEach((item) => {
                    if(arrA.indexOf(item) === -1)
                        result = false;
                });
                return result;
            },
            hasSearch() {
                return this.options.filter((option) => this.search ==='' || this.search !== '' && option.name.includes(this.search));
            },
            getLabel() {
                let label = this.placeholder;
                let count = this.selectedItems.length;
                if(count) {
                    label = `<el-tag type="info">${this.$t(`models.request.category_list.${this.options[this.selectedItems[0]-1].name}`)}</el-tag>`;
                    if(count > 1)
                        label = label + `<el-tag>+${count - 1}</el-tag>`
                }
                return label;
            },
            selectItem(index, notifyChange = false) {
                let pos = this.selected(index);
                if(pos === -1)
                    this.selectedItems.push(index);
                else   
                    this.selectedItems.splice(pos, 1);
                if(notifyChange)
                    this.handleSelect();
            },
            selected(index) {
                return this.selectedItems.indexOf(index);
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
                this.selectedItems = [];
                if(notifyChange)
                    this.handleSelect();
            },
            handleSelect() {
                this.selectClicked = true;
                this.$emit('select-changed', this.selectedItems);
            },
            getTextWidth(text) { 
                var spanText = document.getElementById(this.spanKey);
                spanText.style.position = 'absolute'; 
                spanText.style.left = '0';
                spanText.style.whiteSpace = 'no-wrap'; 
                spanText.innerHTML = text; 
    
                var width = Math.ceil(spanText.clientWidth); 
                return width;
            },
            makeEllipis(text) {
                let result = text, i;
                for(i = 0; i < text.length; i++) {
                    if(this.getTextWidth(text.slice(0, i)) > 100)
                        break;
                }
                if(i < text.length)
                    result = `${text.slice(0, i - 1)}...`;
                return result;
            }
        },
        mounted() {
            this.selectedItems = this.selectedOptions.slice();
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
            color: var(--color-text-placeholder);
            height: 40px;
            position: relative;
            &:hover, &:focus {
                background-color: inherit;
            }
            &:focus {
                border-color: var(--color-primary);
            }

            .dropdown-icon {
                position: absolute;
                right: 6px;
                top: 13px;
                color: var(--color-text-placeholder);
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