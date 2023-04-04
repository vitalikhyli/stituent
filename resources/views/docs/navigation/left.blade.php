<div class="text-sm font-sans whitespace-no-wrap" style="min-height:500px;">
    


    @foreach($navigation as $header => $contents)

        <div class="mb-4">

            <a class="no-underline text-grey-darker" href="{{ array_key_first($contents) }}">
                <div class="text-blue font-bold uppercase">
                  <!--   <span class="">
                        {{ $loop->iteration }}
                    </span>  -->{{ $header }}
                </div>
            </a>

            <div class="list-reset pr-2 w-full">


                @foreach($contents as $path => $item)
                
                    <a class="no-underline text-grey-darker" href="{{ $path }}">

                        <div class="{{ ($path == $current_path) ? 'bg-indigo-lightest' : '' }} border-transparent rounded-full px-4 py-1 capitalize flex">

                        
                            <div class="text-blue w-10 mr-1">
                                {{ $loop->parent->iteration }}.{{ $loop->iteration }}
                            </div>
                            <div>
                                {{ $item }}
                            </div>
                       

                        </div>
                    </a>
                @endforeach

            </div>

        </div>

    @endforeach     



</div>