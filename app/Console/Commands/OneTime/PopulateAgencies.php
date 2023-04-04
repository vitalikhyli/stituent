<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;

class PopulateAgencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:agencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->tab = "    ";

        $this->agencies = $this->getAgenciesArray();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->addNextAgency($key = 0, $current_level = 0, $parent = null);

    }

    public function addNextAgency($key, $level, $parent = null)
    {

        $current_level = $this->agencies[$key]['tabs'];

        if ($current_level > $level) {

            $parent = 'id';
            
            $this->addNextAgency($key, $current_level, $parent = null);
        }

        if (!isset($this->agencies[$key + 1])) return;

        if ($this->agencies[$key + 1]['tabs'] == 0) {
            
        }

    }

    // public function nestRows($level, $parent)
    // {

    //     foreach($rows as $key => $row) {

    //         $row_name = trim(str_replace('[TAB]', '', $row));
    //         $row[$row_name] = [];

    //         if (substr_count($row, '[TAB]') == 0) {

    //             $row = $row_name;

    //         } else {

    //             $row[$row_name] = $this->nestRow();
    //         }
    //             // ,
    //             //             'name' => 
    //             //           ];

    //     }

    //     return $rows;

    // }

    //     foreach($rows as $row) {

    //         $level = substr_count($row, '[TAB]');

    //         if ($level == 0) {

    //         }
    //     }

    //     print_r($rows);


    // }


    public function getAgenciesArray()
    {

        $text = $this->textList();
        $rows = explode("\n", $text);

        $agencies = [];

        foreach($rows as $key => $row) {

            if (trim($row) != '') {

                $agencies[] = [
                                'name' => trim($row),
                                'level' => substr_count(str_replace($this->tab, '[TAB]', $row), '[TAB]')
                              ];

            }

        }

        return $agencies;

    }



    public function textList()
    {
        return "
Constitutional Officers 
    Office of the Governor – GOV 
    Secretary of the Commonwealth – SoC 
    Treasurer and Receiver – T&R 
    Auditor – AUD 
    Attorney General – AGO

Executive Branch 
    Executive Office for Administration and Finance – A&F 
        Dept. of Revenue – DoR 
        Board of Library Commissioners – BLC 
        Operational Services – OSD 
        Group Insurance Commission – GIC 
        Bureau of the State House – BSH 
        Division of Capital Asset Management – DCAMM 
        Office on Disability – OOD 
        Public Employee Retirement Administration – PERA 
        Human Resources Division – HRD 

    Executive Office of Health and Human Services - EOHHS 
        Office of Medicaid – HM 
        Dept. of Mental Health – DMH 
        Dept. of Public Health – DPH 
        Dept. of Children and Families – DCF 
        Dept. of Transitional Assistance – DTA 
        Dept. of Youth Services – DYS 
        Office for Refugees and Immigrants – ORI 
        Dept. of Developmental Services – DDS 
        Mass. Commission for the Blind – MCB 
        Mass. Commission for the Deaf and Hard of Hearing – MCD 
        Mass. Rehabilitation Commission – MRC 
        Soldier’s Home (Holyoke) – HSH 
        Soldier’s Home (Chelsea) – CSH 
        Executive Office of Elder Affairs – EA 
        Center for Health Information & Analysis – CHIA 
        Mass. Health Connector – CCA 
        Health Policy Commission – HPC 

    Executive Office of Housing and Economic Development – EOHED 
        Office of Consumer Affairs and Business Regulations (includes all divisions) – OCABR 
            Include division/department in description  

        Mass. Office of Business Development – OBD 
        Mass. Office of International Trade and Investment – MOITI 
        Mass. Office of Travel and Tourism – MOTT 
        Mass. Permit Regulatory Office – MPRO 
        Department of Housing and Community Development – DHCD 
        Mass. Regulatory Ombudsman – MRO 

        ";
    }
}
