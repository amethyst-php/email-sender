<?php

namespace Amethyst\Managers;

use Amethyst\Common\ConfigurableManager;
use Amethyst\Exceptions;
use Amethyst\Jobs\EmailSender\SendEmail;
use Amethyst\Models\DataBuilder;
use Amethyst\Models\EmailSender;
use Illuminate\Support\Collection;
use Railken\Bag;
use Railken\Lem\Manager;
use Railken\Lem\Result;
use Symfony\Component\Yaml\Yaml;

/**
 * @method \Amethyst\Models\EmailSender                 newEntity()
 * @method \Amethyst\Schemas\EmailSenderSchema          getSchema()
 * @method \Amethyst\Repositories\EmailSenderRepository getRepository()
 * @method \Amethyst\Serializers\EmailSenderSerializer  getSerializer()
 * @method \Amethyst\Validators\EmailSenderValidator    getValidator()
 * @method \Amethyst\Authorizers\EmailSenderAuthorizer  getAuthorizer()
 */
class EmailSenderManager extends Manager
{
    use ConfigurableManager;

    /**
     * @var string
     */
    protected $config = 'amethyst.email-sender.data.email-sender';

    /**
     * Send an email..
     *
     * @param EmailSender|int $email
     * @param array           $data
     *
     * @return \Railken\Lem\Contracts\ResultContract
     */
    public function execute($email, $data = [])
    {
        $data = is_object($data) ? (array) $data : $data;
        
        $email = is_int($email) ? $this->getRepository()->findOneById($email) : $email;

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
     * @return \Railken\Lem\Contracts\ResultContract
     */
    public function render(DataBuilder $data_builder, $parameters, array $data = [])
    {
        $parameters = $this->castParameters($parameters);

        $tm = new TemplateManager();

        $result = new Result();

        try {
            $bag = new Bag($parameters);

            $bag->set('body', $tm->renderRaw('text/html', strval($bag->get('body')), $data));

            $attachments = [];

            foreach ((array) Yaml::parse(strval($bag->get('attachments'))) as $key => $attachment) {
                $attachment = (object) $attachment;

                $attachments[$key]['as'] = strval($tm->renderRaw('text/plain', $attachment->as, $data));

                $attachments[$key]['source'] = strval($tm->renderRaw('text/plain', $attachment->source, $data));
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

    /**
     * Describe extra actions.
     *
     * @return array
     */
    public function getDescriptor()
    {
        return [
            'components' => [
                'renderer',
            ],
            'actions' => [
                'executor',
            ],
        ];
    }
}
