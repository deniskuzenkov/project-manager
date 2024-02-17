<?php

namespace App\Model\Work\UseCase\Projects\Role\Edit;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

class Handler
{
    private RoleRepository $roles;
    private Flusher $flusher;

    public function __construct(RoleRepository $roles, Flusher $flusher)
    {
        $this->flusher = $flusher;
        $this->roles = $roles;
    }

    public function handle(Command $command): void
    {
        $role = $this->roles->get(new Id($command->id));
        $role->edit($command->name, $command->permissions);
        $this->flusher->flush();
    }
}