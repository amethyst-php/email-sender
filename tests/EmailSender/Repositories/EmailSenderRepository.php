<?php

namespace Railken\LaraOre\Tests\EmailSender\Repositories;

use Closure;
use Illuminate\Support\Collection;
use Railken\LaraOre\Contracts\RepositoryContract;
use Railken\LaraOre\EmailSender\EmailSenderManager;

class EmailSenderRepository implements RepositoryContract
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new EmailSenderManager();
    }

    public function newQuery()
    {
        return $this->manager->getRepository()->newQuery();
    }

    public function getTableName()
    {
        return $this->manager->newEntity()->getTable();
    }

    /**
     * @param Collection $resources
     * @param \Closure   $callback
     */
    public function extract(Collection $resources, Closure $callback)
    {
        foreach ($resources as $resource) {
            $callback($resource, ['record' => $resource]);
        }
    }

    /**
     * @param Collection $resources
     *
     * @return Collection
     */
    public function parse(Collection $resources)
    {
        return ['records' => $resources];
    }
}
