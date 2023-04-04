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
            @if(env('LOCAL_MACHINE') == 'Slothe')
                #top-bg {
                    background: #2F4F4F;
                }
            @else
                #top-bg {
                    background: rgb(59,107,227);
                    background: linear-gradient(353deg, rgba(59,107,227,1) 0%, rgba(20,133,204,1) 50%, rgba(62,142,224,1) 100%);
                }
            @endif
            #breadcrumb a {
                color: #eee;
                padding: 0px 5px 0px 5px;
                /*font-weight:bold;*/
            }
            #breadcrumb a:hover {
                color: #fff;
            }


            #edit-room-internal input[type=checkbox],
            #add-room-internal input[type=checkbox] {
               position: absolute;
               top: -9999px;
               left: -9999px;
            }
            #edit-room-internal label,
            #add-room-internal label { 
              
            }

            /* Default State */
            #edit-room-internal .toggle-checkbox,
            #add-room-internal .toggle-checkbox {
               
            }

            /* Toggled State */
            #edit-room-internal input[type=checkbox]:checked ~ label,
            #add-room-internal input[type=checkbox]:checked ~ label {
               background: rgba(62,142,224,1);
               color: white;
               border-color: rgba(62,142,224,1);
            }
            label {
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
            
            [x-cloak] {
                display: none;
            }

        </style>

        @yield('style')

    </head>
    <body>

        <div id="main-wrapper" class="bg-grey-light pb-8">
    
            @yield('navigation-top')

            <div class="flex flex-wrap py-10 text-lg font-sans sm:px-8">
                
                <div id="basechat-rooms-wrapper" class="w-1/5 bg-black-extra text-sm pr-8 -pl-2 text-grey-dark"
                     style="display:none;">
                    <div id="basechat-rooms">
                        <!-- Will be replaced via ajax, rooms.blade.php -->
                    </div>
                </div>


                <!-- Navigation here -->


                
                <div class="w-full sm:w-4/5" style="margin-top: -140px;">

                    <div class="h-10 flex text-sm">

                        <div id="breadcrumb" class="hidden-xs w-1/2 pt-3 pl-4 text-white">

                            Breadcfumb here

                        </div>
                        <div class="w-full sm:w-1/2 flex flex-row-reverse">
                            
                            
                            <div id="call-log-tab" class="laz-tab h-10 uppercase text-blue-darker pt-3 px-4 bg-grey-light mr-2 rounded-t-lg hover:text-black hover:bg-grey-lighter cursor-pointer relative" data-target="#call-log" onclick="focusCallLog()">
                               Call Log
                                <div id="call-log-count" class="bg-red px-1 py-0 text-white rounded-full text-xs absolute pin-t pin-r -mt-2 -mr-2" style="display:none;">
                                    
                                </div>
                            </div>

                        
                            <div id="basechat-tab" class="laz-tab h-10 uppercase text-blue-darker pt-3 px-4 bg-grey-light mr-2 rounded-t-lg hover:text-black hover:bg-grey-lighter cursor-pointer relative" data-target="#basechat" onclick="focusBaseChat()">
                               Stit-Chat
                                <div id="unread-chat-count" class="bg-red px-1 py-0 text-white rounded-full text-xs absolute pin-t pin-r -mt-2 -mr-2" style="display:none;">
                                    
                                </div>
                            </div>


                            <div class="active laz-tab relative rounded-t-lg h-10 uppercase pt-3 px-4 text-blue-darker mr-2 bg-white  cursor-pointer" onclick="focusMain()"data-target="#main">
                                Main
                                @if(false)
                                    @yield('title') 
                                    <!-- You can already see the title in breadcrumb and elsewhere -->
                                @endif
                            </div>
                            
                        </div>
                    </div>

                    
                    <div class="laz-tabs bg-white shadow-lg" style="min-height: 700px;">
                        <div id="main" class="laz-tab-content px-8 pt-6 text-base">
                            @yield('main')

                            <br />
                        </div>
                        <div id="call-log" class="laz-tab-content p-8 hidden">
CAll Log
                        </div>
                        <div id="basechat" class="laz-tab-content bg-black hidden" style="min-height: 700px;">
Empty Shell
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



Call Log Javacsirp

        @yield('javascript')



    </body>
</html>
