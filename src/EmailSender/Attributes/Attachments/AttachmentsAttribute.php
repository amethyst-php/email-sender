<?php

namespace Railken\LaraOre\EmailSender\Attributes\Attachments;

use Railken\Laravel\Manager\Attributes\BaseAttribute;
use Railken\Laravel\Manager\Contracts\EntityContract;
use Railken\Laravel\Manager\Tokens;
use Respect\Validation\Validator as v;

class AttachmentsAttribute extends BaseAttribute
{
    /**
     * Name attribute.
     *
     * @var string
     */
    protected $name = 'attachments';

    /**
     * Is the attribute required
     * This will throw not_defined exception for non defined value and non existent model.
     *
     * @var bool
     */
    protected $required = false;

    /**
     * Is the attribute unique.
     *
     * @var bool
     */
    protected $unique = false;

    /**
     * List of all exceptions used in validation.
     *
     * @var array
     */
    protected $exceptions = [
        Tokens::NOT_DEFINED    => Exceptions\EmailSenderAttachmentsNotDefinedException::class,
        Tokens::NOT_VALID      => Exceptions\EmailSenderAttachmentsNotValidException::class,
        Tokens::NOT_AUTHORIZED => Exceptions\EmailSenderAttachmentsNotAuthorizedException::class,
        Tokens::NOT_UNIQUE     => Exceptions\EmailSenderAttachmentsNotUniqueException::class,
    ];

    /**
     * List of all permissions.
     */
    protected $permissions = [
        Tokens::PERMISSION_FILL => 'emailsender.attributes.attachments.fill',
        Tokens::PERMISSION_SHOW => 'emailsender.attributes.attachments.show',
    ];

    /**
     * Is a value valid ?
     *
     * @param \Railken\Laravel\Manager\Contracts\EntityContract $entity
     * @param mixed                                             $value
     *
     * @return bool
     */
    public function valid(EntityContract $entity, $value)
    {
        return v::length(1, 255)->validate($value);
    }
}
