<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column]
    private ?int $nbPlace = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Teacher $teacher = null;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Planning::class, orphanRemoval: true)]
    private Collection $plannings;

    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'sessions')]
    private Collection $students;


    public function __construct()
    {
        $this->plannings = new ArrayCollection();
        $this->students = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }
    public function dateEndByPlanning()
    {
        $totalDuration=$this->getTotalDuration;
        $dateEndByPlanning= date_add($this->dateStart, date_interval_create_from_date_string("$totalDuration days"));
        return $dateEndByPlanning;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection<int, Planning>
     */
    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings->add($planning);
            $planning->setSession($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getSession() === $this) {
                $planning->setSession(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
        }
        
        return $this;
    }
    
    
    public function removeStudent(Student $student): self
    {
        $this->students->removeElement($student);
        
        return $this;
    }
    
    //Pour compter le nombre de jours total d'une session en adition la durée des plannings
    public function getTotalDuration()
    {
        $totalDuration=0;

        foreach($this->plannings as $planning)
        {
            $totalDuration+=$planning->getNbDay();
        }

        return $totalDuration;
    }

    //Pour compter le nombre de places restantes
    public function getAvailableNbPlace()
    {
        $nbStudent = count($this->students);
        return $this->nbPlace - $nbStudent;
    }
    
    //Pour compter le nombre de plannings disponibles
    public function getNbPlannings()
    {
        $nbPlannings = count($this->plannings);
        return $nbPlannings;
    }
    
    public function __toString()
    {
        return $this->name;
    }
}
