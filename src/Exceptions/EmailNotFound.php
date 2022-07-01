<?php
namespace ErrorNotifier\Notify\Exceptions;

use RuntimeException;

class EmailNotFound extends RunTimeException
{
    public static function make(?string $name): self
    {
        return new self("The notifier email is not found, make sure you have .env file with a variable named: NOTIFIER_EMAIL and a value");
    }
}
