<?php

namespace App\Http\Livewire;

use App\CampaignList;
use App\District;
use App\Group;
use App\Traits\ListBuilderTrait;
use App\Voter;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class ListBuilder extends Component
{
    use WithPagination;
    use ListBuilderTrait;

    public $list_id = null;
    public $edit = false;

    public $current_count = 0;
    public $debug = '';
    public $num_selected = 0;
    public $ward_selected_cities = [];

    public $list_name = '';

    public $show_preview = false;
    public $preview_per_page = 50;

    public $input = [];
    // public $text = [
    // 	'fred' => '',
    // ];

    public $ask_delete = false;

    public $include_deceased = false;
    public $include_archived = false;  

    public $elections;

    public $explain_state;
    public $explain_local;

    public $election_types;

    public function mount($list = null, $edit = null)
    {
        $this->input = $this->getInput(); // in Trait

        if ($list) {
            $this->list_id = $list->id;
            $this->list_name = $list->name;

            foreach ($list->form as $field => $val) {
                $this->input[$field] = $val;
            }
        }

        // dd($this->input['municipalities']);
        $this->elections = $this->getSampleElections();

        if ($edit) {
            $this->edit = true;
        }

        $this->election_types = ['SP000' => 'State Primary',
                                 'L0000' => 'Municipal'];
    }

    public function deleteFlexQuery($key)
    {
        $this->input['flexqueries'][$key]['delete'] = true;
    }

    public function deletePendingFlexQueries()
    {
        foreach($this->input['flexqueries'] as $key => $q) {
            if(isset($q['delete'])) {
                unset($this->input['flexqueries'][$key]);
            }
        }
    }

    public function setAskDelete($tof)
    {
        $this->ask_delete = $tof;
    }

    public function addStreet($city, $new_street)
    {
        $new_street_slug = Str::slug($new_street, '_');
        $street_arr = [];
        $street_arr['name'] = $new_street;
        $street_arr['from'] = null;
        $street_arr['to'] = null;
        $this->input['streets'][$city][$new_street_slug] = $street_arr;
        $this->input['new_streets'][$city] = null;
    }

    public function removeStreet($city, $street_slug)
    {
        unset($this->input['streets'][$city][$street_slug]);
        // dd($this->input, $city, $street_slug);
    }

    public function toggleShowPreview()
    {
        $this->show_preview = ! $this->show_preview;
    }

    public function removeFilter($import_id, $filtercol)
    {
        if (isset($this->input['full_imports'][$import_id]['filters'][$filtercol])) {
            unset($this->input['full_imports'][$import_id]['filters'][$filtercol]);
        }
    }

    public function save()
    {
        if ($this->list_id) {
            $list = CampaignList::find($this->list_id);
        } else {
            $list = new CampaignList;
        }
        $list->static_count = null;

        $list->team_id = Auth::user()->current_team_id;
        $list->user_id = Auth::user()->id;
        $list->name = $this->list_name;
        $list->form = $this->input;
        //dd($list);
        $list->save();

        return redirect('/campaign/lists');
    }

    public function render()
    {
        $logtime = logTime([], 'START');

        foreach ($this->input['new_streets'] as $city => $street) {
            if ($street) {
                $this->addStreet($city, $street);
            }
        }
        $this->input['groups'] = [];
        $group_ids = collect([]);
        foreach ($this->input['categories'] as $cat_id => $cat_group_ids) {
            if (count($cat_group_ids) > 0) {
                $group_ids = $group_ids->merge($cat_group_ids);
            }
        }
        $this->input['groups'] = Group::whereIn('id', $group_ids)->orderBy('name')->get();

        // Fancy filtering imports
        foreach ($this->input['full_imports'] as $import_id => $filter_arr) {
            if (isset($filter_arr['new_filter']) && $filter_arr['new_filter']) {
                if (!isset($this->input['full_imports'][$import_id]['filters'])) {
                    $filters = [$filter_arr['new_filter'] => ''];
                } else {
                    $filters = $this->input['full_imports'][$import_id]['filters'];
                    if (!isset($filters[$filter_arr['new_filter']])) {
                        $filters[$filter_arr['new_filter']] = '';
                    }
                }
                $this->input['full_imports'][$import_id]['filters'] = $filters;
                $this->input['full_imports'][$import_id]['new_filter'] = '';
                //dd($this->input['full_imports']);
            }
        }

        //dd($this->input);

        $main_query = $this->buildMainQuery();
        $logtime = logTime($logtime, 'QUERY');
        $house_districts = $this->getHouseDistricts();
        //dd($house_districts);
        $logtime = logTime($logtime, 'HOUSE');
        $senate_districts = $this->getSenateDistricts();
        $logtime = logTime($logtime, 'SENATE');
        $congressional_districts = $this->getCongressionalDistricts();
        $logtime = logTime($logtime, 'CONGRESS');
        $municipalities = $this->getMunicipalities();
        $logtime = logTime($logtime, 'MUNICIPAL');
        $categories = $this->getCategories();
        $logtime = logTime($logtime, 'CATEGORIES');
        $tags = Auth::user()->team->tags()->orderBy('name')->get();
        
        $logtime = logTime($logtime, 'TAGS');
        $imports = Auth::user()->team->userImports()->orderBy('name')->get();
        $logtime = logTime($logtime, 'IMPORTS');
        $subtract_lists = Auth::user()->team->campaignLists()->orderBy('name')->get();
        $logtime = logTime($logtime, 'LISTS');
        $zipcodes = $this->getZipCodes();

        $logtime = logTime($logtime, 'ZIPS');
        $ethnicities = $this->getEthnicities();
        $logtime = logTime($logtime, 'ETHNICITIES');

        $preview_voters = null;
        if ($this->show_preview) {
            $preview_voters = $main_query->get();
            // $preview_voters = $main_query->paginate($this->preview_per_page);
        }
        $logtime = logTime($logtime, 'PREVIEW');
        //dd($logtime);
        krsort($this->elections);



        $this->deletePendingFlexQueries();
        // dd($preview_voters);
        return view('livewire.list-builder', compact(
                        'congressional_districts',
                        'senate_districts',
                        'house_districts',
                        'municipalities',
                        'categories',
                        'tags',
                        'imports',
                        'preview_voters',
                        'logtime',
                        'zipcodes',
                        'subtract_lists',
                        'ethnicities',
                    ));
    }
}
