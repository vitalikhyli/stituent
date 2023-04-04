<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/images/favicon.png" />

        <title>cf | @yield('title')</title>

        <link href="https://fonts.googleapis.com/css?family=Niconne" rel="stylesheet">
        <!-- 
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        -->

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">


        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />

        <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet"> -->

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
            #top-bg {
                background: rgb(59,107,227);
                background: linear-gradient(353deg, rgba(59,107,227,1) 0%, rgba(20,133,204,1) 50%, rgba(62,142,224,1) 100%);
            }
            #breadcrumb a {
                color: #eee;
                padding: 0px 5px 0px 5px;
                /*font-weight:bold;*/
            }
            #breadcrumb a:hover {
                color: #fff;
            }

            label {
                cursor: pointer;
            }
            
            [x-cloak] {
                display: none;
            }

            
        </style>

        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js" defer></script>

        @livewireStyles

        @yield('style')

    </head>
    <body>

        <div id="main-wrapper" class="bg-grey-light pb-8">
    
            @yield('navigation-top')

            <div class="flex flex-wrap py-10 text-lg font-sans sm:px-8">
                

                @yield('navigation-left')


                
                <div class="w-full sm:w-4/5" style="margin-top: -140px;">

                    <div class="h-10 flex text-sm">

                        <div id="breadcrumb" class="hidden-xs w-1/2 pt-3 text-white">

                            @yield('breadcrumb')

                        </div>
                        <div class="w-full sm:w-1/2 flex flex-row-reverse">
                            
                            
                            <div id="call-log-tab" class="laz-tab h-10 uppercase text-blue-darker pt-3 px-4 bg-grey-light mr-2 rounded-t-lg hover:text-black hover:bg-grey-lighter cursor-pointer relative whitespace-no-wrap" data-target="#call-log" onclick="focusCallLog()">
                               <i class="far fa-edit"></i> <b>Notes</b>
                                <div id="call-log-count" class="bg-red px-1 py-0 text-white rounded-full text-xs absolute pin-t pin-r -mt-2 -mr-2" style="display:none;">
                                    
                                </div>
                            </div>

                    

                            <div class="active laz-tab relative rounded-t-lg h-10 uppercase pt-3 px-4 text-blue-darker mr-2 bg-white  cursor-pointer" onclick="focusMain()"data-target="#main">
                                Main
                            </div>

                            @if(Auth::user()->permissions->developer || 1 == 1 )
                                <div class="p-3 pr-4 text-grey-light text-xs">
                                    Page loaded in <b>{{ round(microtime(true) - LARAVEL_START, 2) }}s</b>
                                </div>
                            @endif
                            
                        </div>
                    </div>

                    
                    <div class="laz-tabs bg-white shadow-lg" style="min-height: 700px;">
                        <div id="main" class="laz-tab-content px-8 pt-6 text-base">
                            @yield('main')

                            <br />
                        </div>
                        <div id="call-log" class="laz-tab-content p-8 hidden">
                            @include('shared-features.call-log.main')
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
                <div class="font-serif font-light text-3xl tracking-wide text-center text-grey" style="font-family: Niconne">
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

        /*
            THIS IS FOR DEBOUNCE, ON KEYUP
            https://stackoverflow.com/questions/1909441/how-to-delay-the-keyup-handler-until-the-user-stops-typing
        */
        function delay(callback, ms) {
          var timer = 0;
          return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
              callback.apply(context, args);
            }, ms || 0);
          };
        }

        function getLookupData(v) {
            if (v == '') {
                $('#main-lookup').addClass('hidden');
            }
            $.get('/{{ Auth::user()->team->app_type }}/lookup/'+v, function(response) {
                if (response == '') {
                    $('#main-lookup').addClass('hidden');
                } else {
                    $('#main-lookup').html(response);
                    $('#main-lookup').removeClass('hidden');
                }
            });
        }



    $(document).ready(function() {


        $("#main-lookup-input").focusout(function(){
            window.setTimeout(function() {$('#main-lookup').addClass('hidden'); }, 300);
        });

        $('#main-lookup-input').keyup(delay(function (e) {
            getLookupData(this.value);
        }, 500));
    

        $('.datepicker').datepicker();

    //////////////////////////////////////////////////////////////////////////////////////
    //
    //  TABS
    //
    //////////////////////////////////////////////////////////////////////////////////////

        $(document).on('click', ('.laz-tab:not(.active)'), function() {
            var classes = $(this).attr('class');
            var currentactive = $(this).siblings('.active');
            var activeclasses = currentactive.attr('class');
            currentactive.attr('class', classes);
            $(this).attr('class', activeclasses);

            var targetname = $(this).attr('data-target');
            $(targetname).closest('.laz-tabs').find('.laz-tab-content').addClass('hidden');
            $(targetname).removeClass('hidden');

            $(this).addClass('bg-white');
            $(this).addClass('text-blue-darker');
            $(this).removeClass('bg-black');
            $(this).removeClass('text-grey-lightest');
            $('#left-nav').show();
            $('#main-wrapper').addClass('bg-grey-light');
            $('#main-wrapper').removeClass('bg-black-extra');
            
        });

    });

      

    function focusCallLog() {
        setTimeout(function() {
            $('#call-subject').focus();
        }, 300);
    }

    function focusMain() {
        setTimeout(function() {
            $('#main').focus();
        }, 300);
    }


    //////////////////////////////////////////////////////////////////////////////////////
    //
    //  FOLLOW UPS
    //
    //////////////////////////////////////////////////////////////////////////////////////

    function updateLeftCounter(num_follow_ups) {
        if (!num_follow_ups) {
            $.get('/{{ Auth::user()->team->app_type }}/followups/count', function(response) {
                num_follow_ups = response;
                showUpdateLeftCounter(num_follow_ups);
            })
        } else {
            showUpdateLeftCounter(num_follow_ups);
        }
    }

    function showUpdateLeftCounter(num_follow_ups) {
        $('#outstandingfollowups').text(num_follow_ups);
        if (num_follow_ups < 1) {
            $('#outstandingfollowups').addClass('hidden');
        } else {
            $('#outstandingfollowups').removeClass('hidden');
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////
    //
    //  OTHER
    //
    //////////////////////////////////////////////////////////////////////////////////////

    $(document).ready(function() {

        var animateHTML = function() {
          var elems;
          var windowHeight;
          function init() {
            elems = document.querySelectorAll('.hidden-at-first');
            windowHeight = window.innerHeight;
            addEventHandlers();
            checkPosition();
          }
          function addEventHandlers() {
            window.addEventListener('scroll', checkPosition);
            window.addEventListener('resize', init);
          }
          function checkPosition() {
            if ($("#hidden-at-first-group").length > 0) {
                var positionFromTop = document.querySelector("#hidden-at-first-group").getBoundingClientRect().bottom -50;
                if (positionFromTop - windowHeight <= 0) {
                    for (var i = 0; i < elems.length; i++) {
                        elems[i].className = elems[i].className.replace(
                          'hidden-at-first',
                          'graphbar-vertical'
                        );
                    }
                }
            }

          }
          return {
            init: init
          };
        };
        animateHTML().init();


        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })

    });


        </script>

        @include('shared-features.call-log.javascript')

        @livewireScripts

        @yield('javascript')
        @stack('scripts')

        @if(session('msg'))
            <span id="flash_message" class="opacity-0 pin-b fixed ml-8 mb-8 z-99 bg-red-dark text-white rounded-full p-3 shadow-lg jumpUpOut">
                {{ session('msg') }}
            </span>
        @endif


    </body>
</html>
