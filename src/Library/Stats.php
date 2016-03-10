<?php

namespace NicolasMahe\SlackOutput\Library;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class Stats
{

    /**
     * Output the stats to slack
     *
     * @param $channel
     */
    static function output($channel)
    {
        $object = new self();
        $object->calculateStats();
        $object->sendToSlack($channel);
    }


    /**
     * The stats array
     *
     * @var array
     */
    public $stats = [ ];


    /**
     * Get the classes and sanitized them
     *
     * @return array
     */
    protected function getClasses()
    {

        $classes = config('slack-output.stats.classes');

        //force classes to have the right format
        $sanitized_classes = [ ];
        foreach ($classes as $classes_name => $constraints) {
            //check constraints are supplied, if not, correct
            //the classes name with the right value
            if (is_int($classes_name)) {
                $classes_name = $constraints;
                $constraints  = [ ];
            }
            $sanitized_classes[$classes_name] = $constraints;
        }

        return $sanitized_classes;
    }


    /**
     * Get the dates
     *
     * @return array
     */
    protected function getDates()
    {
        return config('slack-output.stats.dates');
    }


    /**
     * Do the stats!
     */
    public function calculateStats()
    {
        $classes = $this->getClasses();
        $dates   = $this->getDates();

        //explore all class to stated
        foreach ($classes as $objectClass => $constraints) {
            //prepare useful data
            $stats_fields = [ ];

            $objectName = last(explode('\\', $objectClass));

            //explore each date to count from
            foreach ($dates as $dateName => $date) {
                //create the sql request
                $sql = $objectClass::where('created_at', '>=', $date->toDateTimeString());

                //taking into account the constraint
                foreach ($constraints as $constraintName => $constraintValue) {
                    $sql = $sql->where($constraintName, $constraintValue);
                }

                //count !
                $count = $sql->count();

                //set count
                $stats_fields[] = [
                    "since" => $dateName,
                    "value" => $count
                ];
            }

            //add to stats array
            $this->stats[] = [
                'name'   => $objectName,
                'values' => $stats_fields
            ];
        }
    }


    /**
     * Transform the stats array to a slack attachment
     */
    protected function prepareSlackAttachment()
    {
        $attachments = [ ];

        foreach ($this->stats as $stats) {
            $name = $stats['name'];

            $fields = [ ];

            foreach ($stats['values'] as $stat) {
                $count = $stat['value'];
                $since = $stat['since'];

                $fields[] = [
                    "title" => "Since " . $since,
                    "value" => $count,
                    "short" => true
                ];
            }

            $attachments[] = [
                'color'  => 'grey',
                "title"  => "New " . $name . "s",
                "fields" => $fields
            ];
        }

        return $attachments;
    }


    /**
     * Send the stats to output
     *
     * @param $channel
     */
    public function sendToSlack($channel)
    {
        $attachments = $this->prepareSlackAttachment();

        Artisan::call('slack:post', [
            'to'      => $channel,
            'attach'  => $attachments,
            'message' => "Stats of the " . Carbon::now()->toFormattedDateString()
        ]);
    }

}