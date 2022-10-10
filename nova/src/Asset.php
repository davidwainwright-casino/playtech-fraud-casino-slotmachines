<?php

namespace Laravel\Nova;

use DateTime;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Str;

/**
 * @method static static make(string|self $name, string|null $path, bool|null $remote = null)
 */
abstract class Asset implements Responsable
{
    use Makeable;

    /**
     * The Assert name.
     *
     * @var string
     */
    protected $name;

    /**
     * The Asset path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * Determine Asset is remote.
     *
     * @var bool
     */
    protected $remote;

    /**
     * Construct a new Asset instance.
     *
     * @param  string|self  $name
     * @param  string|null  $path
     * @param  bool|null  $remote
     */
    public function __construct($name, $path, $remote = null)
    {
        if ($name instanceof self) {
            $this->name = $name->name();
            $this->path = $name->path();
            $this->remote = $name->isRemote();

            return;
        }

        if (is_null($remote)) {
            $remote = Str::startsWith($path, ['http://', 'https://', '://']);
        }

        $this->name = $name;
        $this->path = $path;
        $this->remote = $remote;
    }

    /**
     * Make a remote URL.
     *
     * @param  string  $path
     * @return static
     */
    public static function remote($path)
    {
        return new static(md5($path), $path, true);
    }

    /**
     * Get asset name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get asset path.
     *
     * @return string|null
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Determine if URL is remote.
     *
     * @return bool
     */
    public function isRemote()
    {
        return $this->remote;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        abort_if($this->isRemote() || is_null($this->path), 404);

        return response(
            file_get_contents($this->path), 200, $this->toResponseHeaders(),
        )->setLastModified(DateTime::createFromFormat('U', (string) filemtime($this->path)));
    }

    /**
     * Get the Asset URL.
     *
     * @return string
     */
    abstract public function url();

    /**
     * Get response headers.
     *
     * @return array<string, string>
     */
    abstract public function toResponseHeaders();
}
