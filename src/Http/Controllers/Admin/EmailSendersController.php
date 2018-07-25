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

        /** @var \Railken\LaraOre\DataBuilder\DataBuilder */
        $data_builder = (new DataBuilderManager())->getRepository()->findOneById(intval($request->input('data_builder_id')));

        if ($data_builder == null) {
            return $this->error([['message' => 'invalid data_builder_id']]);
        }

        $result = $manager->render(
            $data_builder,
            strval($request->input('body')),
            (array) $request->input('data')
        );

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        return $this->success(['resource' => base64_encode($result->getResource())]);
    }
}
