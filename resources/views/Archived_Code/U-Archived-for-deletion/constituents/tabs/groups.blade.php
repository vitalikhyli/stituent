<?php if (!defined('dir')) define('dir','/u'); ?>

<!-- <div id="person_groups" class="person-tab-content flex {{ ($tab != 'groups') ? 'hidden' : '' }}"> -->
<div>
   

    <!-- <div class="border-t py-2 text-base w-full flex flex-wrap"> -->
                

        @if(isset($groupcats))
        @foreach($groupcats as $thecat)
            <div class="w-full">
                <div class="text-grey-darkest">

                    <div class="text-base pb-2 my-2 cursor-pointer border-b">


                    @if ($person->groups->where('category_id', $thecat->id)->count() <
                    \App\Group::where('category_id', $thecat->id)->where('team_id',Auth::user()->team->id)->count())
                            <a href="{{dir}}/constituents/{{ $person->id }}/category/{{ $thecat->id }}/new">
                            <button class="btn_newgroup float-right rounded-lg bg-blue border text-white text-xs px-2 py-1 mt-1 mb-2 cursor-pointer"><i class="fas fa-plus"></i></button>
                            </a>
                    @endif

                        <span class="capitalize">{{ $thecat->name }}</span>

                    </div>

                    <div class="text-sm mb-4">
                    @if($person->groups->where('category_id', $thecat->id)->count() <= 0)

                        <div class="p-2 text-grey-darker">None</div>

                    @else
                    @foreach($person->groups->where('category_id', $thecat->id) as $thegroup)


                        <a href="{{dir}}/groups/instance/{{ $thegroup->pivot->id }}">
                        <div class="p-2 {{ (!$loop->last) ? 'border-b' : '' }} hover:bg-grey-lightest">

                        <div class="float-right">
                            Edit
                        </div>


                        <i class="fas fa-flag mr-2 w-4"></i> {{ $thegroup->name }}

                            @if(isset($thegroup->pivotdata->position))
                                <div class="ml-8 text-grey-darkest">
                                    Position: <span class="font-bold capitalize">{{ $thegroup->pivotdata->position }}</span>
                                </div>
                            @endif

                            @if(isset($thegroup->pivotdata->notes))
                                <div class="ml-8 text-grey-darker">
                                    {{ $thegroup->pivotdata->notes }}
                                </div>
                            @endif

                        </div>
                        </a>

                    @endforeach
                    @endif

                    </div>


                </div>
            </div>
        @endforeach

        @else

            No groups yet.
            
        @endif

    <!-- </div> -->

</div>
