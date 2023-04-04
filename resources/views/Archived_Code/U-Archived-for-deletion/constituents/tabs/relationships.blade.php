<?php if (!defined('dir')) define('dir','/u'); ?>

<!-- <div id="person_relationships" class="person-tab-content  {{ ($tab != 'relationships') ? 'hidden' : '' }}" style="min-height: 700px;">
 -->

    <div class="mt-4 mb-8">

@if(IDisVoter($person->id))

@else


        <div class="text-xl font-sans pb-1 border-b-4 border-blue">
            Relationships
        </div>


        <div class="mt-4">


        	<div class="text-base border-b pb-2 text-grey-darker">

                <a href="{{dir}}/relationships/new-p2p/{{ $person->id }}">
                    <button class="btn_newgroup float-right rounded-lg bg-blue border text-white text-xs px-2 py-1 my-1 cursor-pointer"><i class="fas fa-plus"></i></button>
                </a>

                People
            </div>
            @if($person->related_people()->count() <= 0)
                <!-- <div class="mt-2 p-2">
                    None
                </div> -->
            @else
            	@foreach($person->related_people() as $r)
                    <div id="relationship_{{ $r->relationship_id }}" class="inline-flex w-full hover:bg-blue-lightest mt-2 pl-4">

                        <div class="flex-grow">
                            <i class="fas fa-user-circle mr-1"></i>
                            <span class="flex-initial text-black"> {{ $r->kind }} of</span>
                            <a href="{{dir}}/constituents/{{ $r->id }}">{{ $r->full_name }}</a>
                        </div>
                        
                        <div class="float-right w-12 ml-2 text-right">
                            <a href="{{dir}}/relationships/{{ $r->relationship_id }}/edit">
                            <button class="text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                                Edit
                            </button>
                            </a>
                        </div>

                    </div>
            	@endforeach
            @endif

            @if($person->related_people_reverse()->count() > 0)

                @if($person->related_people()->count() > 0)
                    <div class="text-grey-darker rounded-t">
                        <!-- Linked to by others -->
                    </div>
                @endif

                @foreach($person->related_people_reverse() as $r)
                    <div id="relationship_{{ $r->relationship_id }}" class="inline-flex w-full hover:bg-blue-lightest mt-2 pl-4">

                        <div class="flex-grow">
                            <i class="fas fa-user-circle mr-1"></i>
                            <a href="{{dir}}/constituents/{{ $r->id }}">{{ $r->full_name }}</a>
                            <span class="flex-initial text-black"> is {{ $r->kind }}</span>
                        </div>

                    </div>
                @endforeach
            @endif


        </div>


        <!------------------------------------- ENTITIES ------------------------------------->

        <div class="flex-1  mt-8">

            <a href="{{dir}}/relationships/new-p2e/{{ $person->id }}">
                <button class="btn_newgroup float-right rounded-lg bg-blue border text-white text-xs px-2 py-1 my-1 cursor-pointer"><i class="fas fa-plus"></i></button>
            </a>

        	<div class="text-base border-b pb-2 text-grey-darker">Organizations</div>
            @if($person->related_entities()->count() <= 0)
<!--                 <div class="mt-2 p-2">
                    None
                </div> -->
            @else
            	@foreach($person->related_entities() as $r)
                    <div id="relationship_{{ $r->relationship_id }}" class="inline-flex w-full hover:bg-blue-lightest mt-2 pl-4">

                        <div class="flex-grow">
                            <i class="fas fa-building mr-1"></i>
                            <span class="flex-initial text-black"> {{ $r->kind }} of</span>
                            <a href="{{dir}}/entities/{{ $r->id }}">{{ $r->name }}</a>
                        </div>
                        
                        <div class="float-right w-12 ml-2 text-right">
                            <a href="{{dir}}/relationships/{{ $r->relationship_id }}/edit">
                            <button class="text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                                Edit
                            </button>
                            </a>
                        </div>

                    </div>
            	@endforeach
            @endif

            @if($person->related_entities_reverse()->count() > 0)
                @if($person->related_entities()->count() > 0)
                    <div class="text-grey-darker rounded-t">
                        <!-- Linked to by others -->
                    </div>
                @endif
                
                @foreach($person->related_entities_reverse() as $r)
                    <div id="relationship_{{ $r->relationship_id }}" class="inline-flex w-full hover:bg-blue-lightest mt-2 pl-4">

                        <div class="flex-grow">
                            <i class="fas fa-building mr-1"></i>
                            <a href="{{dir}}/entities/{{ $r->id }}">{{ $r->name }}</a>
                            <span class="flex-initial text-black"> is {{ $r->kind }}</span>
                        </div>

                    </div>
                @endforeach
            @endif

        </div>

@endif


    </div>

