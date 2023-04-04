<div class="mt-3">

	<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">
		Voting History
	</div>

	@if(!$model->elections)

        <div class="text-grey-dark text-sm italic text-center">No Data Available</div>

    @else


        <div class="w-full text-sm">
            
            @foreach($model->elections_Pretty as $data)

            	@if($loop->iteration == 11)

            		<div id="show-all-elections-div" class="text-white w-full text-center p-2">
                		<button type="button" id="show-all-elections" class="rounded-lg bg-blue text-white px-2 py-1">
                			Show All {{ count($model->elections_Pretty) }}
                		</button>
            		</div>

            		<div id="remainder-of-elections" class="hidden">
            	@endif

                <div class="{{ ($data['off_year']) ? 'bg-grey-lighter' : '' }} flex w-full">
                    <div class="p-1 w-4/5">
                        <i class="fas fa-check-square mr-1 text-blue"></i> 
                        <span class="uppercase text-grey-dark">{{ $data['jurisdiction'] }}</span>
                        {{ $data['type'] }}
                        <span class="text-blue ml-1">{{ $data['date'] }}</span>
                    </div>
                    <div class="">
                        @if($data['registered_as'] == 'D')
                        <span class="text-blue">
                        @elseif($data['registered_as'] == 'R')
                        <span class="text-red">
                        @else
                        <span class="text-grey-dark">
                        @endif
                            {{ $data['registered_as'] }}
                        </span>


                        @if($data['voted_as'])

                            @if($data['voted_as'] == 'D')
                            <span class="text-blue">
                            @elseif($data['voted_as'] == 'R')
                            <span class="text-red">
                            @else
                            <span class="text-grey-dark">
                            @endif
                            
                            Voted {{ $data['voted_as'] }}

                            </span>
                        @endif
                    </div>
                </div>

            	@if($loop->iteration >= 11 && $loop->last)

            		<div id="show-fewer-elections-div" class="text-white w-full text-center p-2">
                		<button type="button" id="show-fewer-elections" class="rounded-lg bg-blue text-white px-2 py-1">
                			Show Only 10 Most Recent Elections
                		</button>
            		</div>

            		</div>
            	@endif

            @endforeach

        </div>

    @endif
</div>
