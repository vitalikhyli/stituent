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

                @if($bg_color = Auth::user()->bgImage)

                    background-image: url('{{ Auth::user()->bgImage }}');
                    background-size: cover;
                    background-position: 0px -800px;

                @elseif($bg_color = Auth::user()->bgColor)
               
                    background: rgb({{ $bg_color['r'] }}, {{ $bg_color['g'] }}, {{ $bg_color['b'] }});
                
                
                @else
                    background: rgb(59,107,227);
                    background: linear-gradient(353deg, rgba(59,107,227,1) 0%, rgba(20,133,204,1) 50%, rgba(62,142,224,1) 100%);
                @endif
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

        <div id="main-wrapper" class="bg-grey-light pb-8">
    
            @yield('navigation-top')

            <div class="flex flex-wrap py-10 text-lg font-sans sm:px-8">
                
               <!--  <div id="basechat-rooms-wrapper" class="w-1/5 bg-black-extra text-sm pr-8 -pl-2 text-grey-dark"
                     style="display:none;">
                    <div id="basechat-rooms"> -->
                        <!-- Will be replaced via ajax, rooms.blade.php -->
                    <!-- </div>
                </div> -->


                @yield('navigation-left')


                
                <div class="w-full sm:w-4/5" style="margin-top: -140px;">

                    <div class="h-10 flex text-sm">

                        <div id="breadcrumb" class="hidden-xs w-1/2 pt-3 text-white">

                            @yield('breadcrumb')

                        </div>
                        <div class="w-full sm:w-1/2 flex flex-row-reverse">
                            
                            
                            <div id="call-log-tab" class="laz-tab h-10 uppercase text-blue-darker pt-3 px-4 bg-grey-light mr-2 rounded-t-lg hover:text-black hover:bg-grey-lighter cursor-pointer relative whitespace-no-wrap" data-target="#call-log" onclick="focusCallLog()">
                               <i class="far fa-edit"></i> <b>@yield('call-log-name')</b>
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

                    <div id="bills" class='bg-grey-lighter overflow-x-hidden'>
                            
                        </div>
                    
                    <div class="laz-tabs bg-white shadow-lg" style="min-height: 700px;">

                        @if(!session('livewire_call_log'))
                            <div id="main" class="laz-tab-content px-8 pt-6 text-base">
                        @endif

                        @if(session('livewire_call_log'))
                            <div id="main" class="laz-tab-content px-8 pt-6 text-base hidden">
                        @endif
                            
                            @yield('main')

                            <br />
                        </div>

                        @if(!session('livewire_call_log'))
                            <div id="call-log" class="laz-tab-content p-8 hidden">
                        @endif

                        @if(session('livewire_call_log'))
                            <div id="call-log" class="laz-tab-content p-8">
                        @endif

                            @if(Auth::user()->permissions->developer && isset($_GET['livewire']))

                                @php
                                    session(['livewire_call_log' => true]);
                                @endphp

                            @endif

                            @if(session('livewire_call_log'))

                                @livewire('call-log.contact-modal')
                                @livewire('call-log.main')
                                @livewire('call-log.contacts')

                            @else

                                @include('shared-features.call-log.main')

                            @endif

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

                <div class="mt-4 leading-tight">
                    <a class="no-underline text-grey-light hover:text-white" href="mailto:laz@communityfluency.com"><b>Lazarus Morrison,</b> Owner<br />
                laz@communityfluency.com</a>
                </div>

                <div class="mt-4 leading-tight">
                    <a class="no-underline text-grey-light hover:text-white" href="mailto:peri@communityfluency.com"><b>Peri O'Connor,</b> Manager<br />
                    peri@communityfluency.com</a>
                </div>
                
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


        var str = "<div class='p-2 text-grey-dark text-sm pl-4'>Legislation <span class='text-xs uppercase'>";
        var bills_str = '';

        $('.legislation').each(function() {

            var year = parseInt($(this).attr('year'));

            var general = Math.floor((year - 1) / 2) - 818;
            //alert(general);
            var session = '';
            if (year % 2 == 0) {
                session = (year-1)+'-'+year;
            } else {
                session = year+'-'+(year+1);
            }
            //alert(session);
    
            reg = /[\s|\(|\/][H|S]B?D?R?\.?\s?[0-9]{1,4}/gi;  
            var targetText = $(this).text(); 
            var matches = targetText.match(reg);
            //var result;
            
            if (matches !== null) {
                for (b=0; b<matches.length; b++) {
                    var thestring = matches[b];
                    var thetype = matches[b].charAt(1).toUpperCase();
                    var visibletype = thetype;
                    if (thestring.includes('D') || thestring.includes('d')) {
                        thetype += 'D';
                        visibletype += 'D';
                    }
                    if (thestring.includes('R') || thestring.includes('r')) {
                        visibletype += 'R';
                    }
                    if (visibletype.length == 1) {
                        visibletype += 'B';
                    }
                    var thenum = thestring.replace( /^\D+/g, '');
                    //alert(thenum);
                    bills_str += "<a class='p-2' target='_blank' href='https://malegislature.gov/Bills/"+general+"/"+thetype+thenum+"' title='"+matches[b]+"'><span class='text-sm font-bold'>"+visibletype+thenum+"</span> ("+session+")</a>";
                }
                
                
            }
            
           
        });
        str += bills_str; 
        str += '</span></div>';
        if (bills_str.length > 0) {  

            $('#bills').html(str);
            $('#bills').slideDown('slow');
        }

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

    function focusTwitter() {
        twttr.widgets.load();
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
