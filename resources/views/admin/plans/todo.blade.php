@extends('admin.base')


@section('title')
    JSON To-Do
@endsection

@section('breadcrumb')
    
    {!! Auth::user()->Breadcrumb('To Do', 'to_do_index', 'level_1') !!}

@endsection

@section('style')

    <style>


    </style>

@endsection

@section('main')


    <div class="text-2xl font-sans">
         JSON-stored To Do List
    </div>

    <form method="POST" id="todo_form" action="/admin/todo/save">

        <input type="submit" value="Save to JSON" class="bg-blue rounded-lg float-right text-white px-4 py-2 font-normal mb-4 shadow" />

    @csrf

    <table class="w-full font-normal">
    <?php $i=0; ?>


<!--         <tr class="border-b bg-grey-lighter font-bold">
            <td class="p-1">
                Category
            </td>
            <td class="p-1">
                Item
            </td>
            <td class="p-1">
                Done?
            </td>
        </tr> -->


        <tr class="border-b border-dashed bg-orange-lightest">
            <td class="p-2">
                <input type="text" class="rounded-lg p-2 border w-full font-bold shadow" name="{{ $i }}_cat" placeholder="New Category" />
            </td>
            <td class="p-2">
                <input type="text" class="rounded-lg p-2 border w-full font-bold shadow" name="{{ $i }}_item" placeholder="New Item" />
            </td>
            <td class="p-2">
                <label for="done">
                <input type="checkbox" id="done" name="{{ $i }}_done" name="" />
                Done
                </label>
            </td>
        </tr>

    @foreach($cats as $thecat) 
    @if($items->where('cat',$thecat)->where('done',0)->count() > 0)
    <tr class="border-b-2 border-blue font-bold text-blue">
        <td class="p-1 pt-4" colspan="3">
            {{ $thecat }}
        </td>
    </tr>  
        
        @foreach($items->where('cat',$thecat)->where('done',0) as $theitem)
            <?php $i++; ?>
            <tr class="{{ (!$loop->last) ? 'border-b border-dashed' : '' }} {{ ($theitem->done) ? 'bg-grey-light text-grey-dark' : '' }}">
                <td class="p-1 pl-10 w-1/4">
                    <input type="text"  class="{{ ($theitem->done) ? 'bg-grey-lighter' : '' }} rounded-lg p-2 border w-full" name="{{ $i }}_cat" value="{{ $theitem->cat }}" />
                </td>
                <td class="p-1">
                    <input type="text"  class="{{ ($theitem->done) ? 'bg-grey-lighter' : '' }} rounded-lg p-2 border w-full" name="{{ $i }}_item" value="{{ $theitem->item }}" />
                </td>
                <td class="p-1">
                    @if($theitem->done)
                        <input type="checkbox" id="{{ $i }}_done" name="{{ $i }}_done" checked name="" /> Done
                    @else
                        <input type="checkbox" id="{{ $i }}_done" name="{{ $i }}_done" name="" /> Done
                    @endif
                </td>
            </tr>
        @endforeach
        @endif
    @endforeach

    <tr>
        <td class="p-1 pt-8 text-center" colspan="3">
            <input type="submit" value="Save to JSON" class="bg-blue rounded-lg text-white px-4 py-2 font-normal mb-4 shadow" />
        </td>
    </tr> 

    <tr class="border-b-2 border-blue font-bold text-blue">
        <td class="p-1 pt-4" colspan="3">
            Done
        </td>
    </tr> 


       
    </tr>

        @foreach($items->where('done',1)->sortBy('cat') as $theitem)
            <?php $i++; ?>
            <tr class="{{ (!$loop->last) ? 'border-b border-dashed' : '' }} {{ ($theitem->done) ? 'bg-grey-light text-grey-dark' : '' }}">
                <td class="p-1 pl-10 w-1/4">
                    <input type="text"  class="{{ ($theitem->done) ? 'bg-grey-lighter' : '' }} rounded-lg p-2 border w-full" name="{{ $i }}_cat" value="{{ $theitem->cat }}" />
                </td>
                <td class="p-1">
                    <input type="text"  class="{{ ($theitem->done) ? 'bg-grey-lighter' : '' }} rounded-lg p-2 border w-full" name="{{ $i }}_item" value="{{ $theitem->item }}" />
                </td>
                <td class="p-1">
                    @if($theitem->done)
                        <input type="checkbox" id="{{ $i }}_done" name="{{ $i }}_done" checked name="" /> Done
                    @else
                        <input type="checkbox" id="{{ $i }}_done" name="{{ $i }}_done" name="" /> To Do
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <input type="hidden" name="total" value="{{ $i }}" />

<br />
<center>
         <input type="submit" value="Save to JSON" class="bg-blue rounded-lg text-white px-4 py-2 font-normal mb-4 shadow" />
</center>

    </form>

    <br />
    <br />

@endsection
