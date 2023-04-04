<?php if (!defined('dir')) define('dir','/u'); ?>

<!-- <a href="{{dir}}/emails/{{ $thecontact->id }}/edit" class="text-blue"> -->
    <div class="flex border-b">
        <div class="pt-2 pr-2">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="text-black p-2 w-full">

            <div class="float-right w-12 ml-2">
                <a href="{{dir}}/emails/{{ $thecontact->id }}/edit">
                <button class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                    View
                </button>
                </a>
            </div>

            <div class="font-bold text-sm">
                {{ \Carbon\Carbon::parse($thecontact->date)->format("n/j/y") }}
            </div>

            <div class="flex">
                <div class="text-blue w-16">Name:</div> {{ $thecontact->name }}
            </div>
            
            <div class="flex">
                <div class="text-blue w-16">Subj:</div> {{ $thecontact->subject }}
            </div>
            
        </div>


    </div>
<!-- </a> -->
