import uuid from 'uuid/v1';
import {mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import Heading from 'components/Heading';
import Card from 'components/Card';
import UploadDocument from 'components/UploadDocument';
import Media from 'components/RequestMedia';
import RequestMedia from 'components/RequestMedia';


export default (config = {}) => {
    let mixin = {
        components: {
            Heading,
            Card,
            UploadDocument,
            Media,
            RequestMedia
        },
        data() {
            return {
                loading: {
                    state: false,
                    text: 'general.please_wait'
                },
                model: {
                    id: '',
                    status: '',
                    type: '',
                    visibility: '',
                    content: '',
                    title: '',
                    price: 0,
                    contact: '',
                    media: []
                },
                validationRules: {
                    title: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('models.listing.listing_title')})
                    }],
                    content: [{
                        required: true,
                        message: this.$t('validation.required', {attribute: this.$t('general.content')})
                    }],
                    price: [{
                        validator: this.validatePrice
                    }]
                },
                media: [],
                price: {
                    integer: 0,
                    decimals: '00'
                },
                uploadOptions: {
                    drop: true,
                    multiple: true,
                    draggable: true,
                    hideUploadButton: true,
                    extensions: 'vnd.openxmlformats-officedocument.wordprocessingml.document,vnd.openxmlformats-officedocument.spreadsheetml.sheet,pdf,png,jpeg,jpg',
                    hideSelectFilesButton: false
                },
                listing_id: null,
                audit_id: null
            };
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            mediaFiles() {
                return [...this.model.media, ...this.media];
            },
            listingConstants() {
                return this.$constants.listings;
            },
            listingPrice() {
                return `${this.price.integer}.${this.price.decimals}`;
            }
        },
        methods: {
            ...mapActions(['uploadListingMedia', 'deleteListingMedia']),
            uploadFiles(file) {
                const allowedFiles = [
                    'jpeg', 'png'
                ];
                const extension = file.raw.type.split('/');
                if (extension[1] && allowedFiles.includes(extension[1])) {
                    const url = `data:${file.raw.type};base64,${file.src}`;
                    this.media.push({
                        url,
                        id: uuid()
                    });
                } else {
                    displayError({
                        success: false,
                        message: this.$t('general.errors.files_extension_images')
                    });
                }
            },
            async uploadNewMedia(id) {
                if (this.media.length) {
                    for (let i = 0; i < this.media.length; i++) {
                        const image = this.media[i];
                        await this.uploadListingMedia({
                            id,
                            media: image.file.src
                        });
                    }
                }
            },
            async deleteMedia(image) {
                if (!image.model_id) {
                    this.media = this.media.filter((files) => {
                        return files.id !== image.id;
                    });
                    displaySuccess({
                        success: true,
                        message: this.$t('models.listing.media.removed')
                    });
                } else {
                    const resp = await this.deleteListingMedia({
                        id: image.model_id,
                        media_id: image.id
                    });
                    this.model.media = this.model.media.filter((files) => {
                        return files.id !== image.id;
                    });
                    displaySuccess(resp);
                }
            },
            async deleteMediaByIndex(index) {
                const resp = await this.deleteListingMedia({
                    id: this.model.id,
                    media_id: this.model.media[index].id
                });
                
                this.model.media.splice(index, 1)
                displaySuccess(resp);
            },
            validatePrice(rule, value, callback) {
                if (this.model.type !== 4) {
                    const priceInteger = +(this.price.integer || undefined);
                    const priceDecimal = +(this.price.decimals || undefined);


                    if (!isNaN(priceDecimal)
                        && !isNaN(priceInteger)
                        && 0 <= priceInteger
                        && 99 >= priceDecimal
                        && priceDecimal >= 0
                        && priceInteger % 1 === 0
                        && priceDecimal % 1 === 0
                        && this.price.integer.length
                        && this.price.decimals.length
                    ) {
                        callback();
                    } else {
                        callback(new Error(this.$t('validation.price.valid')));
                    }
                } else {
                    callback();
                }
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['createListing']),
                    submit() {
                        this.form.validate(async valid => {
                            if (valid) {
                                this.loading.state = true;
                                try {
                                    this.model.price = this.listingPrice;
                                    const resp = await this.createListing(this.model);

                                    await this.uploadNewMedia(resp.data.id);

                                    displaySuccess(resp);

                                    this.media = [];

                                    this.form.resetFields();
                                } catch (err) {
                                    displayError(err);
                                } finally {
                                    this.loading.state = false;
                                }
                            }
                        });
                    },
                };

                break;
            case 'edit':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getListing', 'updateListing']),
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }
                                this.loading.state = true;
                                try {
                                    this.model.price = this.listingPrice;
                                    await this.uploadNewMedia(this.model.id);
                                    const resp = await this.updateListing(this.model);
                                    this.model = Object.assign({}, this.model, resp.data);
                                    this.media = [];
                                    displaySuccess(resp);
                                    resolve(true);
                                } catch (err) {
                                    displayError(err);
                                    resolve(false);
                                } finally {
                                    this.loading.state = false;
                                }
                            });
                        });
                    }
                };

                mixin.created = async function () {
                    this.loading.state = true;

                    const resp = await this.getListing({id: this.$route.params.id});

                    this.model = resp;

                    if (this.model.price) {
                        const modelPrice = this.model.price.split('.');

                        if (modelPrice.length) {
                            this.price = {
                                integer: modelPrice[0],
                                decimals: modelPrice[1]
                            }
                        }
                    }

                    this.loading.state = false;
                };

                break;
        }
    }


    return mixin;
};


