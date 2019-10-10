<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<style type="text/css" media="all">

    * {
        font-size: 14px;
        font-family: {{$re->pdf_font_family ?? 'Arial'}};
    }
    body {
        padding-left: 42px;
        padding-right: 42px;
        padding-top: 25px;
    }

    img.logo {
        width: 181px;
        height: 55px;
    }
    #real-estate {
        padding-left: 148px;
        padding-top: 8px;
    }
    #resident-header {
        padding-top: 70px;
    }
    ul.offer {
        margin-top: 0;
    }
    p.offer {
        margin-bottom: 0;
    }
    ul.offer > li {
        margin-left: -26px;
    }

</style>
</head>

<body>
@yield('body')
</body>

</html>
