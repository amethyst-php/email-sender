<?php

namespace Railken\Amethyst\Managers;

use Illuminate\Support\Collection;
use Railken\Amethyst\Common\ConfigurableManager;
use Railken\Amethyst\Exceptions;
use Railken\Amethyst\Jobs\EmailSender\SendEmail;
use Railken\Amethyst\Models\DataBuilder;
use Railken\Amethyst\Models\EmailSender;
use Railken\Bag;
use Railken\Lem\Manager;
use Railken\Lem\Result;
use Symfony\Component\Yaml\Yaml;

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
     * @param EmailSender $email
     * @param array       $data
     *
     * @return \Railken\Lem\Contracts\ResultContract
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
