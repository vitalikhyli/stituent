@extends('admin.base')


@section('title')
    Pages Checklist
@endsection

@section('breadcrumb')
    
    {!! Auth::user()->Breadcrumb('General Plan', 'plan_index', 'level_1') !!}

@endsection

@section('style')

    <style>

    </style>

@endsection

@section('main')

    

<div class="text-2xl font-sans">
    General Plan
</div>


<?php 
function Launch($date) {
    $launch = "2019-09-02";
    // $launch = "2019-10-07";
    $d = \Carbon\Carbon::parse($launch)->diffInDays($date, false);
    if ($d < 0) {
        echo ($d/-7);
    } elseif ($d == 0) {
        echo '<span class="bg-blue px-2 py-1 rounded-lg text-white">
        <i class="fas fa-rocket mr-1"></i>
        Launch
        </span>';
    }
}
function NextWeek($date) {
    return \Carbon\Carbon::parse($date)->addWeek()->format("n/j/Y");
}

function rowShow($date) {
    // dd(\Carbon\Carbon::now(), \Carbon\Carbon::parse($date)->addWeek());
    if (\Carbon\Carbon::parse($date)->addWeek()->diffInDays(now(), false) >= 7) {
        echo 'line-through text-grey h idden bg-grey-lighter';
    } else {
        echo 'text-xs';
    }
}

function getMonth($date) {
    if (\Carbon\Carbon::parse($date)->format("n") != \Carbon\Carbon::parse($date)->subWeek()->format("n")) {
        echo \Carbon\Carbon::parse($date)->format("F");
    }
}

function ShortDate($date)
{
    return \Carbon\Carbon::parse($date)->format("n/j");
}
?>

<table class="table mt-6 text-left text-xs w-full font-normal">
    <tr class="border-b-2 border-black bg-orange-lightest w-full">
        <th class="font-normal">Month</th>
        <th class="text-left font-normal ">Week</th>
        <th class="text-left font-normal text-blue">Launch</th>
        <th class="text-left  font-normal w-1/4">Coding</th>
        <th class="text-left font-normal w-1/4">Biz</th>
        <th class="text-left font-normal w-1/4">Other</th>
    </tr>

    <!--------------------------------------------/ April /-------------------->
    <?php $date = '2019-04-22'; ?>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">Basic functions structural design</td>
        <td class="cursor-pointer">Now = FluencyBase.com</td>
        <td class="cursor-pointer"></td>
    </tr>

    <!--------------------------------------------/ May /-------------------->

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">Refactoring / teams setup</td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">Admin system, BillyGoat link</td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>


    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">Data integration work</td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>


    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">Data admin system</td>
        <td class="cursor-pointer">Now = CommunityFluency.com</td>
        <td class="cursor-pointer">TOS / privacy draft</td>
    </tr>

    <!--------------------------------------------/ June /-------------------->

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">Goal = Data system functional</td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">UI work; Data error handling</td>
        <td class="cursor-pointer">MA demos x2</td>
        <td class="cursor-pointer"></td>
    </tr>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">UI work</td>
        <td class="cursor-pointer">MA demo #3 + CT demo</td>
        <td class="cursor-pointer">BillyGoat clients linked</td>
    </tr>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs">UI work</td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>

    <!--------------------------------------------/ July /-------------------->

    <tr class="border-t-2 border-black {{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer">MD demo</td>
        <td class="cursor-pointer"></td>
    </tr>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer">Basic TOS done</td>
    </tr>


    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>

    <!--------------------------------------------/ Aug /-------------------->

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer">NCSL 8/5-8/8 Nashville</td>
        <td class="cursor-pointer"></td>
    </tr>

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>
            <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer">TOS Final Lawyer-Approved</td>
    </tr>

            <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>


    <!--------------------------------------------/ Sep /-------------------->

            <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>

            <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>
            <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                


    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>             

    <!--------------------------------------------/ OCT  /-------------------->


    <tr class="border-t-2 border-black {{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr> 
    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>        

    <!--------------------------------------------/ NOV  /-------------------->
  

      <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>              

    <!--------------------------------------------/ DEC  /-------------------->


      <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>                

    <!--------------------------------------------/ 2020  /-------------------->

    <tr class="border-t-2 border-black {{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer">New Year!</td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer" colspan="2">BTU wins BRIT awards</td>
        <td class="cursor-pointer"></td>
    </tr> 

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>         

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr> 

        <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>      

    <tr class="{{ rowShow($date) }}">
        <td class="text-left">
            <?php $date = NextWeek($date); getMonth($date ); ?>
        </td>
        <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
        <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
        <td class="cursor-pointer text-xs"></td>
        <td class="cursor-pointer"></td>
        <td class="cursor-pointer"></td>
    </tr>  

   <tr class="{{ rowShow($date) }}">
       <td class="text-left">
           <?php $date = NextWeek($date); getMonth($date ); ?>
       </td>
       <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
       <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
       <td class="cursor-pointer text-xs"></td>
       <td class="cursor-pointer"></td>
       <td class="cursor-pointer"></td>
   </tr>      

   <tr class="{{ rowShow($date) }}">
       <td class="text-left">
           <?php $date = NextWeek($date); getMonth($date ); ?>
       </td>
       <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
       <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
       <td class="cursor-pointer text-xs"></td>
       <td class="cursor-pointer"></td>
       <td class="cursor-pointer"></td>
   </tr>  

   <tr class="{{ rowShow($date) }}">
       <td class="text-left">
           <?php $date = NextWeek($date); getMonth($date ); ?>
       </td>
       <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
       <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
       <td class="cursor-pointer text-xs"></td>
       <td class="cursor-pointer"></td>
       <td class="cursor-pointer"></td>
   </tr>      

   <tr class="{{ rowShow($date) }}">
       <td class="text-left">
           <?php $date = NextWeek($date); getMonth($date ); ?>
       </td>
       <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
       <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
       <td class="cursor-pointer text-xs"></td>
       <td class="cursor-pointer"></td>
       <td class="cursor-pointer"></td>
   </tr>  

   <tr class="{{ rowShow($date) }}">
       <td class="text-left">
           <?php $date = NextWeek($date); getMonth($date ); ?>
       </td>
       <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
       <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
       <td class="cursor-pointer text-xs"></td>
       <td class="cursor-pointer">Aug 10-13 NCSL - Indianapolis</td>
       <td class="cursor-pointer"></td>
   </tr>      

   <tr class="{{ rowShow($date) }}">
       <td class="text-left">
           <?php $date = NextWeek($date); getMonth($date ); ?>
       </td>
       <td class="cursor-pointer">{{ ShortDate($date) }}</a></td>
       <td class="cursor-pointer text-blue"><?php Launch($date) ?></td>
       <td class="cursor-pointer text-xs"></td>
       <td class="cursor-pointer"></td>
       <td class="cursor-pointer"></td>
   </tr>  
 

</table>



@endsection

