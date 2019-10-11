<template>
    <div class="templates-add">
        <heading :title="$t('models.template.add')" icon="ti-user" shadow="heavy" style="margin-bottom: 20px;"/>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-width="100px" ref="form">
                <el-col :md="16">
                    <card :loading="loading">
                        <el-form-item>
                            <select-language :activeLanguage.sync="language"/>
                        </el-form-item>
                        <el-form-item :label="$t('general.name')" :rules="validationRules.name" prop="name">
                            <el-input autocomplete="off" type="text"
                                      v-model="model.translations[language].name"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.subject')" :rules="validationRules.subject"
                                      prop="subject">
                            <el-input autocomplete="off" type="text"
                                      v-model="model.translations[language].subject"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.body')" :rules="validationRules.body" prop="body">
                            <quill-editor ref="quillEditor"
                                          v-model="model.translations[language].body"
                            >
                            </quill-editor>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="submit" icon="ti-save" type="primary">
                                {{$t('general.actions.save')}}
                            </el-button>
                        </el-form-item>
                    </card>
                </el-col>
                <el-col :md="8">
                    <card :loading="loading">
                        <el-form-item :label="$t('models.template.category')" :rules="validationRules.category"
                                      prop="category_id">
                            <el-select :placeholder="$t('models.template.placeholders.category')"
                                       @change="setSelectedCategory"
                                       class="custom-select"
                                       v-model="model.category_id">
                                <el-option
                                    :key="category.id"
                                    :label="category.name"
                                    :value="category.id"
                                    v-for="category in categories">
                                </el-option>
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
    // require styles
    import 'quill/dist/quill.core.css'
    import 'quill/dist/quill.snow.css'
    import 'quill/dist/quill.bubble.css'

    import {quillEditor} from 'vue-quill-editor'
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import SelectLanguage from 'components/SelectLanguage';
    import TemplatesMixin from 'mixins/adminTemplatesMixin';

    export default {
        mixins: [TemplatesMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            quillEditor,
            SelectLanguage
        }
    }
</script>
<style>
    .ql-container.ql-snow, .ql-editor {
        min-height: 250px;
    }
</style>
