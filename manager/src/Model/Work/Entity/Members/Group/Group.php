<?php

namespace App\Model\Work\Entity\Members\Group;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="work_members_group")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\Column(type="work_members_group_id")
     */
    private Id $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    public function __construct(Id $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function edit(string $name): void{
        $this->name = $name;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


}