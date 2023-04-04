<?php

namespace Database\Seeders;

use App\Account;
use App\Models\Admin\DataFolder;
use App\Models\CC\CCCampaign;
use App\Models\CC\CCUser;
use App\Permission;
use App\Team;
use App\TeamUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AccountsAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $folder = new DataFolder;
        $folder->id = 'MA';
        $folder->name = 'Massachusetts';
        $folder->save();

        $folder = new DataFolder;
        $folder->id = 'CT';
        $folder->name = 'Connecticut';
        $folder->save();

        $campaigns = CCCampaign::all();

        foreach ($campaigns as $campaign) {

            //dd($campaign);
            $account = Account::where('old_cc_id', $campaign->campaignID)->first();

            if (! $account) {
                $account = new Account;
                $account->old_cc_id = $campaign->campaignID;
                $account->billygoat_id = $this->getBillyIdByCampaignID($campaign->campaignID);
                $account->name = $campaign->campaign_name;
                $account->contact_name = $campaign->first_name.' '.$campaign->last_name;
                $account->address = $campaign->address_1.' '.$campaign->address_2;
                $account->city = $campaign->city;
                $account->state = $campaign->state;
                $account->zip = $campaign->postal_code;
                $account->save();
            }

            $office_team = Team::where('account_id', $account->id)
                               ->where('app_type', 'office')
                               ->first();

            if (! $office_team) {
                $office_team = new Team;
                $office_team->account_id = $account->id;
                $office_team->data_folder_id = 'MA';
                $office_team->admin = ($campaign->campaignID == 1) ? true : false;
                $office_team->name = $campaign->campaign_name;
                $office_team->short_name = $campaign->campaign_name;
                $office_team->old_cc_id = $campaign->campaignID;

                $office_team->district_type = $campaign->campaign_type;
                $office_team->district_id = $campaign->campaign_district;

                $office_team->app_type = 'office';

                $office_team->save();
            }
            if ($campaign->campaignID == 409) {
                $office_team->app_type = 'u';
                $office_team->pilot = true;
                $office_team->save();
                continue;
            }
            if ($account->billygoat_id > 0) {
                $campaign_team = Team::where('account_id', $account->id)
                                     ->where('app_type', 'campaign')
                                     ->first();

                if (! $campaign_team) {
                    $campaign_team = new Team;
                    $campaign_team->account_id = $account->id;
                    $campaign_team->data_folder_id = 'MA';
                    $campaign_team->admin = ($campaign->campaignID == 1) ? true : false;
                    $campaign_team->name = $campaign->campaign_name.' Campaign';
                    $campaign_team->short_name = $campaign->campaign_name;
                    $campaign_team->old_cc_id = $campaign->campaignID;

                    $campaign_team->district_type = $campaign->campaign_type;
                    $campaign_team->district_id = $campaign->campaign_district;

                    $campaign_team->app_type = 'campaign';

                    $campaign_team->save();
                }
            }
        }

        $cc_users = CCUser::all();

        foreach ($cc_users as $cc_user) {
            $user = User::where('username', $cc_user->login)->first();
            $office_team = Team::where('old_cc_id', $cc_user->campaignID)
                               ->where('app_type', 'office')
                               ->first();
            if (! $office_team) {
                echo $cc_user->first_name.' '.$cc_user->last_name."\n";
                continue;
            }

            if (! $user) {
                $user = new User;
                $user->username = $cc_user->login;
                if ($cc_user->email) {
                    $user->email = $cc_user->email;
                    if (User::whereEmail($cc_user->email)->exists()) {
                        $user->email = 'DUPE.'.$cc_user->userID.'.'.$user->email;
                    }
                } else {
                    $user->email = 'placeholder.'.$cc_user->userID.'@communityfluency.com';
                }
                $user->current_team_id = $office_team->id;
                $user->name = $cc_user->first_name.' '.$cc_user->last_name;
                $user->created_at = $cc_user->create_date;
                $user->password = bcrypt($cc_user->passwd);

                if (Carbon::parse($cc_user->last_login) > '2000-01-01') {
                    $user->last_login = $cc_user->last_login;
                }
                //dd($user);
                $user->save();
            }
            $teamuser = TeamUser::where('team_id', $office_team->id)
                                    ->where('user_id', $user->id)
                                    ->first();

            if (! $teamuser) {
                $teamuser = new TeamUser;
                $teamuser->team_id = $office_team->id;
                $teamuser->user_id = $user->id;
                $teamuser->save();
            }

            $permission = Permission::where('team_id', $office_team->id)
                                     ->where('user_id', $user->id)
                                     ->first();

            if (! $permission) {
                $permission = new Permission;
                $permission->team_id = $office_team->id;
                $permission->user_id = $user->id;
                if ($cc_user->admin) {
                    $permission->admin = true;
                }
                $permission->save();
            }
        }
    }

    public function getBillyIdByCampaignID($campaign_id)
    {
        $billy_ids = [
            4   => 34,    	// H. Smola
            5   => 201,    	// Sh. Evangelidis
            6   => 15,    	// S. Gobi
            7   => 29,    	// H. Muradian
            8   => 21,		// H. Kafka
            19  => 20,		// H. Jones
            24  => 19,		// S. Humason
            28  => 33,		// H. Poirier
            87  => 26,		// Sh. McDonald
            147 => 22,		// H. Kane
            178 => 35,		// S. Tarr
            187 => 10,		// H. D'Emilia
            193 => 36, 		// M. Vigeant
            203 => 17,		// H. Harrington
            227 => 32,		// H. Orrall
            266 => 12,		// S. Dizoglio
            289 => 28,		// H. Mirra
            357 => 16,		// H. Golden
            391 => 14,		// H. Galvin
            392 => 9,		// S. Creem
            396 => 6,		// H. Arcerio
            397 => 25,		// H. Mark
            404 => 24,		// H. Madaro
            409 => 31,		// NEU
            412 => 30,		// H. Murray
            414 => 27,		// H. Meschino
            421 => 11,		// H. Decker
            430 => 18,		// S. Hinds
            433 => 8,		// S. Comerford
            434 => 23,		// H. Kearney
            435 => 7,		// H. Blais
            437 => 38,		// H. Domb
        ];
        if (isset($billy_ids[$campaign_id])) {
            return $billy_ids[$campaign_id];
        }

        return null;
    }
}

/*

1	All Campaigns
    3	State Sen Stephen M. Brewer
4	Todd Smola
5	Lew Evangelidis
6	Anne Gobi
7	David K. Muradian, Jr.
8	Louis Kafka
    15	Jourdain for Council
19	Bradley Jones
    21	Ryan Fattman
    22	Richard Ross
    23	Mary Rogeness
24	Donald Humason
28	Betty Poirier
    33	Karyn Polito
    34	schneiderpr
    36	Jay Barrows
    43	Senator Richard Tisei
    45	Mike Knapik
    61	Jennifer Flanagan
    76	Colleen Garry
    85	Linda Dorcena Forry
87	Sheriff Joe McDonald
    92	Michael Rush
    135	Lucas for Rep
    137	Kim Ferguson
    138	Kevin Kuros
    140	Karyn Polito - Lt Gov
147	Hannah Kane
    152	GOPMuniPAC
    153	Mary Z. Connaughton
    177	John Mahoney
178	Bruce Tarr
    179	Nicholas Boldyga
    180	Donald Wong
    181	John F. Keenan
    182	Geoffrey G. Diehl
    184	James J. Lyons Jr
    186	Denise Garlick
187	Angelo D'Emilia
    188	Paul A Schmid
    189	Christopher M. Markey
    192	Tackey Chan
193	Arthur Vigeant
    194	Steven L. Levy
    199	James T. Welch
    201	Sheriff Lew
203	Shelia C. Harrington
    204	Jerald A. Parisella
    205	John Fresolo
    226	Shaunna O'Connell
227	Keiko Orrall
    236	Donna Colorio
    246	Worcester County PAC
    256	Stephen Coulter
    263	Steven S. Howitt
    265	Jon Zlotnik
266	Diana DiZoglio
    287	Shaun Toohey
289	Lenny Mirra
    293	John W. Scibak
    295	Marie Angelides
    299	John B
    306	Paul Frost
    307	Nantucket
    311	Paul Heroux
    316	Kenneth Gordon
    319	Jeffrey Roy
    320	Josh Cutler
    322	Mary Keefe
    323	Kathleen O'Connor Ives
    325	Mike Barrett
    326	James Timilty
    327	Millbury
    328	Linda Campbell
    329	Craig Welton
    331	Stephen Lynch
    333	Wayland
    334	Sue S
    338	John Fairbanks
    339	Peter Kush
    340	Scituate
    341	Barre Mass
    342	Jeff Ross
    344	Bill Linehan
    345	Boston
    348	Karen Spilka
    349	Susan Chalifoux Zephir
    350	Jennifer Kannan
    352	Linda D Forry
    354	Woburn
    356	Pat Jehlen
357	Thomas Golden
    359	Karen Spiewak
    363	Harold Naughton
    364	M walsh
    369	Shawn Dooley
    370	Michael Costello
    376	RoseLee Vincent
    379	2nd Brist
    381	Michael S. Day
    384	O'Sullivan
    386	Holden
    388	John Q Adams
391	William C. Galvin
392	Cynthia S. Creem
    393	Sal Lamattina
396	James Arciero
397	Paul Mark
    398	Walter Timilty
    399	test
    401	Jack Brennan
    402	Mark Carron
404	Adrian Madaro
    405	Patrick M. O'Connor
    406	Michael Segal
    407	Brian Ashe
    408	Ben Franklin
409	NEU
    410	for freetown
    411	Ted Philips
412	Brian Murray
    413	David Nangle
414	Joan Meschino
    415	Sheila Hubbard
    417	Newton
    418	Britte McBride
    419	Edward Philips
    420	Al Shaughnessy
421	Marjorie Decker
    422	Brian Arrigo
    423	Thomas P Walsh
    424	Harriette L Chandler
    425	Nancy McGovern
    426	Dean Tran
    427	Dennis McManus
    428	D DiZoglio
    429	Paul R. Feeney
430	Adam G Hinds
    431	Judy O'Connell
    432	Maria Newman
433	Jo Comerford
434	Patrick Kearney
435	Natalie M. Blais
    436	Daniel M Donahue
437	Mindy Domb
    438	Michelle Ciccolo
439	Don F Humason
440	Smitty Pignatelli

6	Rep Arcerio, James	James.Arciero@mahouse.gov
7	Rep Blais, Natalie	Natalie.Blais@mahouse.gov
8	Sen Comerford, Jo	samantha.e.hopper@gmail.com
9	Sen Creem, Cynthia	Cynthia.Creem@masenate.gov
10	Rep D'Emilia, Angelo	Angelo.D'Emilia@mahouse.gov
11	Rep Decker, Marjorie	Marjorie.Decker@mahouse.gov
12	Sen DiZoglio, Diana	Diana.DiZoglio@masenate.gov
13	Sheriff Evangelidis, Lew	lazarusm@gmail.com
14	Rep Galvin, William	William.Galvin@mahouse.gov
15	Sen Gobi, Anne	anne.gobi@masenate.gov
16	Rep Golden, Tom	Thomas.Golden@mahouse.gov
17	Rep Harrington, Sheila	Sheila.Harrington@mahouse.gov
18	Sen Hinds, Adam	adam.hinds@masenate.gov
19	Sen Humason, Don	Donald.Humason@masenate.gov
20	Rep Jones, Brad	Bradley.Jones@mahouse.gov
21	Rep Kafka, Louis	Louis.Kafka@mahouse.gov
22	Rep Kane, Hannah	Hannah.Kane@mahouse.gov
23	Rep Kearney, Patrick	patrick.kearney@mahouse.gov
24	Rep Madaro, Adrian	adrian.madaro@gmail.com
25	Rep Mark, Paul	Paul.Mark@mahouse.gov
26	Sheriff McDonald, Joe	info@sheriffjoemcdonald.com
27	Rep Meschino, Joan	Joan.Meschino@mahouse.gov
28	Rep Mirra, Lenny	Leonard.Mirra@mahouse.gov
29	Rep Muradian, David	David.Muradian@mahouse.gov
30	Rep Murray, Brian	Brian.Murray@mahouse.gov
31	Northeastern University	D.Isberg@northeastern.edu
32	Rep Orrall, Norman	norman.orrall@mahouse.gov
33	Rep Poirier, Elizabeth	Elizabeth.Poirier@mahouse.gov
34	Rep Smola, Todd	Todd.Smola@mahouse.gov
35	Sen Tarr, Bruce	Bruce.Tarr@masenate.gov
36	Mayor Vigeant, Arthur	lazarusm@gmail.com
38	Rep Domb, Mindy	MindyForMa@gmail.com

*/
