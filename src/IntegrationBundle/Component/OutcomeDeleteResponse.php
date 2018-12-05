<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\IntegrationBundle\Component;

/**
 * Class OutcomeDeleteResponse.
 *
 * @package Chamilo\IntegrationBundle\Component
 */
class OutcomeDeleteResponse extends OutcomeResponse
{
    /**
     * OutcomeDeleteResponse constructor.
     *
     * @param OutcomeResponseStatus $statusInfo
     * @param mixed|null            $bodyParam
     */
    public function __construct(OutcomeResponseStatus $statusInfo, $bodyParam = null)
    {
        $statusInfo->setOperationRefIdentifier('deleteResult');

        parent::__construct($statusInfo, $bodyParam);
    }

    /**
     * @param \SimpleXMLElement $xmlBody
     */
    protected function generateBody(\SimpleXMLElement $xmlBody)
    {
        $xmlBody->addChild('deleteResultResponse');
    }
}
