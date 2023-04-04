<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Campaign\Opportunity;



class OpportunitiesController extends Controller
{
    public function index()
    {
    	return view('campaign.opportunities.index');
    }

    public function show($type, $id)
    {
    	if ($type == 'canvass')		return $this->showCanvass($id);
        if ($type == 'phonebank')   return $this->showPhonebank($id);
    	if ($type == 'schedule')	return $this->showSchedule($id);
    }

    //----------------------------------------------------------------------------

    public function showCanvass($id)
    {
    	$opp = Opportunity::find($id);
    	return view('campaign.opportunities.canvass.show', ['opp' => $opp]);
    }

    public function showPhonebank($id)
    {
        $opp = Opportunity::find($id);
        return view('campaign.opportunities.phonebank.show', ['opp' => $opp]);
    }

    public function showSchedule($id)
    {
    	$opp = Opportunity::find($id);
    	return view('campaign.opportunities.schedule.show', ['opp' => $opp]);
    }

}
