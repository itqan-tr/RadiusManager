<?php

namespace App\Console\Commands;

use App\Apartment;
use App\Entrata\Customers;
use App\User;
use Illuminate\Console\Command;

class EntrataGetCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entrata:getCustomers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Entrata Customers';

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
        $customers = Customers::getCustomers(0);
        $bar = $this->output->createProgressBar(count($customers));
        $error_customer = [];
        $bar->start();
        foreach ($customers as $customer) {
            if (isset($customer['Email'])) {
                $lease_id = $customer['LeaseId']['Identification'][0]['IDValue'];
                $apartment = Apartment::where('unit_number', '=', $customer['UnitNumber'])->first();
                if (!$apartment) {
                    $apartment = Apartment::where('name', '=', 'Apt' . $customer['UnitNumber'])->first();
                    if ($apartment) {
                        $apartment->unit_number = $customer['UnitNumber'];
                        $apartment->save();
                    }
                }
                if (!$apartment) {
                    $apartment = new Apartment();
                    $apartment->name = 'Apt' . $customer['UnitNumber'];
                    $apartment->vlan_id = $customer['UnitNumber'];
                    $apartment->unit_number = $customer['UnitNumber'];
                    $apartment->save();
                }
                $apartment_id = $apartment->id;
                if ($user = User::where('email', '=', $customer['Email'])->first()) {
                    $user->lease_id = $lease_id;
                    $user->save();
                } else {
                    $user = new User();
                    $user->apartment_id = $apartment_id;
                    $user->name = $customer['FirstName'] . ' ' . $customer['LastName'];
                    $user->username = $customer['Email'];
                    $user->password = str_random(5);
                    $user->is_enabled = false;
                    $user->default_password = $user->password;
                    $user->email = $customer['Email'];
                    $user->lease_id = $lease_id;
                    $user->save();
                }
                $bar->advance();
            } else {
                $error_customer[] = $customer;
            }
        }
        $bar->finish();
        if (count($error_customer)) {
            echo PHP_EOL . "Error Customers (" . count($error_customer) . ")" . PHP_EOL;
            foreach ($error_customer as $customer) {
                echo "Customer : ({$customer['@attributes']['Id']}) {$customer['FirstName']} {$customer['LastName']} " . PHP_EOL;
            }
        }
    }
}
