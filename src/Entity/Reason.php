<?php

namespace KejawenLab\Application\SemartHris\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use KejawenLab\Application\SemartHris\Component\Reason\Model\ReasonInterface;
use KejawenLab\Application\SemartHris\Component\Reason\Service\ValidateReasonType;
use KejawenLab\Application\SemartHris\Util\StringUtil;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="absent_reasons", indexes={@ORM\Index(name="absent_reasons_idx", columns={"code", "name"})})
 *
 * @ApiResource(
 *     attributes={
 *         "filters"={
 *             "code.search",
 *             "name.search"
 *         },
 *         "normalization_context"={"groups"={"read"}},
 *         "denormalization_context"={"groups"={"write"}}
 *     }
 * )
 *
 * @UniqueEntity("code")
 *
 * @author Muhamad Surya Iksanudin <surya.iksanudin@kejawenlab.id>
 */
class Reason implements ReasonInterface
{
    /**
     * @Groups({"read", "write"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     *
     * @var string
     */
    private $id;

    /**
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=1)
     * @Assert\Length(max=1)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getTypeChoices")
     *
     * @var string
     */
    private $type;

    /**
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=7)
     * @Assert\Length(max=7)
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $code;

    /**
     * @Groups({"read", "write"})
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string) $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return (string) $this->type;
    }

    /**
     * @return string
     */
    public function getTypeText(): string
    {
        return ValidateReasonType::convertToText($this->type);
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        if (!ValidateReasonType::isValidType($type)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not valid type.', $type));
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return (string) $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = StringUtil::uppercase($code);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = StringUtil::uppercase($name);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s: %s - %s', $this->getType(), $this->getCode(), $this->getName());
    }

    /**
     * @return array
     */
    public function getTypeChoices(): array
    {
        return ValidateReasonType::getReasonTypes();
    }
}