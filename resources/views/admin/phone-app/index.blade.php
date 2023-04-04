@extends('admin.base')

@section('title')
    Phone App
@endsection


@section('main')

	<div class="text-3xl font-black border-b-4 py-2">

        Phone App
    </div> 

    <div class="table text-grey-dark">
        @foreach ($office_teams as $team)

            @php 
                $once = false;
            @endphp

            @foreach ($team->users->sortBy('name') as $user)

                @if ($loop->first)
                    <div class="table-row">
                        <div class="table-cell p-1 border-b-2 pb-8"></div>
                        <div class="table-cell p-1 border-b-2 pb-8"></div>
                        <div class="table-cell p-1 border-b-2 pb-8"></div>
                        <div class="table-cell p-1 border-b-2 pb-8"></div>
                        <div class="table-cell p-1 border-b-2 pb-8"></div>
                    </div>
                @endif

                @if (!$user->active)
                    @continue
                @endif

                <div id="user_{{ $user->id }}" class="table-row hover:bg-grey-lightest">
                    <div class="table-cell p-1 uppercase font-bold text-grey-darkest">
                        @if ($loop->first || !$once)
                            {{ $team->name }}
                            @php
                                $once = true;
                            @endphp
                        @endif
                    </div>
                    <div class="table-cell w-8">
                        @if(isset($last_year_lookup[$user->id]))
                            <i class="fa fa-check-circle"></i>
                        @endif
                    </div>
                    <div class="table-cell p-1
                                @if(isset($last_year_lookup[$user->id]))
                                    text-grey-darkest
                                @endif
                                 border-b">

                        @if (isset($last_year_lookup[$user->id]))
                            <div class="float-right text-grey">
                                {{ $last_year_lookup[$user->id] }}
                            </div>
                        @endif
                        {{ $user->name }}
                    </div>
                    <div class="table-cell p-1 border-b">
                        @if (strpos('a'.$user->email, 'placeholder') > 0)
                            <span class="text-red-light">{{ $user->email }}</span>
                        @else
                            {{ $user->email }}
                        @endif
                    </div>
                    <div class="table-cell p-1 border-b border-l">
                        <div class="text-right">
                            <form action="" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <input type="hidden" name="user_id" value="{{ $user->id }}" />
                                <button type="submit" class="text-xs">
                                    Add Device
                                </button>
                            </form>
                        </div>


                        @if ($user->devices->count() > 0)

                            @foreach ($user->devices as $device)
                                <div class="flex items-center p-1 whitespace-no-wrap">
                                    @if ($device->live_at)
                                        <i title="{{ $device->device_info }}" class="fa fa-mobile text-blue text-lg"></i>
                                        <span class="pl-2 text-xs">{{ $device->live_at->format('n/j/Y g:ia') }}</span>
                                    @else
                                        <i class="fa fa-mobile fa-2x"></i>
                                        <span class="pl-2 text-black font-bold">
                                            {{ $device->pin }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            
                        @endif
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>

@endsection