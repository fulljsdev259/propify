<template>
    <el-dropdown trigger="click" @command="onCommandChange">
        <el-button class="el-dropdown-link" icon="icon-language" round>
            {{$i18n.locale}}
        </el-button>
        <el-dropdown-menu class="language-switcher" slot="dropdown">
            <div class="content">
                <i class="icon icon-language"></i>
                {{$t('general.choose_language')}}
            </div>
            <el-divider />
            <el-dropdown-item v-for="(name, iso) in $constants.app.languages" :key="iso" :command="iso">
                <img :src="require(`svg/${iso}.svg`)" />
                {{name}}
            </el-dropdown-item>
        </el-dropdown-menu>
    </el-dropdown>
</template>

<script>
    import {mapState, mapActions} from 'vuex'
    
    export default {
        props: {
            hideFlag: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('application', {
                locale: ({locale}) => locale
            })
        },
        methods: {
            ...mapActions('application', ['setLocale']),

            onCommandChange (locale) {
                this.setLocale(locale)
            }
        },
    }
</script>

<style lang="scss">
    .el-divider--horizontal{
        margin: 10px 0;
    }
    .el-dropdown-menu.language-switcher {
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
            padding: 4px 16px;
            display: flex;
            align-items: center;
            line-height: initial;

            img {
                width: 32px;
                height: 32px;
                margin-right: 12px;
            }
        }
    }
</style>

<style lang="scss" scoped>
    .el-dropdown {
        .el-dropdown-link {
            padding: 12px;
            text-transform: uppercase;
        }
    }
</style>