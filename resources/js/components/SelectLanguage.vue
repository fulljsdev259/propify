<template>
    <el-select
        clearable
        id="languageform" 
        :class="activeLanguage == '' || activeLanguage == undefined ? '' : ' selected'"
        style="display: block" :value="activeLanguage" @input="$emit('update:activeLanguage', $event)"
        :placeholder="$t(role)"
        @change="$emit('change')"
    >
        <el-option 
            v-if="this.isTable == true && activeLanguage!=='' && activeLanguage!==undefined" 
            :label="$t('general.placeholders.select') +' '+ $t('settings.language')" 
            value="">
        </el-option>
        <template slot="prefix">
            <span id="languageflag" v-for="(language, index) in activeLanguages" :class="language.flag" :key="index"></span>
        </template>
        <el-option :label="language.name" :value="language.symbol" v-for="language in languages" :key="language.symbol">
            <span :class="language.flag" v-if="showFlag"></span>&nbsp;&nbsp;{{$t(`general.languages.`+ language.symbol)}}
        </el-option>
    </el-select>
</template>

<script>
export default {
    name: 'selectlanguage',
    data() {
        return {
            languages: [],
            showFlag: true
        }
    },
    props: {
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
    computed: {
        activeLanguages: function () {
            return this.languages.filter((lang) => {
                return lang.symbol == this.activeLanguage;
            })
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
            if( item[0] == 'en')
            {
                flag = flag_class + 'us'
            }
            return {
                name: item[1],
                symbol: item[0],
                flag: flag
            }
        });
    },

}
</script>

<style lang="less">
    .crud-view {
        #languageform.el-input__inner {
            padding-left: 15px !important;
        }
        .selected #languageform.el-input__inner {
            padding-left: 40px !important;
        }
        #languageflag {
            padding-left: 20px;
        }
    }
</style>