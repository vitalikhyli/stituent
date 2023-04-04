<?php

namespace App\Console\Commands\Admin\States\RI;

use App\Console\Commands\Admin\States\NationalMaster;

Use App\VoterMaster;
use App\District;
use App\Municipality;
use App\County;
    

class MasterRI extends NationalMaster
{
    protected $signature                = 'cf:ri_master';
    protected $description              = '';
    public $state                       = 'RI';
    public $districts_count             = ['F' => 2, 'S' => 38, 'H' => 75];
    public $municipal_count             = ['cities' => 39, 'counties' => 5];


    ////////////////////////////////////////////////////////////////////////////////
    //
    // REQUIRED FUNCTIONS:
    //
    
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->chooseAndRunCommands();
    }

    public function forEachRow($switch, $row, $row_num)
    {
        switch ($switch) {
            case 'voters':
                $this->importRow($row, $this->firstrow, $row_num);
                break;
        }
    }

    
    ////////////////////////////////////////////////////////////////////////////////
    //
    // RHODE ISLAND FUNCTIONS:
    //

    public function importRow($row, $firstrow, $row_num)
    {
        $csv = $this->englishColumnNames($row, $firstrow);
        
        $import = new VoterMaster;

        $import->import_order       = $row_num - 1;
        $import->state              = $this->state;

        //-------------------------These fields saved in orginal_import:
        // 17 => "CARRIER CODE"
        // 18 => "POSTAL CITY"
        // 31 => "SPECIAL STATUS CODE"
        // 33 => "DATE OF PRIVILEGE"
        // 35 => "DATE ACCEPTED"
        // 36 => "DATE OF STATUS CHANGE"
        // 38 => "OFF REASON CODE"         <-------- Left-trimmed
        // 39 => "DATE LAST ACTIVE"
        // 44 => "WARD/COUNCIL"                 <-- ?
        // 45 => "WARD DISTRICT"                <-- ?
        // 46 => "SCHOOL COMMITTEE DISTRICT"
        // 47 => "SPECIAL DISTRICT"
        // 48 => "FIRE DISTRICT"
        $import->original_import    = $csv;

        // 0 => "VOTER ID"
        // 1 => "STATUS CODE"

        $import->id                 = $this->state.'_'.$csv['VOTER ID'];
        $import->voter_status       = $csv['STATUS CODE']; //<-------- Had to lengthen VARCHAR to 2

        // 2 => "LAST NAME"
        // 3 => "FIRST NAME"
        // 4 => "MIDDLE NAME"
        // 5 => "PREFIX"
        // 6 => "SUFFIX"

        $import->last_name          = titleCase($csv['LAST NAME']);
        $import->first_name         = titleCase($csv['FIRST NAME']);
        $import->middle_name        = titleCase($csv['MIDDLE NAME']);
        $import->name_title         = titleCase($csv['PREFIX']);
        $import->suffix_name        = titleCase($csv['SUFFIX']);

        // 7 => "STREET NUMBER"
        // 8 => "STREET NAME"
        // 10 => "ZIP CODE"
        // 11 => "ZIP4 CODE"
        // 12 => "CITY"
        // 13 => "UNIT"
        // 16 => "STATE"
        // 9 => "STREET NAME 2"     <-- ? Correct placement ?
        // 14 => "SUFFIX A"         <-- ? Correct placement ?
        // 15 => "SUFFIX B"         <-- ? Correct placement ?

        $import->address_number     = $csv['STREET NUMBER'];
        $import->address_street     = titleCase(trim($csv['STREET NAME'].' '.$csv['STREET NAME 2']));  // <-- ?
        $import->address_fraction   = titleCase(trim($csv['SUFFIX A'].' '.$csv['SUFFIX B']));          // <-- ?
        $import->address_zip        = $csv['ZIP CODE'];
        $import->address_zip4       = $csv['ZIP4 CODE'];
        $import->address_city       = titleCase($csv['CITY']);
        $import->address_apt        = $csv['UNIT'];
        $import->address_state      = $csv['STATE'];

        // 19 => "MAILING STREET NUMBER"
        // 20 => "MAILING STREET NAME 1"
        // 21 => "MAILING STREET NAME 2"
        // 22 => "MAILING ZIP CODE"
        // 23 => "MAILING CITY"
        // 24 => "MAILING UNIT"
        // 25 => "MAILING SUFFIX A"
        // 26 => "MAILING SUFFIX B"
        // 27 => "MAILING STATE"
        // 28 => "MAILING COUNTRY"
        // 29 => "MAILING CARRIER CODE"

        // Format = {"address":"43 Marcelle St","address2":"","city":"Chicopee","state":"MA","zip":"01020","zip4":"1124"}

        $import->mailing_info       = ['address' => trim($csv['MAILING STREET NUMBER']
                                                    .' '.$csv['MAILING STREET NAME 1']
                                                    .' '.$csv['MAILING STREET NAME 2']),
                                       'address2' => trim($csv['UNIT']
                                                    .' '.$csv['MAILING SUFFIX A']
                                                    .' '.$csv['MAILING SUFFIX B']),
                                       'city' => trim($csv['MAILING CITY']),
                                       'state' => trim($csv['MAILING STATE']),
                                       'zip' => trim($csv['MAILING ZIP CODE']),
                                       'country' => trim($csv['MAILING COUNTRY']),
                                       'carrier' => trim($csv['MAILING CARRIER CODE'])
                                      ];

        // 30 => "PARTY CODE"
        // 32 => "DATE EFFECTIVE"
        // 34 => "SEX"
        $import->party              = $csv['PARTY CODE'];
        $import->registration_date  = $csv['DATE EFFECTIVE'];
        $import->gender             = $csv['SEX'];

        // 37 => "YEAR OF BIRTH"
        $import->yob                = $csv['YEAR OF BIRTH'];

        // 40 => "CONGRESSIONAL DISTRICT"
        // 41 => "STATE SENATE DISTRICT"
        // 42 => "STATE REP DISTRICT"

        $district = District::where('state', $this->state)
                            ->where('type', 'F')
                            ->where('code', $csv['CONGRESSIONAL DISTRICT'])
                            ->first();
        if ($district) $import->congress_district   = $district->code;

        $district = District::where('state', $this->state)
                            ->where('type', 'S')
                            ->where('code', $csv['STATE SENATE DISTRICT'])
                            ->first();
        if ($district) $import->senate_district     = $district->code;

        $district = District::where('state', $this->state)
                            ->where('type', 'H')
                            ->where('code', $csv['STATE REP DISTRICT'])
                            ->first();
        if ($district) $import->house_district      = $district->code;

        // 43 => "PRECINCT"
        $import->precinct           = $csv['PRECINCT'];

        $municipality = Municipality::where('state', $this->state)
                                    ->where('code', (substr($csv['PRECINCT'],0,2) * 1))
                                    ->first();
        if ($municipality) {
            $import->city_code           = $municipality->code;
            $import->county_code         = County::find($municipality->county_id)->code;
        }

        // 49 => "PHONE NUMBER"
        $import->home_phone         = $csv['PHONE NUMBER'];

        $import->save();
    }

}
