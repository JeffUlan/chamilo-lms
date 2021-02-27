<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * BranchTransaction.
 *
 * @ORM\Table(name="branch_transaction")
 * @ORM\Entity
 */
class BranchTransaction
{
    /**
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="BranchTransactionStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected Room $branchTransactionStatus;

    /**
     * @ORM\Column(name="transaction_id", type="bigint")
     */
    protected int $externalTransactionId;

    /**
     * @ORM\Column(name="action", type="string", length=20, nullable=true, unique=false)
     */
    protected ?string $action;

    /**
     * @ORM\Column(name="item_id", type="string", length=255, nullable=true, unique=false)
     */
    protected ?string $itemId;

    /**
     * @ORM\Column(name="origin", type="string", length=255, nullable=true, unique=false)
     */
    protected ?string $origin;

    /**
     * @ORM\Column(name="dest_id", type="string", length=255, nullable=true, unique=false)
     */
    protected ?string $destId;

    /**
     * @ORM\Column(name="external_info", type="string", length=255, nullable=true, unique=false)
     */
    protected ?string $externalInfo;

    /**
     * @ORM\Column(name="time_insert", type="datetime")
     */
    protected DateTime $timeInsert;

    /**
     * @ORM\Column(name="time_update", type="datetime")
     */
    protected DateTime $timeUpdate;

    /**
     * @ORM\Column(name="failed_attempts", type="integer")
     */
    protected int $failedAttempts;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\BranchSync")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     */
    protected BranchSync $branch;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set action.
     *
     * @param string $action
     */
    public function setAction($action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set itemId.
     *
     * @param string $itemId
     *
     * @return BranchTransaction
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId.
     *
     * @return string
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set destId.
     *
     * @param string $destId
     *
     * @return BranchTransaction
     */
    public function setDestId($destId)
    {
        $this->destId = $destId;

        return $this;
    }

    /**
     * Get destId.
     *
     * @return string
     */
    public function getDestId()
    {
        return $this->destId;
    }

    /**
     * Set timeInsert.
     *
     * @param DateTime $timeInsert
     *
     * @return BranchTransaction
     */
    public function setTimeInsert($timeInsert)
    {
        $this->timeInsert = $timeInsert;

        return $this;
    }

    /**
     * Get timeInsert.
     *
     * @return DateTime
     */
    public function getTimeInsert()
    {
        return $this->timeInsert;
    }

    /**
     * Set timeUpdate.
     *
     * @param DateTime $timeUpdate
     *
     * @return BranchTransaction
     */
    public function setTimeUpdate($timeUpdate)
    {
        $this->timeUpdate = $timeUpdate;

        return $this;
    }

    /**
     * Get timeUpdate.
     *
     * @return DateTime
     */
    public function getTimeUpdate()
    {
        return $this->timeUpdate;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     *
     * @return BranchTransaction
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalInfo()
    {
        return $this->externalInfo;
    }

    /**
     * @param string $externalInfo
     *
     * @return BranchTransaction
     */
    public function setExternalInfo($externalInfo)
    {
        $this->externalInfo = $externalInfo;

        return $this;
    }

    /**
     * @return int
     */
    public function getFailedAttempts()
    {
        return $this->failedAttempts;
    }

    /**
     * @param int $failedAttempts
     *
     * @return BranchTransaction
     */
    public function setFailedAttempts($failedAttempts)
    {
        $this->failedAttempts = $failedAttempts;

        return $this;
    }

    /**
     * @return BranchSync
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param BranchSync $branch
     *
     * @return $this
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * @return Room
     */
    public function getBranchTransactionStatus()
    {
        return $this->branchTransactionStatus;
    }

    /**
     * @param Room $branchTransactionStatus
     *
     * @return BranchTransaction
     */
    public function setBranchTransactionStatus($branchTransactionStatus)
    {
        $this->branchTransactionStatus = $branchTransactionStatus;

        return $this;
    }

    /**
     * @return int
     */
    public function getExternalTransactionId()
    {
        return $this->externalTransactionId;
    }

    /**
     * @param int $externalTransactionId
     *
     * @return BranchTransaction
     */
    public function setExternalTransactionId($externalTransactionId)
    {
        $this->externalTransactionId = $externalTransactionId;

        return $this;
    }
}
