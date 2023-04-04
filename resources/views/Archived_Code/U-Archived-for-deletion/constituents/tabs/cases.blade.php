<?php if (!defined('dir')) define('dir','/u'); ?>

<!-- <div id="person_notes" class="border-t person-tab-content text-base w-full  {{ ($tab != 'notes') ? 'hidden' : '' }}"> -->

<div class="mb-8">

<div class="text-xl font-sans mt-4 pb-1 border-b-4 border-blue">
    Cases
</div>

@if(isset($cases) && ($cases->count() > 0))

    @foreach($cases->where('resolved',false) as $thecase)
        <a href="{{dir}}/cases/{{ $thecase->id }}">
            <div class="hover:bg-orange-lightest mb-2 px-4 pt-2">
                <span class="text-grey-darkest">
                <i class="fas fa-folder-open text-xl mr-2 "></i>
                </span>{{ $thecase->subject}}
                <span class="float-right text-sm text-grey-dark">
                ({{$thecase->contacts->count() }} notes)
                </span>
            </div>
        </a>
    @endforeach
  
    @foreach($cases->where('resolved',true) as $thecase)
        <a href="{{dir}}/cases/{{ $thecase->id }}">
            <div class="opacity-50 hover:bg-orange-lightest mb-2 px-4 pt-2">
                <span class="text-grey-darkest">
                <i class="fas fa-folder-open text-xl mr-2 "></i>
                </span>{{ $thecase->subject}}
                <span class="float-right text-sm text-grey-dark">
                ({{$thecase->contacts->count() }} notes)
                </span>
            </div>
        </a>
    @endforeach

@else
    <div class="text-grey-dark p-2">
        None
    </div>
@endif

</div>
