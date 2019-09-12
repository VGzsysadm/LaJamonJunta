<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Email extends Constraint
{
    public $domains;
    public $message = 'Solamente se aceptan emails @yahoo, @gmail, @outlook.es, @outlook.com, @hotmail.com, @protonmail.com, @protonmail.ch, @yandex.com, @tutanota.com, @tutanota.de, @tutamail.com, @tuta.io, @keemail.me, @icloud.com';
}