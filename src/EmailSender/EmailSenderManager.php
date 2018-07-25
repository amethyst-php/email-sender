<?php

namespace Railken\LaraOre\EmailSender;

use Illuminate\Support\Facades\Config;
use Railken\Laravel\Manager\Contracts\AgentContract;
use Railken\Laravel\Manager\ModelManager;
use Railken\Laravel\Manager\Tokens;

class EmailSenderManager extends ModelManager
{
    /**
     * Class name entity.
     *
     * @var string
     */
    public $entity = EmailSender::class;

    /**
     * List of all attributes.
     *
     * @var array
     */
    protected $attributes = [
        Attributes\Id\IdAttribute::class,
        Attributes\Name\NameAttribute::class,
        Attributes\Description\DescriptionAttribute::class,
        Attributes\DataBuilderId\DataBuilderIdAttribute::class,
        Attributes\CreatedAt\CreatedAtAttribute::class,
        Attributes\UpdatedAt\UpdatedAtAttribute::class,
        Attributes\DeletedAt\DeletedAtAttribute::class,
        Attributes\Recipients\RecipientsAttribute::class,
        Attributes\Sender\SenderAttribute::class,
        Attributes\Body\BodyAttribute::class,
        Attributes\Subject\SubjectAttribute::class,
        Attributes\Attachments\AttachmentsAttribute::class,
    ];

    /**
     * List of all exceptions.
     *
     * @var array
     */
    protected $exceptions = [
        Tokens::NOT_AUTHORIZED => Exceptions\EmailSenderNotAuthorizedException::class,
    ];

    /**
     * Construct.
     *
     * @param AgentContract $agent
     */
    public function __construct(AgentContract $agent = null)
    {
        $this->entity = Config::get('ore.email-sender.entity');
        $this->attributes = array_merge($this->attributes, array_values(Config::get('ore.email-sender.attributes')));

        $classRepository = Config::get('ore.email-sender.repository');
        $this->setRepository(new $classRepository($this));

        $classSerializer = Config::get('ore.email-sender.serializer');
        $this->setSerializer(new $classSerializer($this));

        $classAuthorizer = Config::get('ore.email-sender.authorizer');
        $this->setAuthorizer(new $classAuthorizer($this));

        $classValidator = Config::get('ore.email-sender.validator');
        $this->setValidator(new $classValidator($this));

        parent::__construct($agent);
    }
}
