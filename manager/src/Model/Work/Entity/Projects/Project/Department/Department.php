<?php

namespace App\Model\Work\Entity\Projects\Project\Department;

use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="work_projects_project_departments")
 */
class Department
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Projects\Project\Project", inversedBy="departments")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    private Project $project;

    /**
     * @var Id
     * @ORM\Column(type="work_projects_project_department_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $name;

    public function __construct(Project $project, Id $id, string $name)
    {
        $this->project = $project;
        $this->id = $id;
        $this->name =$id;
    }

    public function isNameEqual(string $name): bool
    {
        return $this->name === $name;
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