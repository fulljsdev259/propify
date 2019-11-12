<template>
    <div class="custom-date-picker">
        <el-date-picker
            ref="datePicker"
            :format="filter.format"
            :placeholder="filter.name"
            :value-format="filter.format"
            style="width: 100%"
            type="date"
            align="center"
            v-model="selectedDate"
            popper-class="request-date-panel"
        >
        </el-date-picker>
        <div class="actions">
            <el-button 
                ref="pickerButton"
                @click="handleClick" 
                :class="[{'selected-button': selectedDate !== ''}]"
            >
                <span>{{ filter.name }}</span>
                <el-tag 
                    v-if="selectedDate !== ''"
                    size="mini"
                >
                    {{ `: ${selectedDate}` }}
                </el-tag>
            </el-button>
                <el-button 
                v-if="selectedDate !== ''" 
                icon="el-icon-close"
                class="close-button" 
                @click.prevent.stop="handleReset(true)">
            </el-button>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
               selectClicked: false,
               selecting: false,
               selectedDate: '',
            }
        },
        props: {
          filter: {
              type: Object,
              default: () => {}
          },
          selected: {
              type: String,
              default: () => ''
          }
        },
        components: {
           
        },
        computed: {
        },
        methods: {
            handleReset(notifyChange = false) {
                this.selectedDate = '';
                //this.handleSelect();
            },
            handleClick() {
                this.$refs.datePicker.focus();
            },
            handleSelect() {
                this.$emit('date-changed', this.selectedDate);
            }
        },
        watch: {
            selectedDate() {
                this.handleSelect();
            }
        },
        updated() {
            if(this.selected !== '' && this.selected !== undefined && this.selected !== null && this.selected !== this.selectedDate)
                this.selectedDate = this.selected;
        }
    }
</script>
<style lang="scss" scoped>
    .custom-date-picker {
        width: 100%;
        position: relative;
        .el-date-editor {
            position: absolute;
            :global(.el-input__inner) {
                border-radius: 10px;
            }
        }
        .actions {
            left: 0;
            top: 0;
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
                        padding: 0 3px !important;
                        text-align: center;
                        border-radius: 4px;
                        border: 1px solid var(--color-white);
                    }
                }
                &.selected-button {
                    background-color: var(--color-primary);
                    padding-right: 45px;
                    font-size: 12px;
                    span {
                        color: var(--color-white);
                    }
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
         
    }
</style>
<style lang="scss">
    .request-date-panel.el-popper {
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
</style>