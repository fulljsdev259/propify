<template>
    <el-dialog
            :visible="visible"
            v-on:update:visible="$emit('update:visible', $event)"
            title="Floor preview"
            class="pdf-preview-modal"
            width="1140px">
        <div v-loading="loading">
            <div class="zoom-top" v-if="visible">
                <div class="zoom-top__left">
                    <el-button :disabled="currentZoom === maxZoom" @click="zoomIn()"><i class="el-icon-zoom-in"></i></el-button>
                    <el-button :disabled="currentZoom === minZoom" @click="zoomOut()"><i class="el-icon-zoom-out"></i></el-button>
                </div>

<!--                <div class="zoom-top__info">-->
<!--                    <el-tag v-if="addMarkerMode" effect="plain">-->
<!--                        click on img to set marker-->
<!--                    </el-tag>-->
<!--                </div>-->

                <transition-group name="fade">
                    <div key="1" v-if="dragmode" class="zoom-top__right">
                        <el-button :disabled="isNaN(currentDragstop.left) && isNaN(currentDragstop.top)"
                                   icon="icon-floppy"
                                   @click="saveDragstop(), stopAllMarkersDrag()">Save position</el-button>
                        <el-button icon="el-icon-close"
                                   @click="stopAllMarkersDrag(), markersKey += 1">Cancel</el-button>
                    </div>
                    <div key="2" v-else class="zoom-top__right">
                        <el-button icon="el-icon-plus"
                                   @click="putMarkerOnBlock()">add marker</el-button>
                    </div>
                </transition-group>
            </div>
            <panZoom style="overflow: hidden; max-height: 100vh;"
                     ref="panZoom"
                     class="pan-zoom"
                     selector=".scene"
                     :options="{
                            smoothScroll: false,
                            transformOrigin: {x: 0.5, y: 0.5},
                            bounds: true,
                            boundsPadding: 0.1,
                            minZoom: minZoom,
                            maxZoom: maxZoom,
                         }"
                     @init="onInit"
                     @zoom="currentZoom = panzoomInstance.getTransform().scale"
            >
                <div class="scene"
                     style="width:100%; height: 100%; margin:0 auto;">
                    <vue-drag-resize v-for="(item, index) in markers"
                                     :key="index + markersKey"
                                     :class="`marker ${item.isDraggable
                                                        ? 'marker_draggable'
                                                        : dragmode
                                                            ? 'marker_disabled'
                                                            : ''}`"
                                     :isActive="true"
                                     :parentLimitation="true"
                                     :parentScaleX="currentZoom"
                                     :parentScaleY="currentZoom"
                                     :x="item.left"
                                     :y="item.top"
                                     :w="40"
                                     :h="40"
                                     :z="1"
                                     :isResizable="false"
                                     :isDraggable="item.isDraggable"
                                     @dragstop="onDragstop">
                        <div v-if="!dragmode" @click="delMarker(index)" class="el-icon-close marker__close"></div>
                        <el-popover
                                placement="bottom"
                                :title="`Marker id: ${item.id}`"
                                width="auto"
                                trigger="click"
                                :disabled="dragmode">
                            <div slot="reference" class="marker__icon">
                                <i class="el-icon-location"></i>
                            </div>
                            <div>
                                <el-button @click="dragmode = true, item.isDraggable = true">Change position</el-button>
                            </div>
                        </el-popover>
                    </vue-drag-resize>

                    <pdf v-if="isPdf"
                         class="scene__item"
                         :src="pdfFile"
                         :resize="false"
                         :text="false">
                        <template slot="loading">
                            <div class="pdf-loading">
                                Loading PDF...
                            </div>
                        </template>
                    </pdf>

                    <img v-else :src="fileUrl" @load="stopLoading" alt="plan" class="scene__item">
                </div>
            </panZoom>
        </div>

<!--        <div style="display: flex; margin-top: 30px;" v-for="(item, index) in markers">-->
<!--            {{item.id}}: {{'x:'+item.left}}/{{'y:'+item.top}}-->
<!--            index{{index}}-->
<!--            <el-button @click="delMarker(index)" style="margin-left: auto;">del</el-button>-->
<!--        </div>-->
    </el-dialog>
</template>

<script>
    import Vue from 'vue';
    import panZoom from 'vue-panzoom';
    Vue.use(panZoom);

    import pdfvuer from 'pdfvuer';

    import VueDragResize from 'vue-drag-resize'

    Vue.component('vue-drag-resize', VueDragResize);

    export default {
        name: "FloorPreviewModal",
        components: {
            pdf: pdfvuer
        },
        props: {
            initialMarkers: {
                type: Array,
            },
            visible: {
                type: Boolean,
                required: true
            },
            fileUrl: {
                required: true
            }
        },
        data(){
            return {
                loading: true,
                isPdf: null,
                markers: [],
                minZoom: 0.25,
                maxZoom: 2,
                panzoomInstance: null,
                zoomLevels: [0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2],
                currentZoomLevel: 0.25,
                pdfFile: null,
                currentZoom: null,
                dragmode: false,
                markersKey: 0,
                currentDragstop: {
                    left: null,
                    top: null
                },
            }
        },
        methods: {
            stopAllMarkersDrag() {
                this.markers.forEach(element => {
                    element.isDraggable = false;
                });

                this.dragmode = false;
            },
            saveDragstop() {
                this.markers.forEach((element) => {
                    if (element.isDraggable === true) {
                        element.left = this.currentDragstop.left;
                        element.top = this.currentDragstop.top;

                        this.currentDragstop.left = null;
                        this.currentDragstop.top = null;
                    }
                });
            },
            onDragstop(newRect) {
                this.currentDragstop.left = newRect.left;
                this.currentDragstop.top = newRect.top;
            },
            putMarkerOnBlock() {
                let scale = this.panzoomInstance.getTransform().scale;

                let sceneRect = document.querySelector('.scene__item').getBoundingClientRect();
                let panZoomRect = document.querySelector('.pan-zoom').getBoundingClientRect();

                let panZoom = document.querySelector('.pan-zoom');
                let centerX = (panZoomRect.x + panZoom.offsetWidth / 2);
                let centerY = (panZoomRect.y + panZoom.offsetHeight / 2);

                let left = (centerX - sceneRect.left)/scale - 20,
                    top = (centerY - sceneRect.top)/scale - 40;

                left < 0
                    ? left = 0
                    : '';
                left > sceneRect.width / this.currentZoom
                    ? left = sceneRect.width / this.currentZoom - 40
                    : '';

                top < 0
                    ? top = 0
                    : '';
                top > sceneRect.height / this.currentZoom
                    ? top = sceneRect.height / this.currentZoom - 40
                    : '';

                this.markers.push({
                    id: 'marker_' + (this.markers.length + 1),
                    left: +left,
                    top: +top,
                    isDraggable: true,
                });

                this.currentDragstop.left = +left;
                this.currentDragstop.top = +top;

                this.dragmode = true;
            },
            delMarker(index) {
                this.markers.splice(index, 1);
            },
            zoom() {
                const isSmooth = false;
                const scale = this.currentZoomLevel;
                if (scale) {
                    const transform = this.panzoomInstance.getTransform();
                    const deltaX = transform.x;
                    const deltaY = transform.y;
                    const offsetX = scale + deltaX;
                    const offsetY = scale + deltaY;

                    if (isSmooth) {
                        this.panzoomInstance.smoothZoom(0, 0, scale);
                    } else {
                        this.panzoomInstance.zoomAbs(offsetX, offsetY, scale);
                    }
                }
            },
            zoomIn() {
                this.currentZoomLevel = this.zoomLevels.reduce((prev, curr) => {
                    return (Math.abs(curr - this.currentZoom) < Math.abs(prev - this.currentZoom) ? curr : prev);
                });

                const idx = this.zoomLevels.indexOf(this.currentZoomLevel);

                // If next element exists
                if (typeof this.zoomLevels[idx + 1] !== 'undefined') {
                    this.currentZoomLevel = this.zoomLevels[idx + 1];
                }

                if (this.currentZoomLevel === 1) {
                    this.panzoomInstance.moveTo(0, 0);
                    this.panzoomInstance.zoomAbs(0, 0, 1);
                } else {
                    this.zoom();
                }
            },
            zoomOut() {
                this.currentZoomLevel = this.zoomLevels.reduce((prev, curr) => {
                    return (Math.abs(curr - this.currentZoom) < Math.abs(prev - this.currentZoom) ? curr : prev);
                });

                const idx = this.zoomLevels.indexOf(this.currentZoomLevel);

                //if previous element exists
                if (typeof this.zoomLevels[idx - 1] !== 'undefined') {
                    this.currentZoomLevel = this.zoomLevels[idx - 1];
                }

                if (this.currentZoomLevel === 1) {
                    this.panzoomInstance.moveTo(0, 0);
                    this.panzoomInstance.zoomAbs(0, 0, 1);
                } else {
                    this.zoom();
                }
            },

            onInit(panzoomInstance, id) {
                this.panzoomInstance = panzoomInstance;
            },
            initStartPosition() {
                const wrapper = this.$refs.panZoom.$el;
                const scene = this.$refs.panZoom.scene;
                const k = 4/3; // 1.33

                const x = wrapper.offsetWidth / 2 - wrapper.offsetWidth * this.minZoom / 2;
                const y = wrapper.offsetHeight / 2 - scene.offsetHeight * this.minZoom / 2;

                this.panzoomInstance.zoomAbs(x * k, y * k, this.minZoom);
                this.currentZoom = this.panzoomInstance.getTransform().scale;

                if(this.initialMarkers.length > 0) {
                    this.markers = this.initialMarkers;
                    this.markers.forEach(function (element) {
                        element.isDraggable = false;
                    });
                }
            },
            stopLoading() {
                setTimeout(() => {
                    this.initStartPosition();
                    this.loading = false;
                }, 500);
            },
            setPdfFile(fileUrl) {
                this.pdfFile = pdfvuer.createLoadingTask({
                    url: fileUrl,
                    cMapUrl: "pdfjs/cmaps/",
                    cMapPacked: true
                });
                this.pdfFile.promise.then(pdf => {
                    this.stopLoading();
                });

            }
        },
        created() {
            this.isPdf = this.fileUrl.split('.')[this.fileUrl.split('.').length - 1] === 'pdf';

            this.isPdf
                ? this.setPdfFile(this.fileUrl)
                : '';
        },
    }
</script>

<style lang="scss">
    .marker {
        &__icon {
            cursor: pointer;
            z-index: 1;
            position: relative;
            font-size: 40px;
            line-height: 1;
            color: var(--color-success);
        }
        &__close {
            cursor: pointer;
            opacity: 0;
            z-index: 9;
            padding: 2px;
            position: absolute;
            top: -6px;
            right: -6px;
            font-size: 12px;
            color: var(--color-text-regular);
            transition: all 0.3s;
            &:hover {
                color: var(--color-danger);
                font-weight: 700;
            }
        }
        &:before {
            outline: none !important;
        }
        &:hover &__close {
            opacity: 1;
        }
        &_draggable {
            z-index: 2 !important;
        }
        &_disabled &__icon {
            cursor: default;
            opacity: 0.5;
        }
        &_draggable &__icon {
            cursor: move;
        }
    }

    .pan-zoom {
        z-index: 9;
        position: relative;
        margin: 0 auto;
        width: 1100px !important;
        height: 750px;
    }
    .vue-pan-zoom-scene {
        height: 100%;
        outline: none;
        background: #eee;
        .scene {
            height: auto !important;
            &__item {
                margin: 0 auto;
                width: 100%;
                max-height: 100%;
            }
        }
    }

    .zoom-top {
        z-index: 10;
        position: relative;
        margin: 0 auto;
        width: 100%;
        max-width: 1100px;
        &__left,
        &__right{
            display: flex;
            flex-direction: column;
            z-index: 9;
            position: absolute;
            top: 20px;
            .el-button {
                &:disabled {
                    opacity: 0.7;
                }
                &:nth-child(2) {
                    margin: 10px 0 0;
                }
            }
        }
        &__left {
            left: 20px;
        }
        &__right {
            right: 20px;
        }
        &__info {
            z-index: 9;
            position: absolute;
            top: 30px;
            left: 90px;
        }
    }

    .pdf-preview-modal {
        .page {
            width: 100% !important;
            height: auto !important;
            max-height: 100%;
            .canvasWrapper {
                width: 100% !important;
                height: auto !important;
                max-height: 100%;
                canvas {
                    width: 100% !important;
                    height: auto !important;
                    max-height: 100%;
                }
            }
        }
    }
    .pdf-loading {
        display: flex;
        height: 500px;
        align-items: center;
        justify-content: center;
    }
</style>
