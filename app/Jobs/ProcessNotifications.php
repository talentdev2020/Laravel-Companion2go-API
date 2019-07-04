<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Components\NotificationSender;
use App\Classes\{
    NotificationSenderEmail, NotificationSenderSms, NotificationSenderPush
};

/**
 * Class ProcessNotifications
 * How to use: `php artisan queue:work --queue=emails|push|sms --tries=5`
 * @package App\Jobs
 */
class ProcessNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $payload = [];

    /**
     * ProcessNotifications constructor.
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }


    /**
     * Execute the job.
     * @return void
     * @throws \ReflectionException
     */
    public function handle()
    {
        /** @var NotificationSenderEmail|NotificationSenderSms|NotificationSenderPush $sender */
        $sender = NotificationSender::getInstance($this->payload['type']);

        /** Send notification */
        $sender
            ->setAddressee($this->payload['addressee'])
            ->setTitle($this->payload['title'])
            ->setMessage($this->payload['message'])
            ->send();

        echo 'Notification sent. Payload: ' . json_encode($this->payload) . PHP_EOL;
    }
}
