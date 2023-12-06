<?php

namespace App\Services\ThirdParty;

use Throwable;
use Illuminate\Support\{Carbon, Str};
use App\Exceptions\CustomException;

class Spreaker
{
    /**
     * The channel
     */
    private $channel;

    /**
     * Create an instance
     */
    public function __construct($feed)
    {
        try {
            // Get the xml content
            $data = simplexml_load_string($feed);
        } catch (Throwable $e) {
            throw new CustomException('Unable to parse RSS feed: '.$e->getMessage(), 503);
        }

        if ($data === false) {
            throw new CustomException('The RSS feed contains invalid XML.', 503);
        }

        $this->channel = $data;
    }

    /**
     * Process the data gotten from an RSS feed
     */
    public function processFeed()
    {
        return [
            'podcast' => $this->podcastOrShow(),
            'episodes' => $this->episodes()
        ];
    }

    /**
     * Get the podcast or show in the feed
     */
    private function podcastOrShow()
    {
        return [
            'title' => $this->callbackData($this->channel->xpath('/rss/channel/title'), fn($value) => strval($value)),
            'link' => $this->callbackData($this->channel->xpath('/rss/channel/link'), fn($value) => strval($value)),
            'description' => $this->callbackData($this->channel->xpath('/rss/channel/description'), fn($value) => strval($value)),
            'category' => $this->callbackData($this->channel->xpath('/rss/channel/category'), fn($value) => strval($value)),
            'image' => $this->callbackData($this->channel->xpath('/rss/channel/image/url'), fn($value) => strval($value)),
            'itunes' => [
                'owner' => [
                    'name' => $this->callbackData($this->channel->xpath('/rss/channel/itunes:owner/itunes:name'), fn($value) => strval($value)),
                    'email' => $this->callbackData($this->channel->xpath('/rss/channel/itunes:owner/itunes:email'), fn($value) => strval($value)),
                ],
                'subtitle' => $this->callbackData($this->channel->xpath('/rss/channel/itunes:subtitle'), fn($value) => strval($value)),
                'summary' => $this->callbackData($this->channel->xpath('/rss/channel/itunes:summary'), fn($value) => strval($value)),
                'explicit' => $this->callbackData($this->channel->xpath('/rss/channel/itunes:explicit'), fn($value) => strval($value)),
                'type' => $this->callbackData($this->channel->xpath('/rss/channel/itunes:type'), fn($value) => strval($value)),
            ]
        ];
    }

    /**
     * Get the episodes of the podcast or show
     */
    private function episodes()
    {
        $items = $this->channel->xpath('/rss/channel/item');

        // Parse each episodes
        return collect($items)->reverse()
                            ->values()
                            ->map(fn($item, $key) => $this->parseEpisode($item, $key));
    }

    /**
     * Parse the content of an episode
     */
    private function parseEpisode($episode, $number)
    {
        return [
            'episode' => $number + 1,
            'name' => $this->callbackData($episode->xpath('title'), fn($value) => strval($value)),
            'description' => $this->callbackData($episode->xpath('description'), fn($value) => strval($value)),
            'guid' => $this->callbackData($episode->xpath('guid'), fn($value) => strval($value)),
            'published_at' => $this->callbackData($episode->xpath('pubDate'), fn($value) => Carbon::parse($value)->toJSON()),
            'enclosure_url' => $this->callbackData($episode->xpath('enclosure')[0]->attributes()['url'], fn($value) => strval($value)),
            'type' => $this->callbackData($episode->xpath('enclosure')[0]->attributes()['type'], fn($value) => strval($value)),
            'itunes' => [
                'author' => $this->callbackData($episode->xpath('itunes:author'), fn($value) => strval($value)),
                'subtitle' => $this->callbackData($episode->xpath('itunes:subtitle'), fn($value) => strval($value)),
                'summary' => $this->callbackData($episode->xpath('itunes:summary'), fn($value) => strval($value)),
                'duration' => $this->callbackData($episode->xpath('itunes:duration'), fn($value) => $value.' '.Str::plural('minute', $value)),
                'keywords' => $this->callbackData($episode->xpath('itunes:keywords'), fn($value) => explode(',', $value)),
                'explicit' => $this->callbackData($episode->xpath('itunes:explicit'), fn($value) => strval($value)),
                'season' => $this->callbackData($episode->xpath('itunes:season'), fn($value) => strval($value)),
                'episode_type' => $this->callbackData($episode->xpath('itunes:episodeType'), fn($value) => strval($value)),
                'image' => $this->callbackData($episode->xpath('itunes:image')[0]->attributes()['href'], fn($value) => strval($value)),
            ]
        ];
    }



    /**
     * Pass a callback to a given value in parsing of episodes
     */
    private function callbackData($data, $callback)
    {
        return isset($data[0]) ? $callback($data[0]) : null;
    }
}