<div class="mr-8 p-2 text-sm text-center bg-grey-lightest text-grey-dark shadow-lg rounded-lg">
        <div class="text-black font-bold">

            You're in "sandbox" mode

            <button type="button" class="red-tooltip ml-1 rounded-full bg-blue-dark uppercase text-xs text-white px-2 py-1" data-toggle="tooltip" data-placement="right" title="You are now exploring an example account with some fake data already in the system. Feel free to experiment, make your own entries, and make any changes you want.">
                <i class="fas fa-question"></i>
            </button>

        </div>

        @php
            $minutes_to_explore = config('app.sandbox_timeout');
        @endphp

        <span id="countdown" class="text-blue-dark text-lg">{{ Carbon\Carbon::parse(Auth::user()->last_login)->addMinutes($minutes_to_explore)->diffInMinutes() }}:{{ str_pad( 
                        (Carbon\Carbon::parse(Auth::user()->last_login)->addMinutes($minutes_to_explore)->diffInSeconds()
                         - (60 * Carbon\Carbon::parse(Auth::user()->last_login)->addMinutes($minutes_to_explore)->diffInMinutes())),
                         2, 0, STR_PAD_LEFT) }} left to explore
        </span>
</div>

<script type="text/javascript">

    var countDownDate = {!! Carbon\Carbon::parse(Auth::user()->last_login)->addMinutes($minutes_to_explore)->timestamp !!};

    var x = setInterval(function() {

        var now = Date.now() / 1000;
        var now = Math.floor(now);

        var distance = countDownDate - now;
        var minutes = Math.floor(distance % (60 * 60) / 60);
        var seconds = Math.floor(distance % 60);

        document.getElementById("countdown").innerHTML = minutes + ":" + (seconds < 10 ? '0' : '') + seconds + ' left to explore';

        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown").innerHTML = "Feel free to log in again.";
            setTimeout(window.location.href = '/u', 1000);
        }
    }, 1000);
</script>