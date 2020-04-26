<?php

namespace Amethyst\Http\Controllers;

use Amethyst\Core\Http\Controllers\RestManagerController;
use Amethyst\Core\Http\Controllers\Traits as RestTraits;
use Amethyst\Managers\DataBuilderManager;
use Amethyst\Managers\EmailSenderManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailSenderController extends RestManagerController
{
    public function __construct()
    {
        $this->manager = app('amethyst')->get('email-sender');
    }

    /**
     * Generate.
     *
     * @param int                      $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute(int $id, Request $request)
    {
        /** @var \Amethyst\Managers\EmailSenderManager */
        $manager = $this->manager;

        /** @var \Amethyst\Models\EmailSender */
        $email = $manager->getRepository()->findOneById($id);

        if ($email == null) {
            abort(404);
        }

        $result = $manager->execute($email, (array) $request->input('data'));

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(Request $request)
    {
        /** @var \Amethyst\Managers\EmailSenderManager */
        $manager = $this->manager;

        $dbm = (new DataBuilderManager());

        /** @var \Amethyst\Models\DataBuilder */
        $data_builder = $dbm->getRepository()->findOneById(intval($request->input('data_builder_id')));

        if ($data_builder == null) {
            return $this->error([['message' => 'invalid data_builder_id']]);
        }

        $data = (array) $request->input('data');

        $result = $dbm->build($data_builder, $data);

        if (!$result->ok()) {
            return $this->error(['errors' => $result->getSimpleErrors()]);
        }

        $data = array_merge($data, $result->getResource());

        if ($result->ok()) {
            $result = $manager->render(
                $data_builder,
                [
                    'body' => strval($request->input('body')),
                ],
                $data
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
