<?php

declare(strict_types=1);

namespace Dotenv\Parser;

use PhpOption\Option;

final class Entry
{
    /**
     * The entry name.
     *
     * @var string
     */
    private $name;

    /**
     * The entry value.
     *
     * @var \Dotenv\Parser\Value|null
     */
    private $value;

    /**
     * Create a new entry instance.
     *
     * @param string                    $name
     * @param \Dotenv\Parser\Value|null $value
     *
     * @return void
     */
<<<<<<< HEAD
    public function __construct(string $name, Value $value = null)
=======
    public function __construct(string $name, ?Value $value = null)
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get the entry name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the entry value.
     *
     * @return \PhpOption\Option<\Dotenv\Parser\Value>
     */
    public function getValue()
    {
        /** @var \PhpOption\Option<\Dotenv\Parser\Value> */
        return Option::fromValue($this->value);
    }
}
