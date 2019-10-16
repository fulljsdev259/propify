let YimoVueEditor = require('yimo-vue-editor'),
    E = YimoVueEditor.E,
    $ = require('jquery');

export default {
    components: {
        'yimo-vue-editor': YimoVueEditor.default,
    },
    data() {
        return {
            editorKey: 0,
            editorConfig: {
                printLog: false,
                menus: [
                    'source',
                    '|',
                    'bold',
                    'underline',
                    'italic',
                    'strikethrough',
                    'eraser',
                    'forecolor',
                    'bgcolor',
                    '|',
                    'quote',
                    'fontfamily',
                    'fontsize',
                    'head',
                    'unorderlist',
                    'orderlist',
                    'alignleft',
                    'aligncenter',
                    'alignright',
                    '|',
                    'link',
                    'unlink',
                    'table',
                    '|',
                    'video',
                    'location',
                    'insertcode',
                    '|',
                    'undo',
                    'redo',
                    'fullscreen'
                ],
                lang: '',
                familys: [
                    'Arial',
                    'Verdana',
                    'Georgia',
                    'Times New Roman',
                    'Microsoft JhengHei',
                    'Trebuchet MS',
                    'Courier New',
                    'Impact',
                    'Comic Sans MS',
                    'Consolas'
                ],
                colors: '',
            }
        }
    },
    methods: {
        editorChangeLang() {
            this.editorConfig.lang = this.editorTooltips;
            this.editorConfig.colors = this.editorColors;
            this.editorKey+=1;
        },
        reInitHead(E, headArr) {
            E.createMenu(function (check) {
                var menuId = 'head'
                if (!check(menuId)) {
                    return
                }
                var editor = this
                var lang = editor.config.lang

                // 创建 menu 对象
                var menu = new E.Menu({
                    editor: editor,
                    id: menuId,
                    title: lang.head,
                    commandName: 'formatBlock'
                })

                // 初始化数据
                var data = headArr;
                /*
                    data 需要的结构
                    {
                        'commandValue': 'title'
                        ...
                    }
                */

                var isOrderedList

                function beforeEvent(e) {
                    if (editor.queryCommandState('InsertOrderedList')) {
                        isOrderedList = true

                        // 先取消有序列表
                        editor.command(e, 'InsertOrderedList')
                    } else {
                        isOrderedList = false
                    }
                }

                function afterEvent(e) {
                    if (isOrderedList) {
                        // 再设置有序列表
                        editor.command(e, 'InsertOrderedList')
                    }
                }

                // 创建droplist
                var tpl = '{#commandValue}{#title}'
                menu.dropList = new E.DropList(editor, menu, {
                    data: data,
                    tpl: tpl,
                    // 对 ol 直接设置 head，会出现每个 li 的 index 都变成 1 的问题，因此要先取消 ol，然后设置 head，最后再增加上 ol
                    beforeEvent: beforeEvent,
                    afterEvent: afterEvent
                })

                // 定义 update selected 事件
                menu.updateSelectedEvent = function () {
                    var rangeElem = editor.getRangeElem()
                    rangeElem = editor.getSelfOrParentByName(rangeElem, 'h1,h2,h3,h4,h5')
                    if (rangeElem) {
                        return true
                    }
                    return false
                }

                // 增加到editor对象中
                editor.menus[menuId] = menu
            })
        },
        reInitVideo(E, $, videoArr) {
            E.createMenu(function(check) {
                var menuId = 'video'
                if (!check(menuId)) {
                    return
                }
                var editor = this
                var lang = editor.config.lang
                var reg = /^<(iframe)|(embed)/i // <iframe... 或者 <embed... 格式

                // 创建 menu 对象
                var menu = new E.Menu({
                    editor: editor,
                    id: menuId,
                    title: lang.video
                })

                // 创建 panel 内容
                var $content = $('<div></div>')
                var $linkInputContainer = $('<div style="margin:20px 10px;"></div>')
                var $linkInput = $(
                    '<input type="text" class="block" placeholder=\''+videoArr[0]+'：<iframe src="..." frameborder=0 allowfullscreen></iframe>\'/>'
                )
                $linkInputContainer.append($linkInput)
                var $sizeContainer = $('<div style="margin:20px 10px;"></div>')
                var $widthInput = $(
                    '<input type="text" value="640" style="width:50px;text-align:center;"/>'
                )
                var $heightInput = $(
                    '<input type="text" value="498" style="width:50px;text-align:center;"/>'
                )
                $sizeContainer
                    .append('<span> ' + lang.width + ' </span>')
                    .append($widthInput)
                    .append('<span> px &nbsp;&nbsp;&nbsp;</span>')
                    .append('<span> ' + lang.height + ' </span>')
                    .append($heightInput)
                    .append('<span> px </span>')
                var $btnContainer = $('<div></div>')
                // var $howToCopy = $(
                //     '<a href="http://www.kancloud.cn/wangfupeng/wangeditor2/134973" target="_blank" style="display:inline-block;margin-top:10px;margin-left:10px;color:#999;">如何复制视频链接？111</a>'
                // )
                var $btnSubmit = $('<button class="right">' + lang.submit + '</button>')
                var $btnCancel = $(
                    '<button class="right gray">' + lang.cancel + '</button>'
                )
                $btnContainer
                    // .append($howToCopy)
                    .append($btnSubmit)
                    .append($btnCancel)
                $content
                    .append($linkInputContainer)
                    .append($sizeContainer)
                    .append($btnContainer)

                // 取消按钮
                $btnCancel.click(function(e) {
                    e.preventDefault()
                    $linkInput.val('')
                    menu.dropPanel.hide()
                })

                // 确定按钮
                $btnSubmit.click(function(e) {
                    e.preventDefault()
                    var link = $.trim($linkInput.val())
                    var $link
                    var width = parseInt($widthInput.val())
                    var height = parseInt($heightInput.val())
                    var $div = $('<div>')
                    var html = '<p>{content}</p>'

                    // 验证数据
                    if (!link) {
                        menu.dropPanel.focusFirstInput()
                        return
                    }

                    if (!reg.test(link)) {
                        alert('视频链接格式错误！')
                        menu.dropPanel.focusFirstInput()
                        return
                    }

                    if (isNaN(width) || isNaN(height)) {
                        alert('宽度或高度不是数字！')
                        return
                    }

                    $link = $(link)

                    // 设置高度和宽度
                    $link.attr('width', width).attr('height', height)

                    // 拼接字符串
                    html = html.replace('{content}', $div.append($link).html())

                    // 执行命令
                    editor.command(e, 'insertHtml', html)
                    $linkInput.val('')
                })

                // 创建panel
                menu.dropPanel = new E.DropPanel(editor, menu, {
                    $content: $content,
                    width: 400
                })

                // 增加到editor对象中
                editor.menus[menuId] = menu
            })
        },
        reInitTable(E, $, tableArr) {
            E.createMenu(function(check) {
                var menuId = 'table'
                if (!check(menuId)) {
                    return
                }
                var editor = this
                var lang = editor.config.lang

                // 创建 menu 对象
                var menu = new E.Menu({
                    editor: editor,
                    id: menuId,
                    title: lang.table
                })

                // dropPanel 内容
                var $content = $(
                    '<div style="font-size: 14px; color: #666; text-align:right;"></div>'
                )
                var $table = $(
                    '<table class="choose-table" style="margin-bottom:10px;margin-top:5px;">'
                )
                var $row = $('<span>0</span>')
                var $rowspan = $('<span> '+tableArr[0]+' </span>')
                var $col = $('<span>0</span>')
                var $colspan = $('<span> '+tableArr[1]+'</span>')
                var $tr
                var i, j

                // 创建一个n行n列的表格
                for (i = 0; i < 15; i++) {
                    $tr = $('<tr index="' + (i + 1) + '">')
                    for (j = 0; j < 20; j++) {
                        $tr.append($('<td index="' + (j + 1) + '">'))
                    }
                    $table.append($tr)
                }
                $content.append($table)
                $content
                    .append($row)
                    .append($rowspan)
                    .append($col)
                    .append($colspan)

                // 定义table事件
                $table
                    .on('mouseenter', 'td', function(e) {
                        var $currentTd = $(e.currentTarget)
                        var currentTdIndex = $currentTd.attr('index')
                        var $currentTr = $currentTd.parent()
                        var currentTrIndex = $currentTr.attr('index')

                        // 显示
                        $row.text(currentTrIndex)
                        $col.text(currentTdIndex)

                        // 遍历设置背景颜色
                        $table.find('tr').each(function() {
                            var $tr = $(this)
                            var trIndex = $tr.attr('index')
                            if (parseInt(trIndex, 10) <= parseInt(currentTrIndex, 10)) {
                                // 该行需要可能需要设置背景色
                                $tr.find('td').each(function() {
                                    var $td = $(this)
                                    var tdIndex = $td.attr('index')
                                    if (parseInt(tdIndex, 10) <= parseInt(currentTdIndex, 10)) {
                                        // 需要设置背景色
                                        $td.addClass('active')
                                    } else {
                                        // 需要移除背景色
                                        $td.removeClass('active')
                                    }
                                })
                            } else {
                                // 改行不需要设置背景色
                                $tr.find('td').removeClass('active')
                            }
                        })
                    })
                    .on('mouseleave', function(e) {
                        // mouseleave 删除背景色
                        $table.find('td').removeClass('active')

                        $row.text(0)
                        $col.text(0)
                    })

                // 插入表格
                $table.on('click', 'td', function(e) {
                    var $currentTd = $(e.currentTarget)
                    var currentTdIndex = $currentTd.attr('index')
                    var $currentTr = $currentTd.parent()
                    var currentTrIndex = $currentTr.attr('index')

                    var rownum = parseInt(currentTrIndex, 10)
                    var colnum = parseInt(currentTdIndex, 10)

                    // -------- 拼接tabel html --------

                    var i, j
                    var tableHtml = '<table>'
                    for (i = 0; i < rownum; i++) {
                        tableHtml += '<tr>'

                        for (j = 0; j < colnum; j++) {
                            tableHtml += '<td><span>&nbsp;</span></td>'
                        }
                        tableHtml += '</tr>'
                    }
                    tableHtml += '</table>'

                    // -------- 执行命令 --------
                    editor.command(e, 'insertHtml', tableHtml)
                })

                // 创建 panel
                menu.dropPanel = new E.DropPanel(editor, menu, {
                    $content: $content,
                    width: 262
                })

                // 增加到editor对象中
                editor.menus[menuId] = menu
            })
        },
    },
    mounted() {
        setTimeout(() => {
            this.editorChangeLang();

            this.reInitHead(E, this.editorHead);
            this.reInitVideo(E, $, this.editorVideo);
            this.reInitTable(E, $, this.editorTable);
        }, 0);

        this.$root.$on('changeLanguage', () => {
            this.editorChangeLang();

            this.reInitHead(E, this.editorHead);
            this.reInitVideo(E, $, this.editorVideo);
            this.reInitTable(E, $, this.editorTable);
        });
    },
    computed: {
        editorTooltips() {
            return {
                bold: this.$t('models.editor.bold'),
                underline: this.$t('models.editor.underline'),
                italic: this.$t('models.editor.italic'),
                forecolor: this.$t('models.editor.forecolor'),
                bgcolor: this.$t('models.editor.bgcolor'),
                strikethrough: this.$t('models.editor.strikethrough'),
                eraser: this.$t('models.editor.eraser'),
                source: this.$t('models.editor.source'),
                quote: this.$t('models.editor.quote'),
                fontfamily: this.$t('models.editor.fontfamily'),
                fontsize: this.$t('models.editor.fontsize'),
                head: this.$t('models.editor.head'),
                orderlist: this.$t('models.editor.orderlist'),
                unorderlist: this.$t('models.editor.unorderlist'),
                alignleft: this.$t('models.editor.alignleft'),
                aligncenter: this.$t('models.editor.aligncenter'),
                alignright: this.$t('models.editor.alignright'),
                link: this.$t('models.editor.link'),
                linkTarget: this.$t('models.editor.link_target'),
                text: this.$t('models.editor.text'),
                submit: this.$t('models.editor.submit'),
                cancel: this.$t('models.editor.cancel'),
                unlink: this.$t('models.editor.unlink'),
                table: this.$t('models.editor.table'),
                emotion: this.$t('models.editor.emotion'),
                img: this.$t('models.editor.img'),
                uploadImg: this.$t('models.editor.upload_img'),
                linkImg: this.$t('models.editor.link_img'),
                video: this.$t('models.editor.video'),
                width: this.$t('models.editor.width'),
                height: this.$t('models.editor.height'),
                location: this.$t('models.editor.location'),
                loading: this.$t('models.editor.loading'),
                searchlocation: this.$t('models.editor.searchlocation'),
                dynamicMap: this.$t('models.editor.dynamic_map'),
                clearLocation: this.$t('models.editor.clear_location'),
                langDynamicOneLocation: this.$t('models.editor.lang_dynamic_one_location'),
                insertcode: this.$t('models.editor.insertcode'),
                undo: this.$t('models.editor.undo'),
                redo: this.$t('models.editor.redo'),
                fullscreen: this.$t('models.editor.fullscreen'),
                openLink: this.$t('models.editor.open_link'),
                uploadPlaceTxt: this.$t('models.editor.upload_place_txt'),
                uploadTimeoutPlaceTxt: this.$t('models.editor.upload_timeout_place_txt'),
                uploadErrorPlaceTxt: this.$t('models.editor.upload_error_place_txt'),
            }
        },
        editorColors() {
            return {
                '#880000': this.$t('models.editor.color.dark_red'),
                '#800080': this.$t('models.editor.color.violet'),
                '#ff0000': this.$t('models.editor.color.red'),
                '#ff00ff': this.$t('models.editor.color.fresh_pink'),
                '#000080': this.$t('models.editor.color.navy_blue'),
                '#0000ff': this.$t('models.editor.color.blue'),
                '#00ffff': this.$t('models.editor.color.blue'),
                '#008080': this.$t('models.editor.color.blue_green'),
                '#008000': this.$t('models.editor.color.green'),
                '#808000': this.$t('models.editor.color.olive'),
                '#00ff00': this.$t('models.editor.color.light_green'),
                '#ffcc00': this.$t('models.editor.color.orange'),
                '#808080': this.$t('models.editor.color.gray'),
                '#c0c0c0': this.$t('models.editor.color.silver'),
                '#000000': this.$t('models.editor.color.black'),
                '#ffffff': this.$t('models.editor.color.white'),
            }
        },
        editorHead() {
            let title = this.$t('models.editor.title');
            return {
                '<h1>': title + '1',
                '<h2>': title + '2',
                '<h3>': title + '3',
                '<h4>': title + '4',
                '<h5>': title + '5',
            }
        },
        editorVideo() {
            return [
                this.$t('models.editor.in_format'),
            ]
        },
        editorTable() {
            return [
                this.$t('models.editor.rows'),
                this.$t('models.editor.cols'),
            ]
        },
    }
}