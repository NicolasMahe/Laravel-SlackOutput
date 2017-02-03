<?php

namespace NicolasMahe\SlackOutput\Command;

use Illuminate\Console\Command;
use Maknz\Slack\Client;
use Exception;

class SlackPost extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'slack:post
	  {message? : The message to send}
	  {to? : The channel or person}
	  {attach? : The attachment payload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a message to Slack';

    /**
     * The slack client
     *
     * @var Client
     */
    protected $client;


    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $this->line('Processing...');

        //get command arguments
        $message = $this->argument('message');
        $to      = $this->argument('to');
        $attach  = $this->argument('attach');

        //init client
        $this->initClient();

        //attach data
        $this->addAttachment($attach);

        //add to
        $this->addTo($to);

        //send
        $this->send($message);

        $this->info('Message sent');
    }


    /**
     * Init the slack client
     *
     * @return Client
     * @throws Exception
     */
    protected function initClient()
    {
        //get slack config
        $slack_config = config('slack-output');

        if (empty( $slack_config["endpoint"] )) {
            throw new Exception("The endpoint url is not set in the config");
        }

        //init client
        $this->client = new Client($slack_config["endpoint"], [
            "username" => $slack_config["username"],
            "icon"     => $slack_config["icon"]
        ]);
    }


    /**
     * Add the attachments
     *
     * @param $attach
     */
    protected function addAttachment($attach)
    {
        if (is_array($attach)) {
            if ($this->is_assoc($attach)) {
                $attach = [ $attach ];
            }

            foreach ($attach as $attachElement) {
                $this->client = $this->client->attach($attachElement);
            }
        }
    }


    /**
     * Add the receiver
     *
     * @param $to
     */
    protected function addTo($to)
    {
        if ( ! empty( $to )) {
            $this->client = $this->client->to($to);
        }
    }


    /**
     * Send the message
     *
     * @param $message
     */
    protected function send($message)
    {
        $this->client->send($message);
        $this->client = null;
    }


    /**
     * Check if array is associative
     *
     * @param array $array
     *
     * @return bool
     */
    public static function is_assoc(array $array)
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }
}
