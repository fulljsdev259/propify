<!-- temporary -- will be deprecated -->
<template>
    <div :class="['avatar', {'is-on-hover-shadow': shadow === 'hover', 'is-always-shadow': shadow === 'always'}]" :style="style" v-on="$listeners">
        <div v-if="!src" style="pointer-events: none">
            {{initials}}
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: ''
            },
            src: {
                type: String
            },
            size: {
                type: Number,
                default: 40
            },
            shadow: {
                type: String,
                default: 'none',
                validator: value => ['none', 'hover', 'always'].includes(value)
            }
        },
        methods: {
            color (str) {
                let hash = 0;

                for (let i = 0, length = str.length; i < length; i++) {
                    hash = str.charCodeAt(i) + ((hash << 5) - hash)
                }

                let color = '#';

                for (let i = 0; i < 3; i++) {
                    const value = (hash >> (i * 8)) & 0xFF

                    color += ('00' + value.toString(16)).substr(-2)
                }

                return color;
            },
            lightenColor (hex, amount) {
                let usePound = false

                if (hex[0] === '#') {
                    hex = hex.slice(1)
                    usePound = true
                }

                let num = parseInt(hex, 16)
                let r = (num >> 16) + amount

                if (r > 255) r = 255
                else if (r < 0) r = 0

                let b = ((num >> 8) & 0x00FF) + amount

                if (b > 255) b = 255
                else if (b < 0) b = 0

                let g = (num & 0x0000FF) + amount

                if (g > 255) g = 255
                else if (g < 0) g = 0

                return (usePound ? '#' : '') + (g | (b << 8) | (r << 16)).toString(16)
            }
        },
        computed: {
            style () {
                let style = {
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    borderRadius: '50%',
                    flexShrink: '0',
                    width: `${this.size}px`,
                    height: `${this.size}px`,
                    verticalAlign: 'middle'
                }

                if (this.src) {
                    style.backgroundImage = `url('/${this.src}')`
                    style.backgroundRepeat = 'no-repeat'
                    style.backgroundSize =  'cover'
                    style.backgroundPosition = 'center center'
                } else {
                    style.backgroundColor = this.color(this.name)
                    style.fontSize = Math.floor(this.size / 2.5) + 'px'
                    style.color = this.lightenColor(style.backgroundColor, 64)
                }

                return style
            },
            initials () {
                let parts = this.name.split(/[ -]/)
                let initials = ''

                for (let i = 0, length = parts.length; i < length; i++) {
                    initials += parts[i].charAt(0)
                }

                if (initials.length > 3 && initials.search(/[A-Z]/) !== -1) {
                    initials = initials.replace(/[a-z]+/g, '')
                }

                initials = initials.substr(0, 3).toUpperCase()

                return initials
            }
        }
    }
</script>

<style lang="scss" scoped>
    .avatar {
        transition: box-shadow .56s;

        &:hover.is-on-hover-shadow {
            cursor: pointer;
            box-shadow: 0px 2px 4px -1px transparentize(#000, .8), 0px 4px 5px 0px transparentize(#000, .86), 0px 1px 10px 0px transparentize(#000, .88);
        }

        &.is-always-shadow {
            box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
        }
    }
</style>