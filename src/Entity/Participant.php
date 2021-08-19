<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity("email")
 * @UniqueEntity("pseudo")
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 */
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @Assert\Length(min=3,minMessage="Le pseudo doit etre de plus de 3 characteres",max=15,maxMessage="Le pseudo doit etre de plus de 15 characteres")
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $pseudo;

    /**
     *
     * @Assert\Length(max=180,maxMessage="L'email est trop long")
     * @Assert\Email(message="Please enter a valid email address.")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Assert\Length(min="10",minMessage="Le mot de passe doit d'etre de minimum 10 characteres.")
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     *  @Assert\Length(min="3",minMessage="Le nom doit d'etre de minimum 3 characteres.",
     *                  max="15", maxMessage="Le nom doit d'etre de maximun 15 characteres.")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Assert\Length(min="3",minMessage="Le prenom doit d'etre de minimum 3 characteres.",
     *                  max="15", maxMessage="Le prenom doit d'etre de maximun 15 characteres.")
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     *
     * @Assert\Length(max="10",min="10",exactMessage="Veuillez saisir un numero valide")
     * @ORM\Column(type="string", length=11)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="participant")
     */
    private $participations;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $organisations;

    /**
     *
     * @ORM\Column(type="string", length=40)
     */
    private $photo;


    


    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->organisations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->prenom.' '.$this->nom;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    public function removeRole(string $role): bool
    {
        $key = array_search($role, $this->roles, true);
        if(!$key){
            return false;
        }
        array_splice($this->roles, $key);
        return true;

    }

    /**
     * @param string $role rôle recherché
     * @return bool  true si le rôle est contenu dans les rôles de l'utilisateur, false sinon
     */
    public function hasRole(string $role): Boolean
    {
        return in_array($role, $this->roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function administrateur(): Boolean
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Sortie $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->addParticipant($this);
        }

        return $this;
    }

    public function removeParticipation(Sortie $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            $participation->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getOrganisations(): Collection
    {
        return $this->organisations;
    }

    public function addOrganisation(Sortie $organisation): self
    {
        if (!$this->organisations->contains($organisation)) {
            $this->organisations[] = $organisation;
            $organisation->setOrganisateur($this);
        }

        return $this;
    }

    public function removeOrganisation(Sortie $organisation): self
    {
        if ($this->organisations->removeElement($organisation)) {
            // set the owning side to null (unless already changed)
            if ($organisation->getOrganisateur() === $this) {
                $organisation->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        // set the owning side of the relation if necessary
        /*if ($photo->getParticipant() !== $this) {
            $photo->setParticipant($this);
        }*/

        $this->photo = $photo;

        return $this;
    }
}
