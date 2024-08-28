<?php

namespace Spatie\Ignition\Contracts;

<<<<<<< HEAD
interface RunnableSolution extends \Spatie\ErrorSolutions\Contracts\RunnableSolution
{
=======
interface RunnableSolution extends Solution
{
    public function getSolutionActionDescription(): string;

    public function getRunButtonText(): string;

    /** @param array<string, mixed> $parameters */
    public function run(array $parameters = []): void;

    /** @return array<string, mixed> */
    public function getRunParameters(): array;
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
}
