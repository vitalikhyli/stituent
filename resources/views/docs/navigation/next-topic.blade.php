@if($next_topic)

    <a href="{{ array_key_first($next_topic) }}">
        <div class="text-grey-dark uppercase border-t-2 pt-2 mt-8">
            Next Topic <i class="fas fa-arrow-right"></i>
        </div> 

        <div class="text-2xl font-bold capitalize">
             {{ Arr::first($next_topic) }}
        </div>
    </a>

@endif