<?php

namespace App\Http\Livewire\Constituents;

use Livewire\Component;

use App\Person;
use App\Voter;
use App\Category;
use App\Group;
use App\District;

use PDF;
use Auth;
use Response;

use App\Traits\LivewireConstituentQueryTrait;
use App\Traits\ExportTrait;
 

function getPublicObjectVars($obj) {
    $vars = get_object_vars($obj);
    unset($vars['id']);
    unset($vars['redirectTo']);
    return $vars;
}

class QueryForm extends Component
{
	use LivewireConstituentQueryTrait;
    use ExportTrait;

    //////////////////////////////////[ LISTENERS ]////////////////////////////////////////

    protected $listeners = [
        'pass_selected_groups'      => 'receiveSelectedGroups',
        'pass_selected_districts'   => 'receiveSelectedDistricts',
        'pass_selected_cities'      => 'receiveSelectedCities',
        'pass_precincts'            => 'receivePrecincts',
        'pass_selected_zips'        => 'receiveSelectedZips',
        'pass_selected_emails'      => 'receiveSelectedEmails',
        'clear_all'                 => 'clearAll',
        'send_export_fields_and_begin_download' => 'receiveExportFieldsAndDownload'
    ];

    public function receiveSelectedGroups($array)
    {
        $this->selected_groups = $array;
        $this->untouched = false;
    }

    public function receiveSelectedDistricts($array)
    {
        $this->selected_districts = $array;
        $this->untouched = false;
    }

    public function receiveSelectedCities($array)
    {
        $this->selected_cities = $array;
        $this->untouched = false;
    }
    public function receivePrecincts($precincts)
    {
        $this->precincts = $precincts;
        $this->untouched = false;
    }

    public function receiveSelectedZips($array)
    {
        $this->selected_zips = $array;
        $this->untouched = false;
    }

    public function receiveSelectedEmails($array)
    {
        $this->selected_emails = $array;
        $this->untouched = false;
    }

    public function clearAll()
    {
        $this->linked               = false;
        $this->ignore_archived      = true;
        $this->ignore_deceased      = true;
        $this->first_name           = null;
        $this->middle_name          = null;
        $this->last_name            = null;
        $this->email                = null;
        $this->stree                = null;
        $this->municipalities       = [];
        $this->congress_districts   = [];
        $this->senate_districts     = [];
        $this->house_districts      = [];
        $this->zips                 = [];
        $this->parties              = [];
        $this->age                  = null;
        $this->age_operator         = null;
        $this->master_email         = false;
        $this->voter_has_email      = false;
        $this->per_page             = 100;
        $this->order_by             = 'last_name';
        $this->order_direction      = 'asc';

        $this->selected_groups    = [];
        $this->selected_districts = [];
        $this->selected_cities    = [];
        $this->selected_zips      = [];
        $this->selected_emails    = [];
        $this->untouched = true;
    }

    public function receiveExportFieldsAndDownload($data)
    {
        if (!Auth::user()->permissions->export) return
        set_time_limit(-1);

        $export_fields          = $data['export_fields'];
        $include_groups         = $data['include_groups'];
        $include_voter_phones   = $data['include_voter_phones'];
        $householding           = $data['householding'];
        $filename               = $data['filename'];

        $input          = getPublicObjectVars($this);
        $constituents   = $this->constituentQuery($input, $limit = 'none');

        if (!$filename) return;
        if (!$constituents) return;

        // Flip + reorder the column names
        $count = 0;
        $column_names   = collect($export_fields)->reject(function ($item) {
                            return $item !== true;
                          })->map(function ($item) use (&$count) { return $count++; })
                            ->flip()
                            ->toArray();
        $correct_order  = $this->getConstituentFields($basic = true);                          
        $column_names   = $correct_order->intersect($column_names)->values();
                            
        $file_array = $this->createCSVFileFromConstituents($input,
                                                           $constituents,
                                                           $column_names,
                                                           $filename,
                                                           $include_groups,
                                                           $include_voter_phones,
                                                           $householding);

        $headers        = $file_array['headers'];
        $filename_full  = $file_array['filename_full'];
        $filename       = $file_array['filename'];

        return Response::download($filename_full, $filename, $headers);
    }

    //////////////////////////////////[ VARIABLES ]////////////////////////////////////////

    public $export_mode        = false;
    public $search_value;
    public $time;
    public $untouched          = true;
    public $loaded_times;
	public $open               = [];
	public $selected_groups    = [];   // Comes in from from other component
    public $selected_districts = [];   // Comes in from from other component
    public $selected_cities    = [];   // Comes in from from other component
    public $selected_zips      = [];   // Comes in from from other component
    public $selected_emails    = [];   // Comes in from from other component
    public $precincts;

	public $linked             = false;
    public $ignore_archived    = true;
    public $ignore_deceased    = true;
	public $first_name;
	public $middle_name;
	public $last_name;
	public $email;
    public $street;
    public $municipalities      = [];
    public $congress_districts  = [];
    public $senate_districts    = [];
    public $house_districts     = [];
    public $zips                = [];
    public $parties             = [];
    public $age;
    public $age_operator;
    public $master_email        = false;
    public $voter_has_email     = false;
    public $per_page           = 100;
    public $order_by           = 'last_name';
    public $order_direction    = 'asc';

    //////////////////////////////////[ FUNCTIONS ]////////////////////////////////////////

	public function toggleOpen($a, $b)
	{
		$this->open[$a][$b] = ($this->open[$a][$b]) ? false : true;
	}

	public function toggleLinked()
	{
		$this->linked = ($this->linked) ? false : true;
	}

    public function toggleArchive()
    {
        $this->ignore_archived = ($this->ignore_archived) ? false : true;
    }

    public function toggleDeceased()
    {
        $this->ignore_deceased = ($this->ignore_deceased) ? false : true;
    }

    ////////////////////////////////////[ LIFECYCLE ]////////////////////////////////////////

    public function mount()
    {
        //
    }

    public function printLabels()
    {
        $input          = getPublicObjectVars($this);
        $constituents   = $this->constituentQuery($input, $limit = 'none');

        $count = $constituents->count();

        //dd($pdf);
        $pdfContent = PDF::loadView('shared-features.exports.labels-5160-pdf', 
                             compact('constituents'))->output();

        return response()->streamDownload(function() use ($pdfContent) {
                print($pdfContent);

            }, "Avery-5160-Labels-$count-Constituents-".date('Y-m-d').".pdf");
    }

    public function printHouseholdLabels()
    {
        $input          = getPublicObjectVars($this);
        $original_constituents   = $this->constituentQuery($input, $limit = 'none');

        $constituents_householded = $original_constituents->groupBy('full_address');
        $constituents = collect([]);
        $count = 0;
        foreach ($constituents_householded as $household => $constituents_in_house) {

            // Group by last names of residents
            $count++;
            $last_names = $constituents_in_house->where('last_name', '<>', null)
                                                ->groupBy('last_name')
                                                ->map(function ($last_names) {
                                                    return $last_names->count();
                                                });
                                             
            if ($last_names->count() > 2) {

                $family = "";
                $loopcount = 0;
                $total = $last_names->count();
                foreach ($last_names as $lastname => $lastname_count) {
                    if (strlen($family) > 20) {
                        $family .= '+';
                        continue;
                    }
                    $loopcount++;
                    if ($loopcount == $total) {
                        $family .= $lastname;
                    } else {
                        $family .= $lastname."/";
                    }
                }
                if (strlen($family) < 25) {
                    $family .= " Household";
                }

            } elseif ($last_names->count() == 2) {
                $family = "";
                $loopcount = 0;
                foreach ($last_names as $lastname => $lastname_count) {
                    $loopcount++;
                    if ($loopcount == 2) {
                        $family .= $lastname;
                    } else {
                        $family .= $lastname." and ";
                    }
                }
                $family .= " Household";
            } elseif ($last_names->count() == 1) {
                if ($constituents_in_house->count() > 1) {

                    // One last name, multiple people
                    $family = $constituents_in_house->first()->last_name.' Household';
                } else {

                    // One person
                    $family = $constituents_in_house->first()->full_name;
                }
            } else {

                //For some reason, no last names

                $family = 'Current Residents';
            }

            $new_household = ($constituents_in_house->first()->person) ? new Person : new Voter;

            $new_household->full_name = $family;
            $new_household->full_address = $household;
            $new_household->mailing_info = $constituents_in_house->first()->mailing_info;
            $new_household->address_number = $constituents_in_house->first()->address_number;
            $new_household->address_fraction = $constituents_in_house->first()->address_fraction;
            $new_household->address_number = $constituents_in_house->first()->address_number;
            $new_household->address_street = $constituents_in_house->first()->address_street;
            $new_household->address_apt = $constituents_in_house->first()->address_apt;
            $new_household->address_city = $constituents_in_house->first()->address_city;
            $new_household->address_state = $constituents_in_house->first()->address_state;
            $new_household->address_zip = $constituents_in_house->first()->address_zip;

            $constituents->push($new_household);
        }

        $count = $constituents->count();

        //dd($pdf);
        $pdfContent = PDF::loadView('shared-features.exports.labels-5160-pdf', 
                             compact('constituents'))->output();

        return response()->streamDownload(function() use ($pdfContent) {
                print($pdfContent);

            }, "Avery-5160-Labels-$count-Constituents-".date('Y-m-d').".pdf");
    }

    public function updated()
    {
        $this->search_value = trim(
                                trim($this->first_name.' '.$this->middle_name)
                                .' '.$this->last_name
                               );
        $this->untouched = false;
    }

    public function render()
    {
        $people = $this->constituentQuery($input = getPublicObjectVars($this));

        return view('livewire.constituents.query-form', [
                'people' => $people,
                'total_count' => $this->total_count,
                'total_count_people' =>  $this->total_count_people,
                'total_count_voters' => $this->total_count_voters
        ]);

    }

}
