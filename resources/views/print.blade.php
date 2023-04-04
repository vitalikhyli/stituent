<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=0.5">
        <link rel="icon" type="image/png" href="/images/favicon.png" />

        <title>{{ config('app.name') }} @yield('title')</title>

        <link href="https://fonts.googleapis.com/css?family=Niconne:100,200,300,400,500,600,700,800" rel="stylesheet">

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />

        <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">

        <link href="/css/main.css" rel="stylesheet" type="text/css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <style>
            @page {
              size: A4 portrait;
              margin: 1mm 1mm 0 5mm;
            }
            html {
                font-size: 16px;
                background: white;
                font-family: Opens Sans;
            }
            a, button, input {
                transition: .3s;
                text-decoration: none !important;
            }
            * {
                outline: none !important;
            }
            body {
                font-size: 16px;
            }
            .dropdown-menu > li > a {
                display: block;
                padding: 8px 16px;
            }
            .container {
                padding-right: 0px;
                padding-left: 0px;
            }
             /* style sheet for "letter" printing */
             @media print and (width: 8.5in) and (height: 11in) {
                @page {
                    margin: 1in;
                }
             }

        </style>

        @yield('style')

    </head>
    <body>

        <div class="">

            @yield('above-main')

            <div class="m-8 bg-white">

            @yield('main')

            </div>
        </div>

        <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script> -->

        <script type="text/javascript">
            // highlight active menu item
            var url = window.location;
            $('a').filter(function() {
                return this.href == url;
            }).parent().addClass('active');

            function getURLParameter(name,url) {
              return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(url)||[,""])[1].replace(/\+/g, '%20'))||null
            }
        </script>

        @yield('javascript')
    </body>
</html>
