<?php

namespace App\Entity\CreditCard;

use App\Util\TimestampAbleEntity;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreditCard\CreditCardConsumeRepository")
 */
class CreditCardConsume
{
    const STATUS_CREATED = 0;
    const STATUS_PAYING = 1;
    const STATUS_MORA = 2;
    const STATUS_PAYED = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CreditCard\CreditCardUser", inversedBy="creditCardConsume")
     * @return CreditCardUser
     */
    private $creditCardUser;

    /**
     * @ORM\Column(type="string", nullable=true)
     * */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="smallint")
     */
    private $dues;

    /**
     * @ORM\Column(type="float", options={"default"=0})
     * */
    private $amountPayed = 0;

    /**
     * @ORM\Column(type="smallint", options={"default"=0})
     * */
    private $duesPayed = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $interest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;


    /**
     * @ORM\Column(type="date")
     */
    private $consume_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CreditCard\CreditCard", inversedBy="creditCardConsumes")
     * @ORM\JoinColumn(nullable=false)
     * @return CreditCard $creditCard
     */
    private $creditCard;

    /**
     * @ORM\OneToMany(targetEntity="CreditCardPayment", mappedBy="creditConsume")
     */
    private $payments;

    /**
     * @ORM\Column(type="string", nullable=true)
     * */
    private $monthFirstPay;

    use TimestampAbleEntity;

    /**
     * CreditCardConsume constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->status = self::STATUS_CREATED;
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreditCardUser(): ?CreditCardUser
    {
        return $this->creditCardUser;
    }

    public function setCreditCardUser(?CreditCardUser $creditCardUser): self
    {
        $this->creditCardUser = $creditCardUser;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDues(): ?int
    {
        return $this->dues;
    }

    public function setDues(int $dues): self
    {
        $this->dues = $dues;

        return $this;
    }

    public function getInterest(): ?float
    {
        return $this->interest;
    }

    public function setInterest(float $interest): self
    {
        $this->interest = $interest;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getConsumeAt(): ?DateTimeInterface
    {
        return $this->consume_at;
    }

    public function setConsumeAt(DateTimeInterface $consume_at): self
    {
        $this->consume_at = $consume_at;

        return $this;
    }

    public function getCreditCard(): ?CreditCard
    {
        return $this->creditCard;
    }

    public function setCreditCard(?CreditCard $creditCard): self
    {
        $this->creditCard = $creditCard;

        return $this;
    }

    /**
     * @return Collection|CreditCardPayment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(CreditCardPayment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;

            $this->addAmountPayed($payment->getTotalAmount());

            if ($payment->isLegalDue()) {
                $this->addDuePayed();
            }

            $this->changeStatusToPayed();
        }

        return $this;
    }

    public function removePayment(CreditCardPayment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getCreditConsume() === $this) {
                $payment->setCreditConsume(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function activatePayment()
    {
        $this->status = self::STATUS_PAYING;
    }

    /**
     * TODO: VER SI ESTO TIENE SENTIDO
     * @return mixed
     */
    public function getMonthFirstPay()
    {
        return $this->monthFirstPay;
    }

    /**
     * @param mixed $monthFirstPay
     */
    public function setMonthFirstPay($monthFirstPay): void
    {
        $this->monthFirstPay = $monthFirstPay;
    }

    public function getDuesPayed(): ?int
    {
        return $this->duesPayed ?? 0;
    }

    public function addDuePayed(): self
    {
        $this->duesPayed++;

        return $this;
    }

    public function getAmountPayed(): ?float
    {
        return $this->amountPayed;
    }

    public function addAmountPayed(?float $amountPayed): self
    {
        $this->amountPayed += $amountPayed;

        return $this;
    }

    public function hasPayments()
    {
        return $this->payments->count() > 0;
    }

    public function changeStatusToPayed(): self
    {
        if ($this->amount - $this->amountPayed <= 0) {
            $this->status = self::STATUS_PAYED;
        }

        return $this;
    }
}
