@extends('dark-bg')

@section('style')

    <style>

        .prog {
          transform: rotate(-90deg);  
          stroke: #6cb2eb;
          position: absolute;
          top: -17px;
          left: -7px;
        }

        .prog1 circle {
          stroke-dasharray: 190;
            
        }
        .prog2 circle {
          stroke-dasharray: 190; 
        }
        .prog3 circle {
          stroke-dasharray: 190; 
        }


       
    </style>

@endsection

@section('content')

    <div id="data-loader" class="absolute pin-b w-full">

        

        <div class="p-8" style="margin: 5% 10%;">

            <div class="text-blue-dark opacity-25">
                Database: {{ DB::connection()->getDatabaseName() }}
            </div>
            

            <div class="text-4xl text-white mb-8">
                Step @{{ step }} of 3
            </div>
 

            <div class="flex text-2xl text-white mb-8">
                
                <div class="border-4 border-white rounded-full w-16 h-16 text-4xl relative">
                    <div class="prog prog1">
                        <svg height="80" width="80">
                            <circle cx="35" 
                                    cy="35" 
                                    r="30" 
                                    stroke-width="4" 
                                    v-bind:stroke-dashoffset="dashOffsetOne"
                                    fill="none" />
                        </svg>
                    </div>
                    <div v-if="one_percent == 100">
                        <i class="fas fa-check absolute text-blue-lighter" style="margin: 12px 0px 0px 10px;"></i>
                    </div>
                    <div v-else-if="one_percent < 1"></div>
                    <div v-else>
                        
                        <div class="absolute text-xl" style="margin: 15px 0px 0px 8px;">
                            @{{ one_percent }}%
                        </div>

                    </div>
                </div>
                <div class="mt-3 ml-6">
                    Constituents
                </div>
            </div>
            <div class="flex text-2xl text-white mb-8">

                <div class="border-4 border-white rounded-full w-16 h-16 text-4xl relative">

                    <div class="prog prog2">
                        <svg height="80" width="80">
                            <circle cx="35" 
                                    cy="35" 
                                    r="30" 
                                    stroke-width="4" 
                                    v-bind:stroke-dashoffset="dashOffsetTwo"
                                    fill="none" />
                        </svg>
                    </div>
                    <div v-if="two_percent == 100">
                        <i class="fas fa-check absolute text-blue-lighter" style="margin: 12px 0px 0px 10px;"></i>
                    </div>
                    <div v-else-if="two_percent < 1"></div>
                    <div v-else>
                        
                        <div class="absolute text-xl" style="margin: 15px 0px 0px 8px;">
                            @{{ two_percent }}%
                        </div>

                    </div>
                </div>
                <div class="mt-3 ml-6">
                    Election Data
                </div>
            </div>
            <div class="flex text-2xl text-white mb-8">

                <div class="border-4 border-white rounded-full w-16 h-16 text-4xl relative">

                    <div class="prog prog3">
                        <svg height="80" width="80">
                            <circle cx="35" 
                                    cy="35" 
                                    r="30" 
                                    stroke-width="4" 
                                    v-bind:stroke-dashoffset="dashOffsetThree"
                                    fill="none" />
                        </svg>
                    </div>
                    <div v-if="three_percent == 100">
                        <i class="fas fa-check absolute text-blue-lighter" style="margin: 12px 0px 0px 10px;"></i>
                    </div>
                    <div v-else-if="three_percent < 1"></div>
                    <div v-else>
                        
                        <div class="absolute text-xl" style="margin: 15px 0px 0px 8px;">
                            @{{ three_percent }}%
                        </div>

                    </div>
                </div>
                <div class="mt-3 ml-6">
                    District Info
                </div>
            </div>

            <div class="text-right text-white text-xl">
                @{{ total_percent }}%
            </div>

            <div class="w-full h-2 bg-white relative">

                <div class="absolute h-2 bg-blue-light pin-l" v-bind:style="totalWidth">
                </div>
                
            </div>

            <div v-if="data_update.completed_at" class="text-center h-24 overflow-hidden">
                <a href="/home" class="block hover:bg-white mt-8 mx-auto border-2 rounded-full px-4 py-2 uppercase bg-grey-lightest text-blue-dark w-1/3">
                    Go To Dashboard!
                </a>
            </div>
            <div v-else class="h-24 overflow-hidden">

            </div>
        

        </div>

     

    </div>




@endsection

@section('javascript')

    <!-- Vue functionality -->
        <script type="text/javascript">
            Vue.config.devtools = true;
            var app = new Vue({
              el: '#data-loader',
              data: {
                @if ($du)
                    data_update: JSON.parse('{!! $du->toJson() !!}'),
                @else
                    data_update: {},
                @endif
                check: 0,
              },
              computed: {
                one_percent: function() {
                    if (this.data_update.total_count > 0) {
                        var one = (100 * (this.data_update.voter_current/this.data_update.voter_count));
                        return parseInt(one);
                    }
                    return 0;
                },
                two_percent: function() {
                    if (this.data_update.total_count > 0) {
                        var two = (100 * (this.data_update.elections_current/this.data_update.elections_count));
                        return parseInt(two);
                    }
                    return 0;
                },
                three_percent: function() {
                    if (this.data_update.total_count > 0) {
                        var three = (100 * (this.data_update.district_current/this.data_update.district_count));
                        return parseInt(three);
                    }
                    return 0;
                },
                total_percent: function() {
                    if (this.data_update.total_count > 0) {
                        var total = (100 * (this.data_update.total_current/this.data_update.total_count));
                        return parseInt(total);
                    }
                    return 0;
                },
                dashOffsetOne: function() {
                    return 190 - (190 * .01 * this.one_percent);
                },
                dashOffsetTwo: function() {
                    return 190 - (190 * .01 * this.two_percent);
                },
                dashOffsetThree: function() {
                    return 190 - (190 * .01 * this.three_percent);
                },
                totalWidth: function() {
                    return "width: " + this.total_percent + "%";
                },
                step: function() {
                    var step = 1;
                    if (this.one_percent > 99) {
                        step++;
                    }
                    if (this.two_percent > 99) {
                        step++;
                    }
                    return step;
                },

              },
              methods: {
                updateCheck: function(val) {
                    this.check = val;
                    this.check = 5;
                    console.log(this);
                    this.$forceUpdate();
                    //alert(this.check);
                },
                pullDataUpdate: function() {
                    var that = this;
                    axios
                      .get('/data-update/json')
                      .then(function (response) {
                            //that.updateCheck(1);
                            //alert(that.check);
                            // console.log(this.data_update)

                            // console.log(response.data)
                            that.data_update = response.data
                            // console.log(this.data_update)
                            // this.check = 1;
                            that.$forceUpdate()
                            // //alert(this.check)
                            
                        });
                }
              },
              mounted () {
                //this.updateCheck(2);
                var that = this;
                that.pullDataUpdate();
                var refresh = setInterval(function() {
                    that.pullDataUpdate();
                    if (that.data_update.completed_at) {
                        clearInterval(refresh);
                    }
                }, 800);
                
              }
            })

            Vue.config.devtools = true;

        </script>

@endsection
