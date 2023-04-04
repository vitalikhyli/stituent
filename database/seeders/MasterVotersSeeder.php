<?php

namespace Database\Seeders;

use App\Models\CC\CCVoter;
use App\VoterMaster;
use Illuminate\Database\Seeder;

class MasterVotersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //// WTF? Takes 10 minutes to run count() function??????

        /*
        echo "Counting Master Voters...\n";
        $num_voters = VoterMaster::count();
        echo "Count: $num_voters\n";
        if ($num_voters > 1500) {
        	echo "Already Seeded $num_voters Master Voters.\n";
        	return;
        }
        */

        /*
        VoterMaster::truncate();
        for ($i=1; $i<220; $i++) {

            if (CCVoter::where('house_district', $i)->count() > 0) {
                echo "Seeding House District $i\n";
                $voters = CCVoter::where('house_district', $i)
                                 ->take(10)
                                 ->get();

                foreach ($voters as $voter) {
                    $voter->convertAddOrUpdate();
                }
            }
        }
        */
    }
}
