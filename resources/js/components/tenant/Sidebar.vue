<template>
    <div :class="['sidebar', {'hidden': !visible, [`${direction}-direction`]: true, 'with-toggler': showToggler}]">
        <div :class="['toggler', direction === 'vertical' && {'icon-left': visible, 'icon-right': !visible}, direction === 'horizontal' && {'icon-down': visible, 'icon-up': !visible}]" v-if="showToggler && !submenu.visible" @click="handleVisibility"></div>
        <div ref="menu" class="menu">
            
            <div :class="['item', {'active': item.active}]" v-for="item in items" :key="item.title" :style="item.style" @mouseover="handleMouseRoute($event, item)" @click.stop="handleRoute($event, item)">
                <router-link 
                    :to="{name: item.route.name}" v-if="!item.children">
                    <i :class="['icon', item.icon]"></i>
                    <div class="title">{{$t(item.title)}}</div>
                </router-link>
                <template v-else>
                <i :class="['icon', item.icon]"></i>
                <div class="title">{{$t(item.title)}}</div>
                </template>
            </div>
            
        </div>
        <div ref="submenu" class="submenu" :style="{'width': `${submenu.width}px`}" @mouseleave="hideSubmenu">
            <div :class="['item', {'active': item.active}]" v-for="item in submenu.items" :key="item.title" @click.stop="handleRoute($event, item)">
                <router-link 
                    :to="{name: item.route.name}" >
                <i :class="['icon', item.icon]"></i>
                <div class="title">{{$t(item.title)}}</div>
                {{item.visible}}
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            routes: {
                type: Array,
                default: []
            },
            visible: {
                type: Boolean,
                default: true
            },
            direction: {
                type: String,
                default: 'vertical',
                validator: value => ['vertical', 'horizontal'].includes(value)
            },
            showToggler: {
                type: Boolean,
                default: true
            }
        },
        data () {
            return {
                origin: {
                    x: 50,
                    y: 127
                },
                items: [],
                submenu: {
                    width: 0,
                    items: [],
                    visible: false,
                },
                hiddenItems: [],
                animes: {
                    horizontal: null,
                    vertical: null
                }
            }
        },
        methods: {
            handleVisibility () {
                this.$emit('update:visible', this.visible = !this.visible)

                localStorage.setItem('sidebar:visibility', this.visible)
            },
            hideSubmenu() {
                console.log('hideSubmenu called')
                this.submenu.visible = false
            },
            handleMouseRoute (e, item) {
                if (!this.submenu.visible) {
                    if (this.direction === 'horizontal') {
                        this.origin.x = 50
                    } else if (this.direction === 'vertical') {
                        this.origin.y = 127
                    }
                }
                

                if (item.children) {
                    console.log('item.children')
                    // item.active = true

                    if (Object.is(item.children, this.submenu.items)) {
                        this.submenu.visible = !this.submenu.visible
                    } else {
                        this.submenu.visible = true
                    }

                    this.submenu.items = item.children

                    if (item.children.every(({route}) => route.name !== this.$route.name)) {
                        item.active = true
                    }
                } else {
                    console.log('not item.children')
                    this.submenu.visible = false

                    // if (item.route) {
                    //     item.active = true
                    // }
                }
            },
            handleRoute (e, item) {
                
                let i, items

                if (item.key.includes('.')) {
                    i = this.submenu.items.length
                    items = this.submenu.items
                } else {
                    i = this.items.length
                    items = this.items
                }

                while (i--) {
                    if (items[i].key !== item.key) {
                        if (items[i].active) {
                            items[i].active = false

                            if (items[i].children) {
                                if (items[i].children.some(({route}) => route && route.name === this.$route.name)) {
                                    let j = items[i].children.length

                                    while (j--) {
                                        if (items[i].children[j].active) {
                                            items[i].children[j].active = false

                                            break
                                        }
                                    }
                                }

                                this.submenu.items = []
                            }

                            break
                        }
                    }
                }

                if (item.children) {
                    item.active = true

                    if (Object.is(item.children, this.submenu.items)) {
                        this.submenu.visible = !this.submenu.visible
                    } else {
                        this.submenu.visible = true
                    }

                    this.submenu.items = item.children

                    if (item.children.every(({route}) => route.name !== this.$route.name)) {
                        item.active = true
//                        item.children[0].active = true

//                        this.$router.push(item.children[0].route)
                    }
                } else {
                    this.submenu.visible = false

                    if (item.route) {
                        item.active = true

//                        this.$router.push(item.route)
                    }
                }
            },
            onMouseOver (e) {
                console.log('onMouseOver');
                if (!this.submenu.visible) {
                    const bound = e.target.getBoundingClientRect()
                    
                    this.origin.x = e.clientX - bound.x
                    this.origin.y = e.clientY - bound.y

                    if (this.direction === 'horizontal') {
                        this.origin.x += bound.width / 2
                    } else if (this.direction === 'vertical') {
                        this.origin.y += bound.height / 2
                        this.origin.y -= 16
                    }
                }
            },
            doAnimeOnDirection (direction) {
                if (direction === 'horizontal') {
                    this.$anime({
                        targets: this.$el,
                        translateX: [0, 0],
                        translateY: ['100%', '0'],
                        translateZ: 0,
                        opacity: [0, 1],
                        padding: [0, 0],
                        easing: 'easeOutElastic(1, .64)'
                    })
                } else if (direction === 'vertical') {
                    this.$anime({
                        targets: this.$el,
                        translateY: [0, 0],
                        translateX: ['-100%', '0'],
                        translateZ: 0,
                        opacity: [0, 1],
                        padding: [0, 16],
                        easing: 'easeOutElastic(1, .64)',
                        begin: () => this.$el.parentElement.style.overflow = 'hidden',
                        complete: () => this.$el.parentElement.style.overflow = ''
                    })
                }
            },
            parseRoutes (routes) {
                let idx = 0

                this.items = routes.reduce((items, item, i) => {
                    item.key = `${i}`

                    item.active = false

                    if (item.children) {
                        if (!this.submenu.items.length) {
                            this.submenu.items = item.children
                        }

                        item.children = item.children.reduce((children, child, childIdx) => {
                            if ('visible' in child && !child.visible) {
                                return children
                            }

                            child.active = false
                            child.key = `${item.key}.${childIdx}`

                            if (child.route) {
                                if (child.route.name === this.$route.name) {
                                    item.active = true

                                    child.active = true
                                }
                            }

                            children.push(child)

                            return children
                        }, [])
                    } else if (item.route && item.route.name === this.$route.name) {
                        item.active = true
                    }

                    if (item.positionedBottom) {
                        if (!items.find(item => item.positionedBottom)) {
                            item.style = 'margin-top: auto'
                        }

                        items.push(item)
                    } else {
                        items.splice(idx, 0, item)

                        idx++
                    }

                    return items
                }, [])
            }
        },
        watch: {
            'visible' (state) {
                if (!state) {
                    this.submenu.visible = false
                }
            },
            'submenu.visible' (state) {
                const width = document.body.offsetWidth - 112 - 112 / 2

                if (this.direction === 'horizontal') {
                    if (state) {
                        this.$anime({
                            targets: this.$refs.submenu,
                            translateY: ['100%', '-98px'],
                            translateX: this.origin.x,
                            translateZ: 0,
                            opacity: [0, 1],
                            scale: [0, 1],
                            width: [0, width],
                            easing: 'easeOutElastic',
                        })
                    } else {
                        this.$anime({
                            targets: this.$refs.submenu,
                            translateY: ['-98px', '100%'],
                            translateX: this.origin.x,
                            translateZ: 0,
                            opacity: [1, 0],
                            duration: 2000,
                            scale: [{
                                value: 1
                            }, {
                                value: 1.06
                            }, {
                                value: 1
                            }, {
                                value: 0
                            }],
                            width: [width, 0],
                            easing: 'easeOutElastic',
                        })
                    }
                } else if (this.direction === 'vertical') {
                    if (state) {
                        this.$anime({
                            targets: this.$refs.submenu,
                            translateX: ['-100%', '112px'],
                            translateY: this.origin.y,
                            translateZ: 0,
                            opacity: [0, 1],
                            scale: [0, 1],
                            width: [0, width],
                            easing: 'easeOutElastic',
                        })
                    } else {
                        this.$anime({
                            targets: this.$refs.submenu,
                            translateX: ['112px', '-100%'],
                            translateY: this.origin.y,
                            translateZ: 0,
                            opacity: [1, 0],
                            scale: [{
                                value: 1
                            }, {
                                value: 1.06
                            }, {
                                value: 1
                            }, {
                                value: 0
                            }],
                            width: [width, 0],
                            easing: 'easeOutElastic',
                        })
                    }
                }

                if (state) {
                    this.$anime({
                        targets: this.$el.nextElementSibling,
                        filter: ['blur(0px)', 'blur(16px)'],
                        translateZ: 0,
                        scale: [1, 1.08],
                        duration: 240,
                        easing: 'linear'
                    })
                } else {
                    this.$anime({
                        targets: this.$el.nextElementSibling,
                        filter: ['blur(16px)', 'blur(0px)'],
                        translateZ: 0,
                        scale: [1.08, 1],
                        duration: 240,
                        easing: 'linear'
                    })
                }
            },
            'direction' (state) {
                if (this.submenu.visible) {
                    this.submenu.visible = false
                }

                this.doAnimeOnDirection(this.direction)
            },
            '$route': {
                immediate: true,
                handler (routes) {
                    this.parseRoutes(this.routes)
                }
            }
        },
        mounted () {
            this.doAnimeOnDirection(this.direction)
//            this.onSiblingElementClickHandler = () => this.submenu.visible = false

//            this.$el.nextElementSibling.addEventListener('click', this.onSiblingElementClickHandler)
        },
        beforeDestroy () {
//            this.$el.nextElementSibling.removeEventListener('click', this.onSiblingElementClickHandler)
        }
    }
</script>

<style lang="scss" scoped>
    .sidebar {
        padding: 16px;
        z-index: 1;
        box-sizing: border-box;

        .toggler {
            background-color: transparentize(#fff, .28);

            &:hover {
                background-color: #fff;
                color: var(--color-primary);
            }
        }

        &.vertical-direction .menu,
        .submenu {
            overflow-y: auto;
            // overflow-y: overlay;
            // overflow-x: hidden;
        }

        &.vertical-direction {
            height: 100%;

            &.hidden {
                margin-left: -112px;
                overflow: hidden;

                .toggler {
                    left: 12px;
                }
            }

            &:not(.hidden) {
                margin-left: 0;
            }

            &.with-toggler {
                margin-right: 24px;
            }

            .toggler {
                position: absolute;
                left: 124px;
                top: 50%;
                width: 32px;
                z-index: 1;
                height: 72px;
                margin-top: -36px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 0 12px 12px 0;
                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                cursor: pointer;
                transform: translateY(-50%);
                font-size: 16px;
            }

            .menu {
                min-width: 112px;
                width: 112px;
                height: 100%;
                flex-direction: column;

                .item {
                    width: 112px;
                    height: 112px;
                }
            }

            .submenu {
                top: 0;

                .item {
                    padding-left: 32px;

                    a {
                        display: flex;
                    }
                }
            }
        }

        &.horizontal-direction {
            width: 100%;
            height: auto;
            order: 999999;
            padding-top: 0;
            
            /deep/ &::-webkit-scrollbar {
                width: 4px;
            }

            /deep/ &::-webkit-scrollbar-thumb {
                border-radius: 8px;
                width: 4px;
                background-color: transparentize(lighten(#000, 48%), .16);
            }

            /deep/ &:hover::-webkit-scrollbar-thumb {
                background-color: lighten(#000, 48%);
            }

            /deep/ &::-webkit-scrollbar-track {
                border-radius: 8px;
                background-color: darken(#fff, 2%);
            }

            /deep/ &::-webkit-scrollbar-thumb:window-inactive {
                background-color: lighten(lighten(#000, 48%), 12%);
            }
            
            &.hidden {
                padding-bottom: 0;
                overflow: hidden;

                .toggler {
                    bottom: 12px;
                }

                .menu {
                    height: 12px;
                    border-bottom-left-radius: 0;
                    border-bottom-right-radius: 0;

                }
            }

            &:not(.hidden) {
                .menu {
                    height: 96px;
                    border-radius: 0;
                }
            }

            .toggler {
                position: absolute;
                bottom: 108px;
                left: 50%;
                width: 72px;
                z-index: 1;
                height: 32px;
                margin-left: -36px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px 12px 0 0;
                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                cursor: pointer;
                transform: translateX(-50%);
                font-size: 16px;
            }

            .menu {
                width: 100%;
                overflow-x: auto;
                overflow-x: overlay;
                overflow-y: hidden;
                flex-direction: row;

                .item {
                    flex: 1;
                    min-width: 96px;
                    width: 96px;
                    height: 96px;
                    margin: 0 !important;
                }
            }

            .submenu {
                bottom: 0;
                // padding-bottom: 16px;

                .item {

                    a {
                        display: flex;
                    }
                }
            }
        }

        .menu,
        .submenu {
            display: flex;
            position: relative;
            scrollbar-width: thin;
            border-radius: 12px;
            overscroll-behavior: contain;
            border: 1px var(--border-color-base) solid;
            box-shadow: 0 2px 6px transparentize(rgb(59, 14, 14), .88), 0 2px 4px transparentize(#000, .76);
            -webkit-overflow-scrolling: touch;

            &, .item {
                font-weight: 600;
            }

            .item {
                width: 100%;
                cursor: pointer;
                box-sizing: border-box;
                
                

                .title {
                    font-size: 14px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    pointer-events: none;
                    margin-top: 8px;
                }

                &.active {
                    color: var(--color-primary);
                }

                &:hover {
                    background-color: var(--primary-color-lighter);
                }

                a {
                    text-decoration: none;
                    color:inherit;
                    width: inherit;
                }
            }

            &::-webkit-scrollbar {
                width: 4px;
            }

            &::-webkit-scrollbar-thumb {
                border-radius: 8px;
                width: 4px;
                background-color: transparentize(lighten(#000, 48%), .16);
            }

            &:hover::-webkit-scrollbar-thumb {
                background-color: lighten(#000, 48%);
            }

            &::-webkit-scrollbar-track {
                border-radius: 8px;
                background-color: darken(#fff, 2%);
            }

            &::-webkit-scrollbar-thumb:window-inactive {
                background-color: lighten(lighten(#000, 48%), 12%);
            }
        }

        .menu {
            background-color: #fff;
            z-index: 2;

            .item {
                display: flex;
                flex-shrink: 0;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;

                .icon {
                    font-size: 28px;
                    pointer-events: none;
                }
            }
        }

        .submenu {
            flex-direction: column;
            background-color: white;
            max-width: 320px;
            position: absolute;
            left: 0;
            z-index: 1;

            .item {
                padding: 16px;
                display: flex;
                flex-shrink: 0;
                align-items: center;
                opacity: 1;
                position: relative;

                .icon {
                    font-size: 20px;
                    margin-right: 16px;
                }

                .title {
                    margin-top: 0;
                    display: flex;
                    align-items: center;
                }

                &:not(:last-child) {
                    border-bottom: 1px var(--border-color-lighter) solid;
                }
            }
        }
    }

</style>