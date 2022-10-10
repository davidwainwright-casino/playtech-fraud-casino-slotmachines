<?php

namespace Laravel\Nova;

use JsonSerializable;

/**
 * @method static static make(string|self $url, bool $remote = false)
 */
class URL implements JsonSerializable
{
    use Makeable;

    /**
     * The URL.
     *
     * @var string
     */
    public $url;

    /**
     * Determine URL is remote.
     *
     * @var bool
     */
    public $remote;

    /**
     * Construct a new URL instance.
     *
     * @param  string|self  $url
     * @param  bool  $remote
     */
    public function __construct($url, $remote = false)
    {
        if ($url instanceof self) {
            $this->url = $url->url;
            $this->remote = $url->remote;

            return;
        }

        $this->url = $url;
        $this->remote = $remote;
    }

    /**
     * Make a remote URL.
     *
     * @param  string  $url
     * @return static
     */
    public static function remote($url)
    {
        return new static($url, true);
    }

    /**
     * Get the URL.
     *
     * @return string
     */
    public function get()
    {
        return $this->remote === true ? $this->url : Nova::url($this->url);
    }

    /**
     * Determine if currently an active URL.
     *
     * @return bool
     */
    public function active()
    {
        return with(ltrim($this->get(), '/'), function ($url) {
            return request()->is($url, rtrim($url, '/').'/*');
        });
    }

    /**
     * Convert the URL instance to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * Prepare the URL for JSON serialization.
     *
     * @return array{url: string, remote: bool}
     */
    public function jsonSerialize(): array
    {
        return [
            'url' => $this->get(),
            'remote' => $this->remote,
        ];
    }
}
