<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CFPlus extends Model
{
    protected $connection = 'voters';
    protected $casts = ['full_data' => 'array'];

    public function getTable()
    {
        return 'CF_PLUS_FULL';
    }

    public function getPhonesAttribute()
    {
    	if ($this->phone_list) {
    		return $this->phone_list;
    	}
    	if ($this->VoterTelephones_LandlineFormatted) {
    		return $this->VoterTelephones_LandlineFormatted;
    	}
    	if ($this->VoterTelephones_CellPhoneFormatted) {
    		return $this->VoterTelephones_CellPhoneFormatted.' (Cell)';
    	}
    }
    public function getFormattedDataAttribute()
    {
        $field_map = $this->getFieldMap();

        $formatted_data = [];
        foreach ($field_map as $category => $fields) {
            foreach ($fields as $fieldname => $fieldlabel) {
                if (isset($this->full_data[$fieldname])) {
                    $formatted_data[$category][$fieldlabel] = $this->full_data[$fieldname];
                }
            }
        }
        //dd($formatted_data);
        return $formatted_data;
    }

    public static function getFieldMap()
    {
        $field_map = [
            "Personal" => [
                "Voters_FirstName"                              => "First Name",
                "Voters_LastName"                               => "Last Name",
                "EthnicGroups_EthnicGroup1Desc"                 => "Ethnic Group",
                "Ethnic_Description"                            => "Ethnicity",
                "MaritalStatus_Description"                     => "Marital Status",
                "CommercialData_Education"                      => "Education",
                "Voters_BirthDate"                              => "Birthdate",
                "Voters_Gender"                                 => "Gender",
                "VoterTelephones_FullPhone"                     => "Primary Phone",
                "VoterTelephones_TelCellFlag"                   => "Primary Is Cell",
                "VoterTelephones_TelConfidenceCode"             => "Phone Confidence",
                "View_Ops_CellPhone_Phone10"                    => "Cell Phone",
            ],
            "Household" => [
                "Residence_Families_HHCount"                    => "Household Count",
                "Residence_HHGender_Description "               => "Household Gender",
                "Residence_HHParties_Description"               => "Household Party",
                "Unified_School_District"                       => "School District",
                "Residence_Addresses_Property_HomeSq_Footage"   => "Home Sq. Footage",
                "Residence_Addresses_Property_LandSq_Footage"   => "Land Sq. Footage",
                "CommercialData_PcntHHWithChildren"             => "Odds Household Children",
                "CommercialData_PcntHHMarriedCoupleWithChild"   => "Odds Household Married w/ Child",
                "CommercialData_PcntHHMarriedCoupleNoChild"     => "Odds Household Married w/ No Child",
                "CommercialDataLL_PetOwner_Dog"                 => "Pet - Dog",
                "CommercialDataLL_PetOwner_Other"               => "Pet - Other",
                "CommercialData_PcntHHSpanishSpeaking"          => "Odds Spanish Speaking",
                "CommercialData_MedianEducationYears"           => "Median Education Years",
                "AddressDistricts_Change_Changed_County"        => "Changed County",
                "AddressDistricts_Change_Changed_CD "           => "Changed Congressional",
                "AddressDistricts_Change_Changed_HD"            => "Changed House District",
                "AddressDistricts_Change_Changed_SD"            => "Changed Senate District",
            ],
            "Voting" => [
                "Voters_VotingPerformanceEvenYearGeneral"       => "Voting: Even Year General",
                "Voters_VotingPerformanceEvenYearPrimary"       => "Voting: Even Year Primary",
                "Voters_VotingPerformanceEvenYearGeneralAndPrimary" => "Voting: Even Year General & Primary",
                "Voters_VotingPerformanceMinorElection"         => "Voting: Minor Election",
            ],
            "Financial" => [
                "CommercialData_EstimatedIncome"                => "Estimated Income",
                "CommercialData_PropertyType"                   => "Property Type",
                "CommercialData_EstHomeValue"                   => "Estimated Home Value",
                "CommercialData_EstimatedMedianIncome"          => "Estimated Median Income",
                "CommercialData_MosaicZ4Global"                 => "Mosaic® Verticals ",
                // CommercialData_SocialPos    2700",
                "CommercialData_MedianHousingValue"             => "Median Housing Value", 
            ],        
        ];
        return $field_map;
    }
}

/*
{"SEQUENCE":"6535",
"LALVOTERID":"LALMA161873795",
"Voters_StateVoterID":"06BEH2041000",
"VoterTelephones_Phone10":"9786975191",
"VoterTelephones_FullPhone":"(978) 697-5191",
"VoterTelephones_TelAreaCode":"978",
"VoterTelephones_TelCellFlag":"True",
"VoterTelephones_TelConfidenceCode":"1",
"View_Ops_CellPhone_Phone10":"9786975191",
"View_Ops_CellPhone_TelConfidenceCode":"1",
"Voters_FirstName":"Elizabeth",
"Voters_MiddleName":"A",
"Voters_LastName":"Bowlan",
"Household_MailingAddressID":"1788608",
"Mailing_Addresses_Zip":"01854",
"Mailing_Addresses_ZipPlus4":"1009",
"Mailing_Addresses_ZipFull":"01854-1009",
"Mailing_Addresses_StreetName":"Varnum",
"Mailing_Addresses_Designator":"Ave",
"Mailing_Addresses_HouseNumber":"1619",
"Mailing_Addresses_EvenOddFlag":"O",
"Mailing_Addresses_AddressLine":"1619 Varnum Ave",
"Mailing_Addresses_City":"Lowell",
"Mailing_Addresses_State":"MA",
"Mailing_Addresses_CensusTract":"310601",
"Mailing_Addresses_CensusBlockGroup":"2",
"Mailing_Addresses_CensusBlock":"2015",
"Mailing_Addresses_Latitude":"42.654950",
"Mailing_Addresses_Longitude":"-71.381100",
"Mailing_Addresses_Zip9":"018541009",
"Residence_Addresses_Zip":"01854",
"Residence_Addresses_ZipPlus4":"1009",
"Residence_Addresses_ZipFull":"01854-1009",
"Residence_Addresses_StreetName":"Varnum",
"Residence_Addresses_Designator":"Ave",
"Residence_Addresses_HouseNumber":"1619",
"Residence_Addresses_EvenOddFlag":"O",
"Residence_Addresses_AddressLine":"1619 Varnum Ave",
"Residence_Addresses_City":"Lowell",
"Residence_Addresses_State":"MA",
"Residence_Addresses_CensusTract":"310601",
"Residence_Addresses_CensusBlockGroup":"2",
"Residence_Addresses_CensusBlock":"2015",
"Residence_Addresses_Latitude":"42.654950",
"Residence_Addresses_Longitude":"-71.381100",
"Residence_Addresses_Zip9":"018541009",
"Residence_Addresses_Property_HomeSq_Footage":"01600",
"Residence_Addresses_Property_LandSq_Footage":"0010000",
"AddressDistricts_Change_Changed_County":"Has Not Changed Within Last 2 Years",
"AddressDistricts_Change_Changed_CD":"Has Not Changed Within Last 2 Years",
"AddressDistricts_Change_Changed_HD":"Has Not Changed Within Last 2 Years",
"AddressDistricts_Change_Changed_SD":"Has Not Changed Within Last 2 Years",
"Ethnic_LALEthnicCode":"78",
"EthnicGroups_EthnicGroup1":"100",
"EthnicGroups_EthnicGroup1Desc":"European",
"Ethnic_Description":"English\/Welsh",
"Languages_Description":"English",
"Mailing_Families_FamilyID":"M001788608",
"Mailing_Families_HHCount":"1",
"Mailing_HHGender_Description":"Female Only Household",
"Mailing_HHParties_Description":"Democratic",
"Parties_Description":"Democratic",
"Religions_Description":"Catholic",
"Residence_Families_FamilyID":"R001788608",
"Residence_Families_HHCount":"1",
"Residence_HHGender_Description":"Female Only Household",
"Residence_HHParties_Description":"Democratic",
"Voters_CalculatedRegDate":"09\/23\/1994",
"Voters_OfficialRegDate":"09\/23\/1994",
"Voters_Age":"80",
"Voters_BirthDate":"06\/20\/1941",
"Voters_Gender":"F",
"Voters_FIPS":"017",
"Voters_Active":"A",
"Voters_SequenceZigZag":"362104162",
"Voters_SequenceOddEven":"362104162",
"Voters_VotingPerformanceEvenYearGeneral":"57%",
"Voters_VotingPerformanceEvenYearPrimary":"0%",
"Voters_VotingPerformanceEvenYearGeneralAndPrimary":"28%",
"Voters_VotingPerformanceMinorElection":"0%",
"2001_US_Congressional_District":"05",
"US_Congressional_District":"3",
"2001_State_House_District":"MIDDLESEX 16",
"2001_State_Senate_District":"MIDDLESEX 1",
"City":"LOWELL CITY",
"City_Ward":"LOWELL CITY WARD 06",
"State_House_District":"MIDDLESEX 16",
"State_Senate_District":"MIDDLESEX 1",
"County":"MIDDLESEX",
"Precinct":"LOWELL 06-3",
"Designated_Market_Area_(DMA)":"BOSTON DMA (EST.)",
"Unified_School_District":"LOWELL UNIFIED SD (EST.)",
"CommercialData_PresenceOfChildrenCode":"Not Likely to have a child",
"CommercialData_ISPSA":"6",
"CommercialData_DwellingType":"Single Family Dwelling Unit",
"CommercialData_DwellingUnitSize":"1-Single Family Dwelling",
"CommercialData_EstimatedIncome":"$35000-49999",
"CommercialData_Education":"Bach Degree - Extremely Likely",
"CommercialData_OccupationGroup":"Retired",
"CommercialData_Occupation":"Unknown",
"CommercialData_UpscaleFemaleBuyerInHome":"2",
"CommercialData_FamilyMagazineInHome":"1",
"CommercialData_GardeningMagazineInHome":"2",
"CommercialData_CulinaryInterestMagazineInHome":"1",
"CommercialData_PoliticalContributerInHome":"1",
"CommercialData_DonatesEnvironmentCauseInHome":"U",
"CommercialData_DonatesToCharityInHome":"Y",
"CommercialData_ComputerOwnerInHome":"Y",
"CommercialData_LandValue":"$117000",
"CommercialData_PropertyType":"Residential",
"CommercialData_EstHomeValue":"$427965",
"CommercialData_MosaicZ4":"Aging in Place",
"CommercialData_EstimatedMedianIncome":"$126939",
"CommercialData_HHComposition":"2 or more adult Females",
"CommercialData_MosaicZ4Global":"Comfortable Retirement",
"CommercialData_SocialPos":"3855",
"CommercialData_StateIncomeDecile":"6",
"CommercialData_PcntHHWithChildren":"36%",
"CommercialData_PcntHHMarriedCoupleWithChild":"30%",
"CommercialData_PcntHHMarriedCoupleNoChild":"38%",
"CommercialData_MedianHousingValue":"$454364",
"CommercialData_PcntHHSpanishSpeaking":"3%",
"CommercialData_MedianEducationYears":"14",
"CommercialDataLL_Home_Owner_Or_Renter":"Likely Homeowner",
"CommercialDataLL_Net_Worth":"$50000-99999",
"CommercialDataLL_PetOwner_Dog":"Yes",
"CommercialDataLL_PetOwner_Other":"Yes",
"CommercialDataLL_Reading_General_In_Household":"Yes",
"CommercialDataLL_Reading_Magazines_In_Household":"Yes",
"CommercialDataLL_Interest_in_Auto_Work_In_Household":"Yes",
"CommercialDataLL_Interest_in_Gardening_In_Household":"Yes",
"CommercialDataLL_Interest_in_Cooking_General_In_Household":"Yes",
"CommercialDataLL_Interest_in_Automotive_Parts_Accessories_In_Household":"Yes",
"General_2016-11-08":"Y",
"General_2012-11-06":"Y",
"General_2010-11-02":"Y"}
*/
