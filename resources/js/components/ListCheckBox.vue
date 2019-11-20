<template>
    <div class="list-checkbox">
        <el-button 
            size="mini" 
            icon="el-icon-connection" 
            type="primary" 
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
                this.$emit('change-checkbox', this.value);
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

            &.checked {
                border-color: var(--primary-color);
                background-color: var(--primary-color);
                color: var(--color-white);
            }
        }
    }
</style>