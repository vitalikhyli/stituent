<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/images/favicon.png" />

        <title>O S T R I C H : @yield('title')</title>

        <link href="https://fonts.googleapis.com/css?family=Niconne" rel="stylesheet">

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">


        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />


        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">



        <link href="/css/main.css" rel="stylesheet" type="text/css">

        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clndr/1.4.7/clndr.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>

        <style>
            html {
                font-size: 16px;
            }
            a, button, input, .cursor-pointer {
                text-decoration: none !important;
                transition: .3s;
            }
            .transition {
                transition: .3s;
            }
            * {
                outline: none !important;
                /*transition: .3s;*/
            }
            
            table.table th {
                border-top: 0 !important;
            }
            
            body {
                font-size: 16px;
            }
            .hidden-at-first {
                opacity:0;
            }
            /* Override Bootstrap */ 
            .nav-pills>li>a {
                border-radius: 100px;
            }
            .hidden-tailwind {
                display: none;
            }

            .jumpUpOut {
            -webkit-animation: twistFade 8s; /* Safari, Chrome and Opera > 12.1 */
               -moz-animation: twistFade 8s; /* Firefox < 16 */
                -ms-animation: twistFade 8s; /* Internet Explorer */
                 -o-animation: twistFade 8s; /* Opera < 12.1 */
                    animation: twistFade 8s;      
            }

            @keyframes twistFade {
              0% {
                transform: rotate(30deg);
              }
              5% {
                transform: rotate(0deg);
                opacity: 1;
              }
              100% {
                opacity: 0;
              }
            }

            .graphbar-vertical {
                animation: stretch_y 2s;
                transform-origin: bottom;
            }

            @keyframes stretch_y {
              from {
                transform: scaley(0);
              }
              to {
                transform: scaley(1);
              }
            }

            .graphbar-horizontal {
                animation: stretch_x 2s;
                transform-origin: left;
            }

            @keyframes stretch_x {
              from {
                transform: scalex(0);
              }
              to {
                transform: scalex(1);
              }
            }
            /*Only used in Call Log*/
            #call-log-add .btn-default {
                color: inherit; 
                background-color: #fff;
                border-color: #fff; 
                border: 1px solid transparent;
            }
            #call-log-add .btn-default.active,
            #call-log-add .btn-default:active,
            #call-log-add .open>.dropdown-toggle.btn-default {
                color: #333;
                font-weight: bold;
                background-color: #fff;
                box-shadow: none;
                border: 1px solid transparent;
            }
            #call-log-add .btn-default.active.focus,
            #call-log-add .btn-default.active:focus,
            #call-log-add .btn-default.active:hover,
            #call-log-add .btn-default:active.focus,
            #call-log-add .btn-default:active:focus,
            #call-log-add .btn-default:active:hover,
            #call-log-add .open>.dropdown-toggle.btn-default.focus,
            #call-log-add .open>.dropdown-toggle.btn-default:focus,
            #call-log-add .open>.dropdown-toggle.btn-default:hover,
            #call-log-add .btn-default:hover {
                color: #333;
                background: transparent;
                border: 1px solid transparent;
            }


            .dropdown-menu > li > a {
                display: block;
                padding: 8px 16px;
            }
            .container {
                padding-right: 0px;
                padding-left: 0px;
            }
            #left-nav li.active {
                background: white;
            }
            #left-nav li.active a {
                color: #1485cc !important;
            }

            #breadcrumb a {
                color: #eee;
                padding: 0px 5px 0px 5px;
                /*font-weight:bold;*/
            }
            #breadcrumb a:hover {
                color: #fff;
            }
            [x-cloak] {
                display: none;
            }


            /*#edit-room-internal input[type=checkbox],
            #add-room-internal input[type=checkbox] {
               position: absolute;
               top: -9999px;
               left: -9999px;
            }
            #edit-room-internal label,
            #add-room-internal label { 
              
            }*/

            /* Default State */
            /*#edit-room-internal .toggle-checkbox,
            #add-room-internal .toggle-checkbox {
               
            }*/

            /* Toggled State */
            /*#edit-room-internal input[type=checkbox]:checked ~ label,
            #add-room-internal input[type=checkbox]:checked ~ label {
               background: rgba(62,142,224,1);
               color: white;
               border-color: rgba(62,142,224,1);
            }
*/            label {
                cursor: pointer;
            }
            /*#basechat-rooms input[type=checkbox]:checked ~ label > .fa-circle {
                display:none;
            }
            #basechat-rooms input[type=checkbox]:checked ~ label > .fa-check-circle {
                display:inline;
            }
            #basechat-rooms .fa-check-circle {
                display:none;
            }*/
            
        </style>

        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js" defer></script>

        @livewireStyles

        @yield('style')

    </head>
    <body>

        <div id="main-wrapper" class="h-screen">
    
            @yield('main')

        </div>
        

        @livewireScripts

        @include('shared-features.call-log.javascript')

        @yield('javascript')
        @stack('scripts')

        @if(session('msg'))
            <span id="flash_message" class="opacity-0 pin-b fixed ml-8 mb-8 z-99 bg-red-dark text-white rounded-full p-3 shadow-lg jumpUpOut">
                {{ session('msg') }}
            </span>
        @endif


    </body>
</html>
