<?php if (!defined('dir')) define('dir','/office'); ?>

<div id="entity_relationships" class="entity-tab-content  {{ ($tab != 'relationships') ? 'hidden' : '' }}" style="min-height: 700px;">

    <div class="flex flex-wrap border-t pt-8">


        <div class="w-1/2 flex-1 pl-2">


        	<div class="text-xl border-b-4 border-blue pb-2">

                <a href="{{dir}}/relationships/new-e2p/{{ $entity->id }}">
                    <button class="btn_newgroup float-right rounded-lg bg-blue border text-white text-xs px-2 py-1 my-1 cursor-pointer mr-2">Add Relationship
                    </button>
                </a>

                People
            </div>
            @if($entity->related_people()->count() <= 0)
<!--                 <div class="mt-2 p-2">
                    None
                </div> -->
            @else
            	@foreach($entity->related_people() as $r)
                    <div id="relationship_{{ $r->relationship_id }}" class="inline-flex w-full hover:bg-blue-lightest mt-2">

                        <div class="flex-grow">
                            <i class="fas fa-user-circle mr-1"></i>
                            <span class="flex-initial text-black"> {{ $r->kind }} of</span>
                            <a href="{{dir}}/constituents/{{ $r->id }}">{{ $r->full_name }}</a>
                        </div>
                        
                        <div class="float-right w-12 ml-2">
                            <a href="{{dir}}/relationships/{{ $r->relationship_id }}/edit">
                            <button class="text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                                Edit
                            </button>
                            </a>
                        </div>

                    </div>
            	@endforeach
            @endif

            @if($entity->related_people_reverse()->count() > 0)

                @if($entity->related_people()->count() > 0)
                    <div class="mt-3 border-b-4 border-grey-light text-grey-darker py-1 rounded-t">
                        <!-- Linked to by others -->
                    </div>
                @endif

                @foreach($entity->related_people_reverse() as $r)
                    <div id="relationship_{{ $r->relationship_id }}" class="inline-flex w-full hover:bg-blue-lightest mt-2">

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

        <div class="w-1/2 flex-1 pl-2">

            <a href="{{dir}}/relationships/new-e2e/{{ $entity->id }}">
                <button class="btn_newgroup float-right rounded-lg bg-blue border text-white text-xs px-2 py-1 my-1 cursor-pointer mr-2">Add Relationship
                </button>
            </a>

        	<div class="text-xl border-b-4 border-blue pb-2">Organizations</div>
            @if($entity->related_entities()->count() <= 0)
<!--                 <div class="mt-2 p-2">
                    None
                </div> -->
            @else
            	@foreach($entity->related_entities() as $r)
                    <div id="relationship_{{ $r->relationship_id }}" class="inline-flex w-full hover:bg-blue-lightest mt-2">

                        <div class="flex-grow">
                            <i class="fas fa-building mr-1"></i>
                            <span class="flex-initial text-black"> {{ $r->kind }} of</span>
                            <a href="{{dir}}/entities/{{ $r->id }}">{{ $r->name }}</a>
                        </div>
                        
                        <div class="float-right w-12 ml-2">
                            <a href="{{dir}}/relationships/{{ $r->relationship_id }}/edit">
                            <button class="text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                                Edit
                            </button>
                            </a>
                        </div>

                    </div>
            	@endforeach
            @endif

            @if($entity->related_entities_reverse()->count() > 0)

                @if($entity->related_entities()->count() > 0)
                    <div class="mt-3 border-b-4 border-grey-light text-grey-darker py-1 rounded-t">
                        <!-- Linked to by others -->
                    </div>
                @endif

                @foreach($entity->related_entities_reverse() as $r)
                    <div id="relationship_{{ $r->relationship_id }}" class="inline-flex w-full hover:bg-blue-lightest mt-2">

                        <div class="flex-grow">
                            <i class="fas fa-building mr-1"></i>
                            <a href="{{dir}}/entities/{{ $r->id }}">{{ $r->name }}</a>
                            <span class="flex-initial text-black"> is {{ $r->kind }}</span>
                        </div>

                    </div>
                @endforeach
            @endif

        </div>



    </div>


</div>
