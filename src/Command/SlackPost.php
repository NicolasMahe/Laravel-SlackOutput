<?php

namespace NicolasMahe\SlackOutput\Command;

use Illuminate\Console\Command;
use Maknz\Slack\Facades\Slack;

class SlackPost extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slack:post
	  {message? : The message to send}
	  {to? : The channel or person}
	  {attach? : The attachment}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send a message to Slack with queuing';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
    $this->line('Processing...');

    $message = $this->argument('message');
    $to = $this->argument('to');
    $attach = $this->argument('attach');

    $slack = Slack::createMessage();

    if (is_array($attach)) {
      $slack->attach($attach);
    }

    if (!empty($to)) {
      $slack->to($to);
    }

    $slack->send($message);

		$this->info('Message send');
	}

}
