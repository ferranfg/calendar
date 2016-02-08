<?php

namespace Ferranfg\Calendar\Models;

class Item
{
    protected $attributes;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) return $this->attributes[$key];
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function toArray()
    {
        return json_decode(json_encode($this->attributes), true);
    }

}