<?php

namespace App\Console\Commands;

use App\Entrata\Leases;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EntrataGetMistLeases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entrata:getMitsLeases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves lease or application information.';

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
     * @return mixed
     */
    public function handle()
    {
        $leases = Leases::getMitsLeases(null, null);
        $bar = $this->output->createProgressBar(count($leases));
        $bar->start();
        \DB::table('users')->update(['is_enabled' => false]);
        foreach ($leases as $lease) {
            $bar->advance();
            $start_date = null;
            $end_date = null;
            $lease_id = $lease['Identification']['IDValue'];
            foreach ($lease['LeaseEvents']['LeaseEvent'] as $leaseEvent) {
                if ($leaseEvent['@attributes']['EventType'] == 'LeaseFrom') {
                    $start_date = $leaseEvent['@attributes']['Date'];
                } elseif ($leaseEvent['@attributes']['EventType'] == 'ActualMoveIn') {
                    $start_date = $leaseEvent['@attributes']['Date'];
                } elseif ($leaseEvent['@attributes']['EventType'] == 'LeaseTo') {
                    $end_date = $leaseEvent['@attributes']['Date'];
                } elseif ($leaseEvent['@attributes']['EventType'] == 'ActualMoveOut') {
                    $end_date = $leaseEvent['@attributes']['Date'];
                }
            }
            if ($lease['Status'][0]['ApprovalStatus'] == 'Current' || $lease['Status'][0]['ApprovalStatus'] == 'Notice') {
                $is_enabled = true;
            } else {
                $is_enabled = false;
            }

            \DB::table('users')
                ->where('lease_id', '=', $lease_id)
                ->update(['is_enabled' => $is_enabled, 'start_date' => $start_date, 'end_date' => $end_date]);
        }
        $bar->finish();

        // Activate Users
        $active_users = User::where('is_enabled', '=', true)
            ->count();

        // De-Activate Usersz
        $inactive_users = User::where('is_enabled', '=', false)
            ->count();

        echo PHP_EOL;
        echo User::all()->count() . " Total Users as of " . Carbon::today()->toDateString() . PHP_EOL;
        echo "$active_users Users active as of " . Carbon::today()->toDateString() . PHP_EOL;
        echo "$inactive_users Users inactive " . Carbon::today()->toDateString() . PHP_EOL;
    }
}
