<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Person;

use Schema;


class MergePeople extends Component
{
	public $keep;
	public $remove;
	public $attributes;

	public function mount()
	{
		
		$this->ignore = [
						'household_id',
						'id',
						'team_id',
						'is_household',
						'mass_gis_id',
						'full_name',
						'full_name_middle'
						];
		$this->attributes = collect(Schema::getColumnListing('people'))->flip()->except($this->ignore)->flip();
	}

// array:63 [▼
//   0 => "id"
//   1 => "team_id"
//   2 => "voter_id"
//   3 => "is_household"
//   4 => "household_id"
//   5 => "mass_gis_id"
//   6 => "full_name"
//   7 => "full_name_middle"
//   8 => "full_address"
//   9 => "primary_phone"
//   10 => "work_phone"
//   11 => "other_phones"
//   12 => "primary_email"
//   13 => "work_email"
//   14 => "other_emails"
//   15 => "master_email_list"
//   16 => "massemail_neversend"
//   17 => "social_twitter"
//   18 => "social_facebook"
//   19 => "support"
//   20 => "private"
//   21 => "old_private"
//   22 => "name_title"
//   23 => "first_name"
//   24 => "middle_name"
//   25 => "last_name"
//   26 => "suffix_name"
//   27 => "address_number"
//   28 => "address_fraction"
//   29 => "address_street"
//   30 => "address_apt"
//   31 => "address_city"
//   32 => "address_state"
//   33 => "address_zip"
//   34 => "address_lat"
//   35 => "address_long"
//   36 => "mailing_info"
//   37 => "business_info"
//   38 => "gender"
//   39 => "party"
//   40 => "spouse_name"
//   41 => "dob"
//   42 => "yob"
//   43 => "deceased"
//   44 => "deceased_date"
//   45 => "governor_district"
//   46 => "congress_district"
//   47 => "senate_district"
//   48 => "house_district"
//   49 => "county_code"
//   50 => "ward"
//   51 => "precinct"
//   52 => "city_code"
//   53 => "old_cc_id"
//   54 => "old_voter_code"
//   55 => "upload_id"
//   56 => "created_by"
//   57 => "updated_by"
//   58 => "deleted_by"
//   59 => "data_history"
//   60 => "deleted_at"
//   61 => "created_at"
//   62 => "updated_at"

    public function render()
    {
    	$this->keep = Person::find(19);

        return view('livewire.merge.merge-people');
    }
}
