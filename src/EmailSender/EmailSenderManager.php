<?php

namespace Railken\LaraOre\EmailSender;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Railken\LaraOre\DataBuilder\DataBuilder;
use Railken\LaraOre\DataBuilder\DataBuilderManager;
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
     * Render an email.
     *
     * @param DataBuilder $data_builder
     * @param string      $body
     * @param array       $data
     *
     * @return \Railken\Laravel\Manager\Contracts\ResultContract
     */
    public function render(DataBuilder $data_builder, string $body, array $data = [])
    {
        $repository = $data_builder->repository;
        $input = $data_builder->input;

        $result = new Result();
        $result->addErrors((new DataBuilderManager())->getValidator()->raw((array) $input, $data));

        if (!$result->ok()) {
            return $result;
        }

        $tm = new TemplateManager();

        try {
            $query = $repository->newInstanceQuery($data);

            $resources = $query->get();

            $rendered = $tm->renderRaw('text/html', strval($body), array_merge($data, $repository->parse($resources)));

            $result->setResources(new Collection($rendered));
        } catch (FormattingException | \PDOException | \Railken\SQ\Exceptions\QuerySyntaxException $e) {
            $e = new Exceptions\EmailSenderRenderException($e->getMessage());
            $result->addErrors(new Collection([$e]));
        } catch (\Twig_Error $e) {
            $e = new Exceptions\EmailSenderRenderException($e->getRawMessage().' on line '.$e->getTemplateLine());

            $result->addErrors(new Collection([$e]));
        }

        return $result;
    }
}
