<template>
    <div class="list-checkbox">
        <el-button 
            size="mini" 
            icon="el-icon-connection" 
            :class="{'checked': value}"
            @click="handleClick"
        >
        </el-button>
    </div>
</template>
<script>
    export default {
        props: {
            checked: {
                type: Boolean,
                default: () => false
            }
        },
        data() {
            return {
                value: this.checked
            }
        },
        methods: {
            handleClick() {
                this.value = !this.value;
                if(this.value)
                    this.$emit('clicked');
                else 
                    this.$emit('unclicked');
            }
        },
      
    }
</script>
<style lang="scss" scoped>
    .list-checkbox {
        .el-button {
            border-color: transparent;
            background-color: transparent;
            color: var(--color-text-secondary);
            border-radius: 6px;
            padding: 5px 5px ;

            &.checked {
                border-color: var(--primary-color);
                background-color: var(--primary-color);
                color: var(--color-white);
            }
            &.checked:hover {
                border-color: var(--primary-color) !important;
                background-color: var(--primary-color) !important;
                color: var(--color-white) !important;
                i {
                    font-weight: 400;
                }
            }
            :global(i) {
                font-size: 18px;
            }
        }
    }
</style>