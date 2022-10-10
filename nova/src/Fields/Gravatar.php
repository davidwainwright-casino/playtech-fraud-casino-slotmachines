<?php

namespace Laravel\Nova\Fields;

/**
 * @method static static make(mixed $name = 'Avatar', string|null $attribute = 'email')
 */
class Gravatar extends Avatar
{
    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @return void
     */
    public function __construct($name = 'Avatar', $attribute = 'email')
    {
        parent::__construct($name, $attribute ?? 'email');

        $this->exceptOnForms();

        $this->maxWidth(50);
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute)
    {
        $callback = function () use ($resource, $attribute) {
            $first_letter = strtoupper(substr(parent::resolveAttribute($resource, $attribute), 0,1));
            $avatar_1 = ['A', 'B', 'C', 'D', 'E'];
            $avatar_2 = ['I', 'J', 'K', 'L', 'M'];
            $avatar_3 = ['Q', 'R', 'S', 'T', 'U'];
            $avatar_4 = ['Y', 'Z', 'F', 'G', 'H'];
            $avatar_5 = ['V', 'W', 'X', 'N', 'O', 'P'];

            if(in_array($first_letter, $avatar_1)) {
                return 'https://i.imgur.com/Ai85I0z.jpeg';

            }
            if(in_array($first_letter, $avatar_2)) {
                return 'https://i.imgur.com/7oAJiiQ.jpeg';
            }
            if(in_array($first_letter, $avatar_3)) {
                return 'https://i.imgur.com/WQbzjUe.jpeg';
            }
            if(in_array($first_letter, $avatar_4)) {
                return 'https://i.imgur.com/ZsMH8OW.jpeg';
            }
            if(in_array($first_letter, $avatar_5)) {
                return 'https://i.imgur.com/tDpMU7z.jpeg';
            }

            return 'https://www.gravatar.com/avatar/'.md5(strtolower(parent::resolveAttribute($resource, $attribute))).'?s=300';
        };

        $this->preview($callback)->thumbnail($callback);
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge([
            'indexName' => '',
        ], parent::jsonSerialize());
    }
}
