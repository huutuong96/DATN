<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Iterators;


<<<<<<< HEAD

/**
 * Applies the callback to the elements of the inner iterator.
=======
/**
 * @deprecated use Nette\Utils\Iterables::map()
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 */
class Mapper extends \IteratorIterator
{
	/** @var callable */
	private $callback;


	public function __construct(\Traversable $iterator, callable $callback)
	{
		parent::__construct($iterator);
		$this->callback = $callback;
	}


	public function current(): mixed
	{
		return ($this->callback)(parent::current(), parent::key());
	}
}
