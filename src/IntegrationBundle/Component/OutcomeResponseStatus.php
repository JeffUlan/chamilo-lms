<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\IntegrationBundle\Component;

/**
 * Class OutcomeResponseStatus.
 *
 * @package Chamilo\IntegrationBundle\Component
 */
class OutcomeResponseStatus
{
    const SEVERITY_STATUS = 'status';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_ERROR = 'error';

    const CODEMAJOR_SUCCESS = 'success';
    const CODEMAJOR_PROCESSING = 'processing';
    const CODEMAJOR_FAILURE = 'failure';
    const CODEMAJOR_UNSUPPORTED = 'unsupported';

    /**
     * @var string
     */
    private $codeMajor = '';

    /**
     * @var string
     */
    private $severity = '';

    /**
     * @var string
     */
    private $messageRefIdentifier = '';

    /**
     * @var string
     */
    private $operationRefIdentifier = '';

    /**
     * @var string
     */
    private $description = '';

    /**
     * Get codeMajor.
     *
     * @return string
     */
    public function getCodeMajor()
    {
        return $this->codeMajor;
    }

    /**
     * Set codeMajor.
     *
     * @param string $codeMajor
     *
     * @return OutcomeResponseStatus
     */
    public function setCodeMajor($codeMajor)
    {
        $this->codeMajor = $codeMajor;

        return $this;
    }

    /**
     * Get severity.
     *
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Set severity.
     *
     * @param string $severity
     *
     * @return OutcomeResponseStatus
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * Get messageRefIdentifier.
     *
     * @return int
     */
    public function getMessageRefIdentifier()
    {
        return $this->messageRefIdentifier;
    }

    /**
     * Set messageRefIdentifier.
     *
     * @param int $messageRefIdentifier
     *
     * @return OutcomeResponseStatus
     */
    public function setMessageRefIdentifier($messageRefIdentifier)
    {
        $this->messageRefIdentifier = $messageRefIdentifier;

        return $this;
    }

    /**
     * Get operationRefIdentifier.
     *
     * @return int
     */
    public function getOperationRefIdentifier()
    {
        return $this->operationRefIdentifier;
    }

    /**
     * Set operationRefIdentifier.
     *
     * @param int $operationRefIdentifier
     *
     * @return OutcomeResponseStatus
     */
    public function setOperationRefIdentifier($operationRefIdentifier)
    {
        $this->operationRefIdentifier = $operationRefIdentifier;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return OutcomeResponseStatus
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
