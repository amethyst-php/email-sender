<?php

namespace Railken\LaraOre\EmailSender;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Railken\Bag;
use Railken\LaraOre\DataBuilder\DataBuilder;
use Railken\LaraOre\DataBuilder\DataBuilderManager;
use Railken\LaraOre\Jobs\EmailSender\SendEmail;
use Railken\LaraOre\Template\TemplateManager;
use Railken\Laravel\Manager\Contracts\AgentContract;
use Railken\Laravel\Manager\ModelManager;
use Railken\Laravel\Manager\Result;
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

    /**
     * Send an email..
     *
     * @param EmailSender $email
     * @param array       $data
     *
     * @return \Railken\Laravel\Manager\Contracts\ResultContract
     */
    public function send(EmailSender $email, array $data = [])
    {
        $result = (new DataBuilderManager())->validateRaw($email->data_builder, $data);

        dispatch(new SendEmail($email, $data, $this->getAgent()));

        return $result;
    }

    /**
     * Render an email.
     *
     * @param DataBuilder $data_builder
     * @param array       $parameters
     * @param array       $data
     *
     * @return \Railken\Laravel\Manager\Contracts\ResultContract
     */
    public function render(DataBuilder $data_builder, $parameters, array $data = [])
    {
        $parameters = $this->castParameters($parameters);

        $tm = new TemplateManager();

        $data = $data;

        $result = new Result();

        try {
            $bag = new Bag($parameters);

            $bag->set('body', $tm->renderRaw('text/html', strval($bag->get('body')), $data));

            $attachments = [];

            foreach ((array) $bag->get('attachments', []) as $key => $attachment) {
                $attachment = (object) $attachment;

                $attachments[$key]['as'] = strval($tm->renderRaw('text/plain', $attachment->as, $data));

                $attachments[$key]['source'] = (new Bag($data))->get($attachment->source);
            }

            $bag->set('attachments', $attachments);

            $bag->set('recipients', explode(',', $tm->renderRaw('text/plain', strval($bag->get('recipients')), $data)));
            $bag->set('subject', $tm->renderRaw('text/plain', strval($bag->get('subject')), $data));
            $bag->set('sender', $tm->renderRaw('text/plain', strval($bag->get('sender')), $data));

            $result->setResources(new Collection([$bag->toArray()]));
        } catch (\Twig_Error $e) {
            $e = new Exceptions\EmailSenderRenderException($e->getRawMessage().' on line '.$e->getTemplateLine());

            $result->addErrors(new Collection([$e]));
        }

        return $result;
    }
}
