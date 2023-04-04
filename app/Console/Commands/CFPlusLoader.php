<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Console\Command;
use App\CFPlus;

class CFPlusLoader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:cf_plus {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takes a csv file from L2 and adds it to the CF PLUS database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = storage_path('app/cf_plus');
        if (!file_exists($path)) {
            mkdir($path);
        }
        if ($this->option('file')) {
            $import_name = $this->option('file');

            $already_imported = CFPlus::where('import', $import_name)
                                      ->pluck('SEQUENCE')
                                      ->flip();
            $file_path = $path."/".$import_name;
            if (file_exists($file_path)) {
                $csv = fopen($file_path, 'r');

                $colnames = null;
                $sequence = 0;
                while ($row = fgetcsv($csv)) {

                    if (!$colnames) {
                        $colnames = $row;
                        continue;
                    }
                    $sequence++;

                    $record = [];
                    foreach ($colnames as $index => $colname) {
                        if ($row[$index]) {
                            $record[$colname] = $row[$index];
                        }
                    }

                    if (!isset($record['SEQUENCE'])) {
                      $record['SEQUENCE'] = $sequence;
                    }
                    if (isset($already_imported[$record['SEQUENCE']])) {
                        continue;
                    }

                    echo $record['SEQUENCE']."\r";

                    $cfp = new CFPLus;
                    $cfp->import = $import_name;
                    $cfp->SEQUENCE = $record['SEQUENCE'];
                    $cfp->LALVOTERID = $record['LALVOTERID'];
                    $cfp->Voters_StateVoterID = $record['Voters_StateVoterID'];
                    $cfp->voter_id = 'MA_'.$record['Voters_StateVoterID'];
                    $cfp->full_data = $record;
                    $cfp->save();
                    //dd($record);
                }
            } else {
                dd($file_path, "Not Found");
            }
        }
        
        $indexed_columns = [
            'cell_phone'         => ['View_Ops_CellPhone_Phone10', 
                                     'VoterTelephones_CellPhoneUnformatted'],
            'home_phone'         => ['VoterTelephones_Phone10',
                                     'VoterTelephones_LandlineUnformatted'],
            'ethnic_description' => ['Ethnic_Description'],
            'estimated_income'   => ['CommercialData_EstimatedIncome', 
                                     'CommercialData_EstimatedHHIncome'], 
        ];

        $current_columns = Schema::connection('voters')->getColumnListing('CF_PLUS_FULL');

        // Make sure columns are there
        foreach ($indexed_columns as $indexed_column => $dud) {
            if (!in_array($indexed_column, $current_columns)) {
                Schema::connection('voters')->table('CF_PLUS_FULL', function (Blueprint $table) use ($indexed_column) {
                    echo "Adding $indexed_column\n";
                    $table->string($indexed_column)->after('full_data')->index()->nullable();
                });
            }
        }

        // Fill data for columns
        $cf_plusses = CFPlus::query();
        if ($this->option('file')) {
            $cf_plusses->where('import', $this->option('file'));
        }

        $total = $cf_plusses->count();
        $current = 0;
        $cf_plusses->chunkById(1000, function ($cfp_collection) use ($indexed_columns, $total, &$current) {

            foreach ($cfp_collection as $cfp) {
                foreach ($indexed_columns as $indexed_column => $full_data_fields) {
                    foreach ($full_data_fields as $full_data_field) {
                      $current++;

                      //echo "Checking $full_data_field for $indexed_column \n";
                      //dd($cfp->full_data);
                      $full_data = $cfp->full_data;
                      if (isset($full_data[$full_data_field])) {
                          $cfp->$indexed_column = $full_data[$full_data_field];
                          $cfp->save();
                          echo "\t".$total." - $current: ".$cfp->voter_id." $indexed_column \r";
                      }
                    }
                } 
            }

        });
        
    }
}

/*

  "SEQUENCE" => "1"
  "LALVOTERID" => "LALMA162505663"
  "Voters_StateVoterID" => "10DJY1088000"
  "Voters_CountyVoterID" => ""
  "VoterTelephones_Phone10" => "9784273212"
  "VoterTelephones_FullPhone" => "(978) 427-3212"
  "VoterTelephones_TelAreaCode" => "978"
  "VoterTelephones_TelCellFlag" => "True"
  "VoterTelephones_TelConfidenceCode" => "1"
  "View_Ops_CellPhone_Phone10" => "9784273212"
  "View_Ops_CellPhone_TelConfidenceCode" => "1"
  "Voters_NamePrefix" => ""
  "Voters_FirstName" => "Johanny"
  "Voters_MiddleName" => ""
  "Voters_LastName" => "Dejesus"
  "Voters_NameSuffix" => ""
  "Household_MailingAddressID" => "11643865"
  "Mailing_Addresses_Zip" => "01850"
  "Mailing_Addresses_ZipPlus4" => ""
  "Mailing_Addresses_ZipFull" => "01850"
  "Mailing_Addresses_StreetName" => "6th"
  "Mailing_Addresses_Designator" => "St"
  "Mailing_Addresses_PrefixDirection" => ""
  "Mailing_Addresses_SuffixDirection" => ""
  "Mailing_Addresses_HouseNumber" => "156B"
  "Mailing_Addresses_HalfIndicator" => ""
  "Mailing_Addresses_HalfSuffix" => ""
  "Mailing_Addresses_EvenOddFlag" => "E"
  "Mailing_Addresses_ApartmentType" => ""
  "Mailing_Addresses_ApartmentNum" => ""
  "Mailing_Addresses_AddressLine" => "156B 6th St"
  "Mailing_Addresses_ExtraAddressLine" => ""
  "Mailing_Addresses_City" => "Lowell"
  "Mailing_Addresses_State" => "MA"
  "Mailing_Addresses_CensusTract" => "310200"
  "Mailing_Addresses_CensusBlockGroup" => "3"
  "Mailing_Addresses_CensusBlock" => "3017"
  "Mailing_Addresses_Latitude" => "42.656320"
  "Mailing_Addresses_Longitude" => "-71.304726"
  "Mailing_Addresses_Country" => ""
  "Mailing_Addresses_Zip9" => "01850"
  "Mailing_Addresses_NCOA_CancelDate" => ""
  "Residence_Addresses_Zip" => "01850"
  "Residence_Addresses_ZipPlus4" => ""
  "Residence_Addresses_ZipFull" => "01850"
  "Residence_Addresses_StreetName" => "6th"
  "Residence_Addresses_Designator" => "St"
  "Residence_Addresses_PrefixDirection" => ""
  "Residence_Addresses_SuffixDirection" => ""
  "Residence_Addresses_HouseNumber" => "156B"
  "Residence_Addresses_HalfIndicator" => ""
  "Residence_Addresses_HalfSuffix" => ""
  "Residence_Addresses_EvenOddFlag" => "E"
  "Residence_Addresses_ApartmentType" => ""
  "Residence_Addresses_ApartmentNum" => ""
  "Residence_Addresses_AddressLine" => "156B 6th St"
  "Residence_Addresses_ExtraAddressLine" => ""
  "Residence_Addresses_City" => "Lowell"
  "Residence_Addresses_State" => "MA"
  "Residence_Addresses_CensusTract" => "310200"
  "Residence_Addresses_CensusBlockGroup" => "3"
  "Residence_Addresses_CensusBlock" => "3017"
  "Residence_Addresses_Latitude" => "42.656320"
  "Residence_Addresses_Longitude" => "-71.304726"
  "Residence_Addresses_Country" => ""
  "Residence_Addresses_Zip9" => "01850"
  "Residence_Addresses_Property_HomeSq_Footage" => ""
  "Residence_Addresses_Property_LandSq_Footage" => ""
  "AbsenteeTypes_Description" => ""
  "AddressDistricts_Change_Changed_County" => "Has Not Changed Within Last 2 Years"
  "AddressDistricts_Change_Changed_CD" => "Has Not Changed Within Last 2 Years"
  "AddressDistricts_Change_Changed_HD" => "Between 6 Months and 1 Year Ago"
  "AddressDistricts_Change_Changed_LD" => ""
  "AddressDistricts_Change_Changed_SD" => "Has Not Changed Within Last 2 Years"
  "CountyEthnic_LALEthnicCode" => ""
  "CountyEthnic_Description" => ""
  "Ethnic_LALEthnicCode" => "64"
  "EthnicGroups_EthnicGroup1" => "200"
  "EthnicGroups_EthnicGroup1Desc" => "Hispanic and Portuguese"
  "EthnicGroups_EthnicGroup2" => ""
  "EthnicGroups_EthnicGroup2Desc" => ""
  "Ethnic_Description" => "Hispanic"
  "Languages_Description" => ""
  "Mailing_Families_FamilyID" => "M011643865"
  "Mailing_Families_HHCount" => "1"
  "Mailing_HHGender_Description" => "Male Only Household"
  "Mailing_HHParties_Description" => "Independent"
  "MaritalStatus_Description" => ""
  "MilitaryStatus_Description" => ""
  "Parties_Description" => "Non-Partisan"
  "Religions_Description" => ""
  "Residence_Families_FamilyID" => "R011643865"
  "Residence_Families_HHCount" => "1"
  "Residence_HHGender_Description" => "Male Only Household"
  "Residence_HHParties_Description" => "Independent"
  "VoterParties_Change_Changed_Party" => ""
  "Voters_CalculatedRegDate" => "01/22/2007"
  "Voters_OfficialRegDate" => "01/22/2007"
  "Voters_Age" => "33"
  "Voters_BirthDate" => "10/10/1988"
  "Voters_PlaceOfBirth" => ""
  "Voters_Gender" => "M"
  "Voters_FIPS" => "017"
  "Voters_Active" => "I"
  "Voters_SequenceZigZag" => "361665335"
  "Voters_SequenceOddEven" => "361665335"
  "Voters_VotingPerformanceEvenYearGeneral" => "0%"
  "Voters_VotingPerformanceEvenYearPrimary" => "0%"
  "Voters_VotingPerformanceEvenYearGeneralAndPrimary" => "0%"
  "Voters_VotingPerformanceMinorElection" => "0%"
  "2001_US_Congressional_District" => "05"
  "2011_Redistricting_Congressional_District" => ""
  "Old_US_Congressional_District" => ""
  "US_Congressional_District" => "3"
  "2001_State_House_District" => "MIDDLESEX 16"
  "2001_State_Legislative_District" => ""
  "2001_State_Senate_District" => "MIDDLESEX 1"
  "2003_Old_NYC_Council_District" => ""
  "2011_Redistricting_Legislative_District" => ""
  "2011_Redistricting_State_House_District" => ""
  "2011_Redistricting_State_Senate_District" => ""
  "City" => "LOWELL CITY"
  "City_Council_Commissioner_District" => ""
  "City_Mayoral_District" => ""
  "City_Ward" => "LOWELL CITY WARD 09"
  "Judicial_Juvenile_Court_District" => ""
  "Judicial_Supreme_Court_District" => ""
  "Old_State_House_District" => ""
  "Old_State_Senate_District" => ""
  "State_House_District" => "MIDDLESEX 16"
  "State_Legislative_District" => ""
  "State_Senate_District" => "MIDDLESEX 1"
  "Borough" => ""
  "Borough_Ward" => ""
  "County" => "MIDDLESEX"
  "County_Board_of_Education_District" => ""
  "County_Commissioner_District" => ""
  "County_Community_College_District" => ""
  "County_Fire_District" => ""
  "County_Hospital_District" => ""
  "County_Legislative_District" => ""
  "County_Library_District" => ""
  "County_Memorial_District" => ""
  "County_Paramedic_District" => ""
  "County_Service_Area" => ""
  "County_Service_Area_SubDistrict" => ""
  "County_Sewer_District" => ""
  "County_Superintendent_of_Schools_District" => ""
  "County_Supervisorial_District" => ""
  "County_Unified_School_District" => ""
  "County_Water_District" => ""
  "County_Water_Landowner_District" => ""
  "County_Water_SubDistrict" => ""
  "Judicial_County_Board_of_Review_District" => ""
  "Judicial_County_Court_District" => ""
  "Justice_of_the_Peace" => ""
  "Precinct" => ""
  "Town_Council" => ""
  "Town_District" => ""
  "Town_Ward" => ""
  "Township" => ""
  "Township_Ward" => ""
  "Village" => ""
  "Village_Ward" => ""
  "4-H_Livestock_District" => ""
  "Airport_District" => ""
  "Ambulance_District" => ""
  "Annexation_District" => ""
  "Aquatic_Center_District" => ""
  "Aquatic_District" => ""
  "Assessment_District" => ""
  "Bay_Area_Rapid_Transit" => ""
  "Board_of_Education_District" => ""
  "Board_of_Education_SubDistrict" => ""
  "Bonds_District" => ""
  "Career_Center" => ""
  "Cemetery_District" => ""
  "Central_Committee_District" => ""
  "Chemical_Control_District" => ""
  "City_School_District" => ""
  "City_School_SubDistrict" => ""
  "City_Sub_Dist_-_Old" => ""
  "Coast_Water_District" => ""
  "College_Board_District" => ""
  "Committee_Super_District" => ""
  "Communications_District" => ""
  "Community_College" => ""
  "Community_College_Commissioner_District" => ""
  "Community_College_SubDistrict" => ""
  "Community_College-At_Large" => ""
  "Community_Council_District" => ""
  "Community_Council_SubDistrict" => ""
  "Community_Facilities_District" => ""
  "Community_Facilities_SubDistrict" => ""
  "Community_Hospital_District" => ""
  "Community_Planning_Area" => ""
  "Community_Service_District" => ""
  "Community_Service_SubDistrict" => ""
  "Congressional_Township" => ""
  "Conservation_District" => ""
  "Conservation_SubDistrict" => ""
  "Consolidated_Water_District" => ""
  "Control_Zone_District" => ""
  "Corrections_District" => ""
  "County_Board_of_Education_SubDistrict" => ""
  "County_Sub_Dist_-_Old" => ""
  "Democratic_Convention_Member" => ""
  "Democratic_Zone" => ""
  "Designated_Market_Area_(DMA)" => "BOSTON DMA (EST.)"
  "District_Attorney" => ""
  "Drainage_District" => ""
  "Education_Commission_District" => ""
  "Educational_Service_District" => ""
  "Educational_Service_Subdistrict" => ""
  "Election_Commissioner_District" => ""
  "Elementary_School_District" => ""
  "Elementary_School_SubDistrict" => ""
  "Emergency_Communication_(911)_District" => ""
  "Emergency_Communication_(911)_SubDistrict" => ""
  "Enterprise_Zone_District" => ""
  "Exempted_Village_School_District" => ""
  "EXT_District" => ""
  "Facilities_Improvement_District" => ""
  "Fire_District" => ""
  "Fire_Maintenance_District" => ""
  "Fire_Protection_District" => ""
  "Fire_Protection_SubDistrict" => ""
  "Fire_Protection_Tax_Measure_District" => ""
  "Fire_Service_Area_District" => ""
  "Fire_SubDistrict" => ""
  "Flood_Control_Zone" => ""
  "Forest_Preserve" => ""
  "Garbage_District" => ""
  "Geological_Hazard_Abatement_District" => ""
  "Hamlet_Community_Area" => ""
  "Hamlet_Community_Area_Council" => ""
  "Health_District" => ""
  "High_School_District" => ""
  "High_School_SubDistrict" => ""
  "Hospital_District" => ""
  "Hospital_SubDistrict" => ""
  "Improvement_Landowner_District" => ""
  "Independent_Fire_District" => ""
  "Irrigation_District" => ""
  "Irrigation_SubDistrict" => ""
  "Island" => ""
  "Judicial_Appellate_District" => ""
  "Judicial_Appellate_SubDistrict" => ""
  "Judicial_Chancery_Court" => ""
  "Judicial_Circuit_Court_District" => ""
  "Judicial_District" => ""
  "Judicial_District_Court_District" => ""
  "Judicial_Family_Court_District" => ""
  "Judicial_Jury_District" => ""
  "Judicial_Magistrate_Division" => ""
  "Judicial_Sub-Circuit_District" => ""
  "Judicial_Superior_Court_District" => ""
  "Land_Commission" => ""
  "Landscaping_&_Lighting_Assessment_Distric" => ""
  "Law_Enforcement_District" => ""
  "Learning_Community_Coordinating_Council_District" => ""
  "Levee_District" => ""
  "Levee_Reconstruction_Assesment_District" => ""
  "Library_District" => ""
  "Library_Services_District" => ""
  "Library_SubDistrict" => ""
  "Lighting_District" => ""
  "Local_Hospital_District" => ""
  "Local_Park_District" => ""
  "Maintenance_District" => ""
  "Master_Plan_District" => ""
  "Memorial_District" => ""
  "Metro_Service_District" => ""
  "Metro_Service_Subdistrict" => ""
  "Metro_Transit_District" => ""
  "Metropolitan_Water_District" => ""
  "Middle_School_District" => ""
  "Mosquito_Abatement_District" => ""
  "Mountain_Water_District" => ""
  "Multi-township_Assessor" => ""
  "Municipal_Advisory_Council_District" => ""
  "Municipal_Court_District" => ""
  "Municipal_Utility_District" => ""
  "Municipal_Utility_SubDistrict" => ""
  "Municipal_Water_District" => ""
  "Municipal_Water_SubDistrict" => ""
  "Museum_District" => ""
  "Northeast_Soil_and_Water_District" => ""
  "Open_Space_District" => ""
  "Open_Space_SubDistrict" => ""
  "Other" => ""
  "Paramedic_District" => ""
  "Park_Commissioner_District" => ""
  "Park_District" => ""
  "Park_SubDistrict" => ""
  "Planning_Area_District" => ""
  "Police_District" => ""
  "Port_District" => ""
  "Port_SubDistrict" => ""
  "Power_District" => ""
  "Proposed_City" => ""
  "Proposed_City_Commissioner_District" => ""
  "Proposed_Community_College" => ""
  "Proposed_District" => ""
  "Proposed_Elementary_School_District" => ""
  "Proposed_Fire_District" => ""
  "Proposed_Unified_School_District" => ""
  "Public_Airport_District" => ""
  "Public_Regulation_Commission" => ""
  "Public_Service_Commission_District" => ""
  "Public_Utility_District" => ""
  "Public_Utility_SubDistrict" => ""
  "Rapid_Transit_District" => ""
  "Rapid_Transit_SubDistrict" => ""
  "Reclamation_District" => ""
  "Recreation_District" => ""
  "Recreational_SubDistrict" => ""
  "Regional_Office_of_Education_District" => ""
  "Republican_Area" => ""
  "Republican_Convention_Member" => ""
  "Resort_Improvement_District" => ""
  "Resource_Conservation_District" => ""
  "River_Water_District" => ""
  "Road_Maintenance_District" => ""
  "Rural_Service_District" => ""
  "Sanitary_District" => ""
  "Sanitary_SubDistrict" => ""
  "School_Board_District" => ""
  "School_District" => ""
  "School_District-Vocational" => ""
  "School_Facilities_Improvement_District" => ""
  "School_Subdistrict" => ""
  "Service_Area_District" => ""
  "Sewer_District" => ""
  "Sewer_Maintenance_District" => ""
  "Sewer_SubDistrict" => ""
  "Snow_Removal_District" => ""
  "Soil_&_Water_District" => ""
  "Soil_&_Water_District-At_Large" => ""
  "Special_Reporting_District" => ""
  "Special_Tax_District" => ""
  "State_Board_of_Equalization" => ""
  "Storm_Water_District" => ""
  "Street_Lighting_District" => ""
  "Superintendent_of_Schools_District" => ""
  "Transit_District" => ""
  "Transit_SubDistrict" => ""
  "TriCity_Service_District" => ""
  "TV_Translator_District" => ""
  "Unified_School_District" => "LOWELL UNIFIED SD (EST.)"
  "Unified_School_SubDistrict" => ""
  "Unincorporated_District" => ""
  "Unincorporated_Park_District" => ""
  "Unprotected_Fire_District" => ""
  "Ute_Creek_Soil_District" => ""
  "Vector_Control_District" => ""
  "Vote_By_Mail_Area" => ""
  "Wastewater_District" => ""
  "Water_Agency" => ""
  "Water_Agency_SubDistrict" => ""
  "Water_Conservation_District" => ""
  "Water_Conservation_SubDistrict" => ""
  "Water_Control__Water_Conservation" => ""
  "Water_Control__Water_Conservation_SubDistrict" => ""
  "Water_District" => ""
  "Water_Public_Utility_District" => ""
  "Water_Public_Utility_Subdistrict" => ""
  "Water_Replacement_District" => ""
  "Water_Replacement_SubDistrict" => ""
  "Water_SubDistrict" => ""
  "Weed_District" => ""
  "CommercialData_PresenceOfChildrenCode" => "Known Data"
  "CommercialData_ISPSA" => ""
  "CommercialData_DwellingType" => "Multi-Family Dwelling"
  "CommercialData_DwellingUnitSize" => "3-Triplex"
  "CommercialData_EstimatedIncome" => "$75000-99999"
  "CommercialData_Education" => "HS Diploma - Likely"
  "CommercialData_OccupationGroup" => "Other"
  "CommercialData_Occupation" => "Unknown"
  "CommercialData_UpscaleBuyerInHome" => ""
  "CommercialData_UpscaleMaleBuyerInHome" => ""
  "CommercialData_UpscaleFemaleBuyerInHome" => ""
  "CommercialData_BookBuyerInHome" => ""
  "CommercialData_FamilyMagazineInHome" => ""
  "CommercialData_FemaleOrientedMagazineInHome" => ""
  "CommercialData_ReligiousMagazineInHome" => ""
  "CommercialData_GardeningMagazineInHome" => ""
  "CommercialData_CulinaryInterestMagazineInHome" => ""
  "CommercialData_HealthFitnessMagazineInHome" => ""
  "CommercialData_DoItYourselferMagazineInHome" => ""
  "CommercialData_FinancialMagazineInHome" => ""
  "CommercialData_ReligiousContributorInHome" => ""
  "CommercialData_PoliticalContributerInHome" => ""
  "CommercialData_DonatesEnvironmentCauseInHome" => "U"
  "CommercialData_DonatesToCharityInHome" => "Y"
  "CommercialData_PresenceOfPremCredCrdInHome" => ""
  "CommercialData_ComputerOwnerInHome" => "Y"
  "CommercialData_HomePurchasePrice" => ""
  "CommercialData_HomePurchaseDate" => ""
  "CommercialData_LandValue" => ""
  "CommercialData_PropertyType" => "Triplex"
  "CommercialData_EstHomeValue" => ""
  "CommercialData_MosaicZ4" => ""
  "CommercialData_EstimatedMedianIncome" => ""
  "CommercialData_HHComposition" => "1 adult Male + Children"
  "CommercialData_MosaicZ4Global" => ""
  "CommercialData_SocialPos" => ""
  "CommercialData_StateIncomeDecile" => ""
  "CommercialData_PcntHHWithChildren" => ""
  "CommercialData_PcntHHMarriedCoupleWithChild" => ""
  "CommercialData_PcntHHMarriedCoupleNoChild" => ""
  "CommercialData_MedianHousingValue" => ""
  "CommercialData_PcntHHSpanishSpeaking" => ""
  "CommercialData_MedianEducationYears" => ""
  "CommercialData_OccupationIndustry" => ""
  "CommercialData_LikelyUnion" => ""
  "CommercialDataLL_Gun_Owner" => ""
  "CommercialDataLL_Veteran" => ""
  "CommercialDataLL_Affordable_Care_Act" => ""
  "CommercialDataLL_Bachmann_Michelle" => ""
  "CommercialDataLL_Blackwell_Ken" => ""
  "CommercialDataLL_Bush_Iraq_Policy" => ""
  "CommercialDataLL_Bush_George" => ""
  "CommercialDataLL_Cain_Herman" => ""
  "CommercialDataLL_Church_Attendee" => ""
  "CommercialDataLL_Clinton_Hillary" => ""
  "CommercialDataLL_Election_of_Conservative_Judges" => ""
  "CommercialDataLL_Facebook_User_Frequent" => ""
  "CommercialDataLL_Gay_Marriage" => ""
  "CommercialDataLL_Gun_Control" => ""
  "CommercialDataLL_Huckabee_Mike" => ""
  "CommercialDataLL_Immigration_Loosen_Restrictions" => ""
  "CommercialDataLL_Lawsuit_Damages_Should_be_Limited" => ""
  "CommercialDataLL_McCain_John" => ""
  "CommercialDataLL_Obama_Barack" => ""
  "CommercialDataLL_Palin_Sarah" => ""
  "CommercialDataLL_Party_Identification" => ""
  "CommercialDataLL_Privatize_Social_Security" => ""
  "CommercialDataLL_Pro_Choice" => ""
  "CommercialDataLL_Pro_Life" => ""
  "CommercialDataLL_Romney_Mitt" => ""
  "CommercialDataLL_Santorum_Rick" => ""
  "CommercialDataLL_School_Choice" => ""
  "CommercialDataLL_Social_Views" => ""
  "CommercialDataLL_Taxes_Raise" => ""
  "CommercialDataLL_Twitter_User_Frequent" => ""
  "CommercialDataLL_Donates_to_Animal_Welfare" => ""
  "CommercialDataLL_Donates_to_Arts_and_Culture" => ""
  "CommercialDataLL_Donates_to_Childrens_Causes" => ""
  "CommercialDataLL_Donates_to_Healthcare" => ""
  "CommercialDataLL_Donates_to_International_Aid_Causes" => ""
  "CommercialDataLL_Donates_to_Veterans_Causes" => ""
  "CommercialDataLL_Home_Owner_Or_Renter" => ""
  "CommercialDataLL_Net_Worth" => ""
  "CommercialDataLL_Business_Owner" => ""
  "CommercialDataLL_Investor" => ""
  "CommercialDataLL_Donates_to_Wildlife_Preservation" => ""
  "CommercialDataLL_Donates_to_Conservative_Causes" => ""
  "CommercialDataLL_Donates_to_Liberal_Causes" => ""
  "CommercialDataLL_Donates_to_Local_Community" => ""
  "CommercialDataLL_PetOwner_Horse" => ""
  "CommercialDataLL_PetOwner_Cat" => ""
  "CommercialDataLL_PetOwner_Dog" => ""
  "CommercialDataLL_PetOwner_Other" => ""
  "CommercialDataLL_Home_Office" => ""
  "CommercialDataLL_Reading_General_In_Household" => ""
  "CommercialDataLL_Reading_Religious_In_Household" => ""
  "CommercialDataLL_Reading_Science_Fiction_In_Household" => ""
  "CommercialDataLL_Reading_Magazines_In_Household" => ""
  "CommercialDataLL_Reading_Audio_Books_In_Household" => ""
  "CommercialDataLL_Interest_in_History_Military_In_Household" => ""
  "CommercialDataLL_Interest_in_Current_Affairs_Politics_In_Household" => ""
  "CommercialDataLL_Interest_in_Religious_Inspirational_In_Household" => ""
  "CommercialDataLL_Interest_in_Science_Space_In_Household" => ""
  "CommercialDataLL_Interest_in_Education_Online_In_Household" => ""
  "CommercialDataLL_Interest_in_Electronic_Gaming_In_Household" => ""
  "CommercialDataLL_Cable_or_Dish_Television" => ""
  "CommercialDataLL_Buyer_Antiques_In_Household" => ""
  "CommercialDataLL_Buyer_Art_In_Household" => ""
  "CommercialDataLL_Interest_in_Theater_Performing_Arts_In_Household" => ""
  "CommercialDataLL_Interest_in_the_Arts_In_Household" => ""
  "CommercialDataLL_Interest_in_Musical_Instruments_In_Household" => ""
  "CommercialDataLL_Collector_General_In_Household" => ""
  "CommercialDataLL_Collector_Stamps_In_Household" => ""
  "CommercialDataLL_Collector_Coins_In_Household" => ""
  "CommercialDataLL_Collector_Arts_In_Household" => ""
  "CommercialDataLL_Collector_Antiques_In_Household" => ""
  "CommercialDataLL_Collector_Avid_In_Household" => ""
  "CommercialDataLL_Collector_Sports_In_Household" => ""
  "CommercialDataLL_Collector_Military_In_Household" => ""
  "CommercialDataLL_Interest_in_Home_Repair_In_Household" => ""
  "CommercialDataLL_Interest_in_Auto_Work_In_Household" => ""
  "CommercialDataLL_Interest_in_Sewing_Knitting_In_Household" => ""
  "CommercialDataLL_Interest_in_Woodworking_In_Household" => ""
  "CommercialDataLL_Interest_in_Photography_In_Household" => ""
  "CommercialDataLL_Interest_in_Aviation_In_Household" => ""
  "CommercialDataLL_Interest_in_House_Plants_In_Household" => ""
  "CommercialDataLL_Interest_in_Crafts_In_Household" => ""
  "CommercialDataLL_Interest_in_Gardening_In_Household" => ""
  "CommercialDataLL_Interest_in_Photography_Video_In_Household" => ""
  "CommercialDataLL_Interest_in_Smoking_In_Household" => ""
  "CommercialDataLL_Interest_in_Home_Furnishings_In_Household" => ""
  "CommercialDataLL_Interest_in_Home_Improvement_In_Household" => ""
  "CommercialDataLL_Interest_in_Food_Wines_In_Household" => ""
  "CommercialDataLL_Interest_in_Cooking_General_In_Household" => ""
  "CommercialDataLL_Interest_in_Cooking_Gourmet_In_Household" => ""
  "CommercialDataLL_Interest_in_Foods_Natural_In_Household" => ""
  "CommercialDataLL_Interest_in_BoardGames_Puzzles_In_Household" => ""
  "CommercialDataLL_Interest_in_Gaming_Casino_In_Household" => ""
  "CommercialDataLL_Interest_in_Sweepstakes_Contests_In_Household" => ""
  "CommercialDataLL_Interest_in_Travel_Domestic_In_Household" => ""
  "CommercialDataLL_Interest_in_Travel_International_In_Household" => ""
  "CommercialDataLL_Interest_in_Travel_Cruise_In_Household" => ""
  "CommercialDataLL_Interest_in_Exercise_Health_In_Household" => ""
  "CommercialDataLL_Interest_in_Exercise_Running_Jogging_In_Household" => ""
  "CommercialDataLL_Interest_in_Exercise_Walking_In_Household" => ""
  "CommercialDataLL_Interest_in_Exercise_Aerobic_In_Household" => ""
  "CommercialDataLL_Interest_in_SpectatorSports_Auto_Racing_In_Household" => ""
  "CommercialDataLL_Interest_in_SpectatorSports_on_TV_In_Household" => ""
  "CommercialDataLL_Interest_in_SpectatorSports_Football_In_Household" => ""
  "CommercialDataLL_Interest_in_SpectatorSports_Baseball_In_Household" => ""
  "CommercialDataLL_Interest_in_SpectatorSports_Basketball_In_Household" => ""
  "CommercialDataLL_Interest_in_SpectatorSports_Hockey_In_Household" => ""
  "CommercialDataLL_Interest_in_SpectatorSports_Soccer_In_Household" => ""
  "CommercialDataLL_Interest_in_Tennis_In_Household" => ""
  "CommercialDataLL_Interest_in_Golf_In_Household" => ""
  "CommercialDataLL_Interest_in_Snow_Skiing_In_Household" => ""
  "CommercialDataLL_Interest_in_Motorcycling_In_Household" => ""
  "CommercialDataLL_Interest_in_Nascar_In_Household" => ""
  "CommercialDataLL_Interest_in_Boating_Sailing_In_Household" => ""
  "CommercialDataLL_Interest_in_Scuba_Diving_In_Household" => ""
  "CommercialDataLL_Interest_in_Sports_Leisure_In_Household" => ""
  "CommercialDataLL_Interest_in_Hunting_In_Household" => ""
  "CommercialDataLL_Interest_in_Fishing_In_Household" => ""
  "CommercialDataLL_Interest_in_Camping_Hiking_In_Household" => ""
  "CommercialDataLL_Interest_in_Shooting_In_Household" => ""
  "CommercialDataLL_Interest_in_Automotive_Parts_Accessories_In_Household" => ""
  "CommercialDataLL_Hispanic_Country_Origin" => ""
  "CommercialDataLL_Household_Primary_Language" => ""
  "CommercialDataLL_Gun_Owner_Concealed_Permit" => ""
  "FECDonors_NumberOfDonations" => ""
  "FECDonors_TotalDonationsAmount" => ""
  "FECDonors_TotalDonationsAmt_Range" => ""
  "FECDonors_LastDonationDate" => ""
  "FECDonors_AvgDonation" => ""
  "FECDonors_AvgDonation_Range" => ""
  "FECDonors_PrimaryRecipientOfContributions" => ""
*/
