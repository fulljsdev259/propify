<template>
    <el-dropdown trigger="click">
        <el-button class="el-dropdown-link" icon="icon-plus" circle />
        <el-dropdown-menu class="quick-links" slot="dropdown">
            <div class="content">
                <i class="icon icon-link"></i>
                {{$t('resident.quick_links')}}
                <div class="description">A quicker way to get to the specific links.</div>
            </div>
            <el-dropdown-item v-for="link in data" :key="link.title" :icon="link.icon">
                {{link.title}}
            </el-dropdown-item>
            <el-dropdown-item v-if="!data.length">No links available!</el-dropdown-item>
        </el-dropdown-menu>
    </el-dropdown>
</template>

<script>
    export default {
        props: {
            data: {
                type: Array,
                required: true,
                defalt: () => ([])
            }
        }
    }
</script>

<style lang="scss">
    .el-dropdown-menu.quick-links {
        // TODO - make this global within the theme
        border-color: var(--border-color-base);
        box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
        border-radius: 12px; 

        .content {
            color: var(--color-primary);
            font-size: 15px;
            font-weight: 600;
            padding: 8px 16px;

            .icon {
                font-size: 24px;
                vertical-align: middle;
            }

            .description {
                color: var(--color-text-placeholder);
                font-size: 13px;
                font-weight: 500;
            }
        }

        .el-dropdown-menu__item {
            i {
                font-size: 18px;
            }
        }
    }
</style>