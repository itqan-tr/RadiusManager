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
    protected $description = 'Retrieves list of customers for a property.';

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
            if ($customer['LeaseId']['CustomerType'][0] == 'Primary') {
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
                        $user->apartment_id = $apartment_id;
                        $user->save();

                        //Update RadReplies after user is updated.
                        if (!$user->radreplies()->where('attribute', 'Tunnel-Type')->first()) {
                            $user->radreplies()->create([
                                'attribute' => 'Tunnel-Type',
                                'value' => '13'
                            ]);
                        }

                        if (!$user->radreplies()->where('attribute', 'Tunnel-Medium-Type')->first()) {
                            $user->radreplies()->create([
                                'attribute' => 'Tunnel-Medium-Type',
                                'value' => '6'
                            ]);
                        }

                        if (!$user->radreplies()->where('attribute', 'Tunnel-Private-Group-Id')->first()) {
                            $user->radreplies()->create([
                                'attribute' => 'Tunnel-Private-Group-Id',
                                'value' => $user->apartment->vlan_id
                            ]);
                        } else {
                            $user->radreplies()->where('attribute', 'Tunnel-Private-Group-Id')->first()->update([
                                'value' => $user->apartment->vlan_id
                            ]);
                        }
                    } else {
                        $user = new User();
                        $user->apartment_id = $apartment_id;
                        $user->name = $customer['FirstName'] . ' ' . $customer['LastName'];
                        $user->username = $customer['Email'];
                        $user->password = str_random(6);
                        $user->is_enabled = false;
                        $user->default_password = $user->password;
                        $user->email = $customer['Email'];
                        $user->lease_id = $lease_id;
                        $user->save();

                        //Add RadReplies after user is created.
                        $user->radreplies()->create([
                            'attribute' => 'Tunnel-Type',
                            'value' => '13'
                        ]);
                        $user->radreplies()->create([
                            'attribute' => 'Tunnel-Medium-Type',
                            'value' => '6'
                        ]);

                        $user->radreplies()->create([
                            'attribute' => 'Tunnel-Private-Group-Id',
                            'value' => $user->apartment->vlan_id
                        ]);
                    }

                    $bar->advance();
                } else {
                    $error_customer[] = $customer;
                }
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
