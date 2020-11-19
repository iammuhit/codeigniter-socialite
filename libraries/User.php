<?php

namespace App\Modules\Social\Libraries;

class User implements \IteratorAggregate {

    /**
     * The user's primary ID.
     *
     * @var mixed
     */
    public $id = null;

    /**
     * The user's unique ID.
     *
     * @var mixed
     */
    public $uid = null;

    /**
     * The user's nickname (screen name, username etc).
     *
     * @var mixed
     */
    public $nickname = null;

    /**
     * The user's username
     *
     * @var mixed
     */
    public $username = null;

    /**
     * The user's username
     *
     * @var mixed
     */
    public $display_name = null;

    /**
     * The user's name.
     *
     * @var mixed
     */
    public $name = null;

    /**
     * The user's first name.
     *
     * @var string
     */
    public $first_name = null;

    /**
     * The user's last name.
     *
     * @var string
     */
    public $last_name = null;

    /**
     * The user's email.
     *
     * @var string
     */
    public $email = null;

    /**
     * The user's location.
     *
     * @var string|array
     */
    public $location = null;

    /**
     * The user's description.
     *
     * @var string
     */
    public $description = null;

    /**
     * The user's image URL.
     *
     * @var string
     */
    public $imageUrl = null;

    /**
     * The users' URLs.
     *
     * @var string|array
     */
    public $urls = [];

    /**
     * Any extra data.
     *
     * @var array
     */
    public $extra = [];

    /**
     * Set a property on the user.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value) {
        if (isset($this->{$key})) {
            $this->{$key} = $value;
        }
    }

    /**
     * Get a property from the user.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key) {
        if (isset($this->{$key})) {
            return $this->{$key};
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator() {
        return new \ArrayIterator($this);
    }

}
