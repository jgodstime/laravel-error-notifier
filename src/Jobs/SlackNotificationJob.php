<?php
namespace ErrorNotifier\Notify\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SlackNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $payload =  [

        $data = $this->data;
            // dd($data);
        $isAuthenticated = $data['is_authenticated'] ? "True" : "False";

        $payload  = [
            "attachments" => [
                [

                    "color" => "#c71616",
                    "blocks" => [
                        [
                            "type" => "context",
                            "elements" => [
                                [
                                    "type" => "mrkdwn",
                                    "text" => "@channel  :zap:"
                                ]
                            ]
                        ],

                        [
                            "type" => "section",
                            "text" => [
                                "type" => "plain_text",
                                "text" =>"Error Message: {$data['message']}"
                            ]
                        ],
                        [
                            "type" => "divider"
                        ],
                        [
                            "type" => "section",
                            "fields" => [
                                [
                                    "type" => "mrkdwn",
                                    "text" => "*File:* {$data['file']}"
                                ],
                                [
                                    "type" => "mrkdwn",
                                    "text" => "*Line:* {$data['line']}"
                                ]
                            ]
                        ],
                        [
                            "type" => "section",
                            "fields" => [
                                [
                                    "type" => "mrkdwn",
                                    "text" => "*Access Url:* {$data['access_url']}"
                                ],
                                [
                                    "type" => "mrkdwn",
                                    "text" => "*Is Authenticated:* {$isAuthenticated}"
                                ]

                            ]
                        ],
                        [
                            "type" => "section",
                            "fields" => [
                                [
                                    "type" => "mrkdwn",
                                    "text" => "*ID:* ".$data['id']
                                ],
                                [
                                    "type" => "mrkdwn",
                                    "text" => "*Email:* ".$data['email']
                                ]

                            ]
                        ],
                        [
                            "type" => "divider"
                        ],
                        [
                            "type" => "section",
                            "text" => [
                                "type" => "plain_text",
                                "text" => "Trace: {$data['trace']} "
                            ]
                        ],

                    ]
                ]
            ]
        ];

        $response = Http::post(config('notifier.channels.slack.url'), $payload);
        $response->json();

    }
}
