<?php

namespace Database\Seeders;

use App\Account;
use App\Person;
use App\Team;
use App\User;
use Database\Seeders\AccountsAndUsersSeeder;
use Database\Seeders\CasesSeeder;
use Database\Seeders\ContactsSeeder;
use Database\Seeders\Faker\PresetSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $start = $this->seederStart();

        $this->call(MasterVotersSeeder::class);
        $this->call(GeographySeeder::class);
        $this->call(AccountsAndUsersSeeder::class);
        $this->call(VoterSlicesSeeder::class);

        if (env('LOCAL_MACHINE') != 'Slothe') {
            $this->call(PeopleSeeder::class);
            $this->call(GroupsSeeder::class);
            $this->call(ContactsSeeder::class);
            $this->call(CasesSeeder::class);
            // For local testing
        }

        if (env('LOCAL_MACHINE') == 'Slothe') {
            $this->anonymizeStuff();
            $this->call(PresetSeeder::class);           // Create login teams+users locally
            Artisan::call('cf:populate_slices');
        }

        $this->call(NortheasternUniversitySeeder::class);

        $this->seederFinish($start);
    }

    public function anonymizeStuff()
    {
        $model = Account::all();
        $fields = ['name', 'email', 'contact_name'];
        $this->cycleThroughAnonymize($model, $fields);

        $model = Team::all();
        $fields = ['name', 'short_name'];
        $this->cycleThroughAnonymize($model, $fields);

        $model = User::all();
        $fields = ['name', 'username', 'email'];
        $this->cycleThroughAnonymize($model, $fields);

        $model = Person::all();
        $fields = ['full_name', 'full_name_middle', 'primary_email', 'first_name', 'last_name'];
        $this->cycleThroughAnonymize($model, $fields);
    }

    public function cycleThroughAnonymize($model, $fields)
    {
        $display_field = $fields[0];

        $not_vowels = ['q', 'w', 'r', 't', 'p', 's', 'd', 'f', 'g', 'h', 'j', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm'];

        foreach ($model as $key => $row) {
            foreach ($fields as $thefield) {

                //$row->$thefield = Str::random(strlen($row->$thefield));

                $row->$thefield = ucfirst(str_ireplace($not_vowels, $not_vowels[array_rand($not_vowels)], $row->$thefield));
            }

            $row->save();

            echo 'Anonymizing: '.$key.' '.$display_field.' '.$row->$display_field."\r\n";
        }
    }

    public function seederStart()
    {
        return \Carbon\Carbon::now();
    }

    public function seederFinish($start)
    {
        $end = \Carbon\Carbon::now();
        $duration = $end->diffInSeconds($start);
        $duration = round($duration / 60, 2);
        echo str_repeat('-', 76)."\r\n";
        $grand_total = 'Seeding took a grand total of '.$duration.' minutes.';
        $spacer = (76 - strlen($grand_total)) / 2;
        echo str_repeat(' ', $spacer).$grand_total."\r\n";
        echo str_repeat('-', 76)."\r\n";
    }

    public function ProgressBar($counter, $limit, $noun, $options)
    {
        $steps = 40;
        $char_len = 40;
        $output = '';
        $mod = $limit / $steps;
        $char_step = round($char_len / $steps, 0);

        if ($options == 'static') {
            $output = '  '.str_repeat('*', $char_len).'| ';
            $output = $output.' '.$limit.' '.$noun;
            $output = $output."\r\n";
        } else {
            $counter++;
            $output = '  '.str_repeat('*', round($counter / $mod, 0) * $char_step)
            .str_repeat('_', $char_len - (round($counter / $mod, 0) * $char_step))
            .'| ';
            $output = $output.' '.$counter.' '.$noun;
            if ($counter >= $limit) {
                $counter = 0;
                $output = $output."\r\n";
            } else {
                $output = $output."\r";
            }
        }
        echo $output;

        return $counter;
    }
}
