<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/images/favicon.png" />

        <title>Community Fluency Docs | @yield('title', ucwords($current_page))</title>

        <link href="https://fonts.googleapis.com/css?family=Niconne" rel="stylesheet">
        <!-- 
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        -->

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">


        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />

        <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet"> -->


        <link href="/css/main.css" rel="stylesheet" type="text/css">

        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clndr/1.4.7/clndr.min.js"></script>

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
            body {
                font-size: 16px;
            }
            /* Override Bootstrap */ 
            .nav-pills>li>a {
                border-radius: 100px;
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
 /*           #top-bg {
                background: rgb(59,107,227);
                background: linear-gradient(353deg, rgba(59,107,227,1) 0%, rgba(20,133,204,1) 50%, rgba(62,142,224,1) 100%);
            }*/
            #breadcrumb a {
                color: #eee;
                padding: 0px 5px 0px 5px;
            }
            #breadcrumb a:hover {
                color: #fff;
            }
            
            .graphbar-horizontal {
                animation: stretch_x 1s;
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

            .graphbar-vertical {
                animation: stretch_y 1s;
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

            .logo_text {
                animation: color_change 6s infinite;
            }

            @keyframes color_change {
                0% { opacity: 1; }
                10% { opacity: 0; }
                25% { opacity: 1; }
                100% { opacity: 1; }
            }

        </style>

        @yield('style')

    </head>
    <body>



    <div class="bg-grey-lightest pb-8 tracking-wide h-auto">

        @yield('navigation-top')

        <div class="flex flex-wrap py-8 text-lg px-8">

            <div class="w-64">

                @yield('navigation-left')

            </div>

            <div class="pl-8 w-4/5">

                <div class=" pb-2 text-3xl font-bold capitalize text-blue-dark">
                    @yield('title', $current_page)
                </div>

                <div class="pl-4py-4 text-base text-grey-darker w-3/4">
                    @yield('main')
                </div>

                <div class="py-4 text-base text-grey-darker w-3/4">

                    <div class="float-right w-48">
                        @yield('navigation-next-topic')
                    </div>

                </div>

            </div>

            

        </div>

    </div>

        
        <div class="bg-grey-dark p-8 flex items-center text-grey">

            <div class="w-1/3">
                {{ config('app.name') }}<br>
                P.O. Box 8703<br>
                Boston, MA 02114
            </div>
           

            <div class="w-1/3">
                <div class="text-3xl tracking-wide text-center text-grey" style="font-family: Niconne">
                    &copy; {{ config('app.name') }} {{ date('Y') }}
                </div>
            </div>

             <div class="w-1/3 text-right">
                Contact Us:<br>
                <a class="no-underline text-grey-light hover:text-white" href="mailto:laz@communityfluency.com">Lazarus Morrison, Owner</a><br>
                <a class="no-underline text-grey-light hover:text-white" href="mailto:peri@communityfluency.com">Peri O'Connor, Manager</a><br>
                
            </div>
            
        </div>

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <script type="text/javascript">
            // highlight active menu item
            var url = window.location;
            $('a').filter(function() {
                return this.href == url;
            }).parent().addClass('active');

            function getURLParameter(name,url) {
              return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(url)||[,""])[1].replace(/\+/g, '%20'))||null
            }

            $(document).on('click', '.remote-modal', function(e){
                e.preventDefault();
                var target = $(this).attr('target');
                var href = $(this).attr('href');
                $(target).modal('show').find('.modal-dialog').load(href);
            });

            $(document).on('click', ('.laz-tab:not(.active)'), function() {
                var classes = $(this).attr('class');
                var currentactive = $(this).siblings('.active');
                var activeclasses = currentactive.attr('class');
                currentactive.attr('class', classes);
                $(this).attr('class', activeclasses);

                var targetname = $(this).attr('data-target');
                $(targetname).closest('.laz-tabs').find('.laz-tab-content').addClass('hidden');
                $(targetname).removeClass('hidden');

                if (targetname == '#stit-chat') {
                    $(this).removeClass('bg-white');
                    $(this).removeClass('text-blue-darker');
                    $(this).addClass('bg-black');
                    $(this).addClass('text-grey-lightest');
                    $('#stitch-rooms').show();
                    $('#left-nav').hide();
                    $('#main-wrapper').removeClass('bg-grey-light');
                    $('#main-wrapper').addClass('bg-black-extra');
                } else {
                    $(this).addClass('bg-white');
                    $(this).addClass('text-blue-darker');
                    $(this).removeClass('bg-black');
                    $(this).removeClass('text-grey-lightest');
                    $('#stitch-rooms').hide();
                    $('#left-nav').show();
                    $('#main-wrapper').addClass('bg-grey-light');
                    $('#main-wrapper').removeClass('bg-black-extra');
                }
                
            });


           $(function () {
              $('[data-toggle="tooltip"]').tooltip()
            })

        </script>

        @yield('javascript')


        @if(session('msg'))
            <span id="flash_message" class="opacity-0 pin-b fixed ml-8 mb-8 z-99 bg-red-dark text-white rounded-full p-3 shadow-lg jumpUpOut">
                {{ session('msg') }}
            </span>
        @endif

    </body>
</html>
