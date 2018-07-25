<?php

namespace Railken\LaraOre\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Railken\LaraOre\Api\Http\Controllers\RestConfigurableController;
use Railken\LaraOre\Api\Http\Controllers\Traits as RestTraits;
use Railken\LaraOre\DataBuilder\DataBuilderManager;

class EmailSendersController extends RestConfigurableController
{
    use RestTraits\RestIndexTrait;
    use RestTraits\RestShowTrait;
    use RestTraits\RestCreateTrait;
    use RestTraits\RestUpdateTrait;
    use RestTraits\RestRemoveTrait;

    /**
     * The config path.
     *
     * @var string
     */
    public $config = 'ore.email-sender';

    /**
     * The attributes that are queryable.
     *
     * @var array
     */
    public $queryable = [
        'id',
        'name',
        'sender',
        'recipients',
        'subject',
        'body',
        'attachments',
        'description',
        'data_builder_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are fillable.
     *
     * @var array
     */
    public $fillable = [
        'name',
        'sender',
        'recipients',
        'subject',
        'body',
        'attachments',
        'description',
        'data_builder',
        'data_builder_id',
    ];

    /**
     * Generate.
     *
     * @param int                      $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function send(int $id, Request $request)
    {
        /** @var \Railken\LaraOre\EmailSender\EmailSenderManager */
        $manager = $this->manager;

        /** @var \Railken\LaraOre\EmailSender\EmailSender */
        $email = $manager->getRepository()->findOneById($id);

        if ($email == null) {
            return $this->not_found();
        }

        $result = $manager->send($email, (array) $request->input('data'));

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        return $this->success([]);
    }

    /**
     * Render raw template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request)
    {
        /** @var \Railken\LaraOre\EmailSender\EmailSenderManager */
        $manager = $this->manager;

        $dbm = (new DataBuilderManager());

        /** @var \Railken\LaraOre\DataBuilder\DataBuilder */
        $data_builder = $dbm->getRepository()->findOneById(intval($request->input('data_builder_id')));

        if ($data_builder == null) {
            return $this->error([['message' => 'invalid data_builder_id']]);
        }

        $result = $dbm->build($data_builder, (array) $request->input('data'));

        if ($result->ok()) {
            $result = $manager->render(
                $data_builder,
                [
                    'body' => strval($request->input('body')),
                ],
                $result->getResource()
            );
        }

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        $resource = $result->getResource();

        return $this->success(['resource' => [
            'body'    => base64_encode($resource['body']),
            'subject' => base64_encode($resource['subject']),
        ]]);
    }
}
