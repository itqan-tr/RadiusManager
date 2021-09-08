<?php

namespace App\Console\Commands;

use App\Mail\ForgotPassword;
use App\Mail\WelcomeEmail;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class SendWelcomeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:welcome {days=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send welcome email to Customers {days} before the start date.';

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
        if ($this->hasArgument('days')) {
            $days = (int)$this->argument('days');
        } else {
            $days = 0;
        }
        $users = User::whereDate('start_date', '=', Carbon::today()->addDays($days))
            ->where(function ($query) use ($days) {
                $query->where('last_emailed_at', '<', Carbon::now()->subHours(24))->orWhereNull('last_emailed_at');
            })
            ->get();

        foreach ($users as $user) {
            Mail::to($user)->send(new WelcomeEmail($user));
            echo 'Welcome Email sent to ' . $user->email . PHP_EOL;
            $user->last_emailed_at = Carbon::now();
            $user->save();
        }
    }
}
