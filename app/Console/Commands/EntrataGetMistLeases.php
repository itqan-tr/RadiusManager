<?php

namespace App\Console\Commands;

use App\Apartment;
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
        $apartments = Apartment::whereNotNull('unit_number')->get();
        $bar = $this->output->createProgressBar(count($apartments));
        $bar->start();
        foreach ($apartments as $apartment) {
            $unit_number = $apartment->unit_number;
            $leases = Leases::getMitsLeases($unit_number);
            foreach ($leases as $lease) {
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
                \DB::table('users')
                    ->where('lease_id', '=', $lease_id)
                    ->update(['start_date' => $start_date, 'end_date' => $end_date]);
            }
            $bar->advance();
        }
        $bar->finish();

        // Activate Users
        $activated_users = \DB::table('users')
            ->where('start_date', '<=', Carbon::today()->toDateString())
            ->where('end_date', '>=', Carbon::today()->toDateString())
            ->update(['is_enabled' => '1', 'updated_at' => Carbon::now()]);

        // De-Activate Users
        $deactivated_users = \DB::table('users')
            ->where('start_date', '>', Carbon::today()->toDateString())
            ->orWhere('end_date', '<', Carbon::today()->toDateString())
            ->orWhereNull('start_date')
            ->orWhereNull('end_date')
            ->update(['is_enabled' => '0', 'updated_at' => Carbon::now()]);

        echo User::all()->count() . " Total Users as of " . Carbon::today()->toDateString() . PHP_EOL;
        echo "$activated_users Users Activated as of " . Carbon::today()->toDateString() . PHP_EOL;
        echo "$deactivated_users Users De-Activated " . Carbon::today()->toDateString() . PHP_EOL;
    }
}
