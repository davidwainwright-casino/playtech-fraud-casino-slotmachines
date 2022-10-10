<?php

namespace Laravel\Nova;

trait WithBadge
{
    /**
     * The badge content for the menu item.
     *
     * @var (\Closure():\Laravel\Nova\Badge)|(callable():\Laravel\Nova\Badge)|\Laravel\Nova\Badge|null
     */
    public $badgeCallback;

    /**
     * The condition for showing the badge inside the menu item.
     *
     * @var (\Closure():bool)|bool
     */
    public $badgeCondition = true;

    /**
     * The type of badge that should represent the item.
     *
     * @var string
     */
    public $badgeType = 'info';

    /**
     * Set the content to be used for the item's badge.
     *
     * @param  (\Closure():\Laravel\Nova\Badge)|(callable():\Laravel\Nova\Badge)|\Laravel\Nova\Badge|string  $badgeCallback
     * @param  string|null  $type
     * @return $this
     */
    public function withBadge($badgeCallback, $type = 'info')
    {
        $this->badgeType = $type;

        if (is_callable($badgeCallback) || $badgeCallback instanceof Badge) {
            $this->badgeCallback = $badgeCallback;
        }

        if (is_string($badgeCallback)) {
            $this->badgeCallback = function () use ($badgeCallback, $type) {
                return Badge::make($badgeCallback, $type);
            };
        }

        return $this;
    }

    /**
     * Set the content to be used for the item's badge if the condition matches.
     *
     * @param  (\Closure():\Laravel\Nova\Badge)|(callable():\Laravel\Nova\Badge)|\Laravel\Nova\Badge|string  $badgeCallback
     * @param  string|null  $type
     * @param  (\Closure():bool)|bool  $condition
     * @return $this
     */
    public function withBadgeIf($badgeCallback, $type, $condition)
    {
        $this->badgeCondition = $condition;

        $this->withBadge($badgeCallback, $type);

        return $this;
    }

    /**
     * Resolve the badge for the item.
     *
     * @return \Laravel\Nova\Badge|null
     */
    public function resolveBadge()
    {
        if (value($this->badgeCondition)) {
            if (is_callable($this->badgeCallback)) {
                $result = call_user_func($this->badgeCallback);

                if (is_null($result)) {
                    throw new \Exception('A menu item badge must always have a value.');
                }

                if (! $result instanceof Badge) {
                    return Badge::make($result, $this->badgeType);
                }

                return $result;
            }

            return $this->badgeCallback;
        }
    }
}
