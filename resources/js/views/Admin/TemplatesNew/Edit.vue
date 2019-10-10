<template>
    <div class="templates-edit">
        <heading :title="$t('general.actions.edit')" icon="ti-user" shadow="heavy" style="margin-bottom: 20px;">
            <edit-actions :queryParams="{tab: 'requests'}" :saveAction="submit" route="adminSettings"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-width="100px" ref="form">
                <el-col :md="12">
                    <card :loading="loading">
                        <el-form-item :label="$t('general.name')" :rules="validationRules.name" prop="name">
                            <el-input autocomplete="off" type="text"
                                      v-model="model.name"></el-input>
                        </el-form-item>
                        <el-form-item>
                           <select-language :activeLanguage.sync="language"/>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.subject')" :prop="`translations.${language}.subject`"
                                      :rules="validationRules.subject">
                            <el-input @blur="setLastFocusedElement($event)" autocomplete="off"
                                      type="text"
                                      v-model="model.translations[language].subject"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.body')" :prop="`translations.${language}.body`"
                                      :rules="validationRules.body"
                                      :key="editorKey">
                            <yimo-vue-editor
                                :config="editorConfig"
                                v-model="model.translations[language].body"></yimo-vue-editor>
                        </el-form-item>
                    </card>
                </el-col>
                <el-col :md="12">
                    <card :loading="loading">

                        <el-form-item :label="$t('models.template.category')" :rules="validationRules.category"
                                      prop="category_id">
                            <el-select :placeholder="$t('models.template.placeholders.category')"
                                       @change="setSelectedCategory"
                                       v-model="model.category_id"
                            >
                                <el-option-group
                                    :key="group.id"
                                    :label="group.name"
                                    v-for="group in categories">
                                    <el-option
                                        :key="category.id"
                                        :label="category.name"
                                        :value="category.id"
                                        v-for="category in group.categories">
                                    </el-option>
                                </el-option-group>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.tags')" v-if="!_.isEmpty(selectedCategory)">
                            <el-button :key="tag" @click="insertTag(tag)" size="mini" type="info"
                                       v-for="tag in categoryTags">{{tag}}
                            </el-button>
                        </el-form-item>
                    </card>
                </el-col>
            </el-form>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import EditActions from 'components/EditViewActions';
    import SelectLanguage from 'components/SelectLanguage';
    import TemplatesMixin from 'mixins/adminTemplatesMixin';
    import EditorConfig from 'mixins/adminEditorConfig';

    export default {
        mixins: [TemplatesMixin({
            mode: 'edit'
        }), EditorConfig],
        components: {
            Heading,
            Card,
            EditActions,
            SelectLanguage
        },
    }
</script>

<style>
</style>
