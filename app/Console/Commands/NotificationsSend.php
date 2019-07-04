<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Interfaces\INotificationTypes;
use App\Jobs\ProcessNotifications;

/**
 * Class PermissionAssignRole
 * @package App\Console\Commands
 */
class NotificationsSend extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'notifications:send';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ProcessNotifications::dispatch([
            'type' => INotificationTypes::EMAIL,
            'addressee' => [
                'email' => 'ego.cogitare@gmail.com',
                'name' => 'Alexander Bogish',
            ],
            'title' => 'Notification title',
            'message' => [
                'template' => 'emails.index',
                'data' => [
                    'firstname' => 'Custom html firstname'
                ]
            ],
        ])
        ->onQueue('emails');
    }
}