<?php
/* For licensing terms, see /license.txt */

/**
 * Class ImsLtiServiceDeleteResponse.
 */
class ImsLtiServiceDeleteResponse extends ImsLtiServiceResponse
{
    /**
     * ImsLtiServiceDeleteResponse constructor.
     *
     * @param ImsLtiServiceResponseStatus $statusInfo
     * @param mixed|null                  $bodyParam
     */
    public function __construct(ImsLtiServiceResponseStatus $statusInfo, $bodyParam = null)
    {
        $statusInfo->setOperationRefIdentifier('deleteResult');

        parent::__construct($statusInfo, $bodyParam);
    }

    /**
     * @param SimpleXMLElement $xmlBody
     */
    protected function generateBody(SimpleXMLElement $xmlBody)
    {
        $xmlBody->addChild('deleteResultResponse');
    }
}
