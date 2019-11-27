<template>
    <div class="ui-drawer">
        <slot />
    </div>
</template>

<script>
    export default {
        name: 'ui-drawer',
        props: {
            size: {
                type: Number,
                default: 448
            },
            docked: {
                type: Boolean,
                default: false
            },
            visible: {
                type: Boolean,
                default: false
            },
            direction: {
                type: String,
                default: 'left',
                validator: value => ['top', 'right', 'bottom', 'left'].includes(value)
            },
            overlayElement: {
                type: String,
                validator: value => value[0] === '#'
            },
            zIndex: {
                type: Number,
                default: 999999999,
                validator: value => value > 0
            },
            isResident: {
                type: Boolean,
                default: false
            },
        },
        data () {
            return {
                siblingElement: null,
                transparentOverlayElement: null,
                transparentOverlayClickHandler: null
            }
        },
        watch: {
            'visible': {
                immediate: false,
                handler (state) {
                    if (['left', 'right'].includes(this.direction)) {
                        this.$el.style.maxWidth = document.body.offsetWidth - this.size / 1 / 6 + 'px'
                    }

                    switch (this.direction) {
                        case 'top':
                            this.$anime({targets: this.$el, translateY: state ? '0' : '-100%'})

                            break
                        case 'right':
                            this.$anime({targets: this.$el, translateX: state ? '0' : '100%'})

                            break
                        case 'bottom':
                            this.$anime({targets: this.$el, translateY: state ? '0' : '100%'})

                            break
                        case 'left':
                            this.$anime({targets: this.$el, translateX: state ? '0' : '-100%'})

                            break
                    }

                    // TODO - auto blur container if visible is true first
                    if (state) {
                        
                        this.transparentOverlayElement.style.zIndex = 0

                        const animeOptions = {
                            targets: this.siblingElement,
                            filter: this.isResident ? ['blur(0px)', 'blur(4px)'] : ['blur(0px)', 'blur(1px)'],
                            translateZ: 0,
                            easing: 'easeInOutBack'
                        }

                        if (this.docked) {
                            if(this.isResident)
                                animeOptions.translateX = -this.size
                            else
                                animeOptions.translateX = 0
                        }

                        this.$anime(animeOptions)
                    } else {
                        this.transparentOverlayElement.style.zIndex = -1

                        const animeOptions = {
                            targets: this.siblingElement,
                            filter: this.isResident ? ['blur(4px)', 'blur(0px)'] : ['blur(1px)', 'blur(0px)'],
                            translateZ: 0,
                            easing: 'easeInOutBack'
                        }

                        if (this.docked) {
                            animeOptions.translateX = 0
                        }

                        this.$anime(animeOptions)
                    }
                }
            }
        },
        mounted () {
            this.transparentOverlayClickHandler = () => this.$emit('update:visible', false)

            this.transparentOverlayElement = document.createElement('div')
            this.transparentOverlayElement.style.cssText = 'width: 100%; height: 100%; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: -1; cursor: pointer'

            this.transparentOverlayElement.addEventListener('click', this.transparentOverlayClickHandler)

            this.$el.parentElement.style.position = 'relative'
            this.$el.parentElement.style.overflowX = 'hidden'

            this.$nextTick(() => {
                this.siblingElement = this.overlayElement ? document.getElementById(this.overlayElement.substr(1)) : this.$el.nextElementSibling || this.$el.previousElementSibling
                this.$el.parentElement.appendChild(this.transparentOverlayElement)
            })

            this.$anime.set(this.$el, {translateZ: 0, zIndex: this.zIndex, ...{
                top: {width: 'calc(100% - 2px)', height: this.size, top: 0, translateY: this.visible ? '0' : '-100%'},
                left: {width: this.size, height: 'calc(100% - 2px)', left: 0, translateX: this.visible ? '0' : '-100%'},
                right: {width: this.size, height: 'calc(100% - 2px)', right: 0, translateX: this.visible ? '0' : '100%'},
                bottom: {widtj: 'calc(100% - 2px)', height: this.size, bottom: 0, translateY: this.visible ? '0' : '100%'}
            }[this.direction]})
        },
        beforeDestroy () {
            this.$el.parentElement.style.position = ''
            this.$el.parentElement.style.overflowX = ''

            this.transparentOverlayElement.removeEventListener('click', this.transparentOverlayClickHandler)
        }
    }
</script>

<style lang="sass">
    .ui-drawer
        top: 0
        position: absolute
        background-color: #fff
        border: 1px var(--border-color-base) solid
        box-shadow: 0 16px 28px 0 transparentize(#000, .78), 0 25px 55px 0 transparentize(#000, .79)

    @media only screen and (max-width: 676px)
        .ui-drawer
            box-shadow: none
</style>