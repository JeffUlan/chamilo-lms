<?php
/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Entity\GradebookEvaluation;
use Chamilo\UserBundle\Entity\User;

/**
 * Class ImsLtiReplaceServiceRequest.
 */
class ImsLtiServiceReplaceRequest extends ImsLtiServiceRequest
{
    /**
     * ImsLtiReplaceServiceRequest constructor.
     *
     * @param SimpleXMLElement $xml
     */
    public function __construct(SimpleXMLElement $xml)
    {
        parent::__construct($xml);

        $this->responseType = ImsLtiServiceResponse::TYPE_REPLACE;
        $this->xmlRequest = $this->xmlRequest->replaceResultRequest;
    }

    protected function processBody()
    {
        $resultRecord = $this->xmlRequest->resultRecord;
        $sourcedId = (string) $resultRecord->sourcedGUID->sourcedId;
        $resultScore = (float) $resultRecord->result->resultScore->textString;

        if (0 > $resultScore || 1 < $resultScore) {
            $this->statusInfo
                ->setSeverity(ImsLtiServiceResponseStatus::SEVERITY_WARNING)
                ->setCodeMajor(ImsLtiServiceResponseStatus::CODEMAJOR_FAILURE);

            return;
        }

        $sourcedParts = explode(':', $sourcedId);

        $em = Database::getManager();
        /** @var GradebookEvaluation $evaluation */
        $evaluation = $em->find('ChamiloCoreBundle:GradebookEvaluation', $sourcedParts[0]);
        /** @var User $user */
        $user = $em->find('ChamiloUserBundle:User', $sourcedParts[1]);

        if (empty($evaluation) || empty($user)) {
            $this->statusInfo
                ->setSeverity(ImsLtiServiceResponseStatus::SEVERITY_STATUS)
                ->setCodeMajor(ImsLtiServiceResponseStatus::CODEMAJOR_FAILURE);

            return;
        }

        $score = $evaluation->getMax() * $resultScore;

        $results = Result::load(null, $user->getId(), $evaluation->getId());

        if (empty($results)) {
            $result = new Result();
            $result->set_evaluation_id($evaluation->getId());
            $result->set_user_id($user->getId());
            $result->set_score($score);
            $result->add();
        } else {
            /** @var Result $result */
            $result = $results[0];
            $result->addResultLog($user->getId(), $evaluation->getId());
            $result->set_score($score);
            $result->save();
        }

        $this->statusInfo
            ->setSeverity(ImsLtiServiceResponseStatus::SEVERITY_STATUS)
            ->setCodeMajor(ImsLtiServiceResponseStatus::CODEMAJOR_SUCCESS)
            ->setDescription(
                sprintf(
                    get_plugin_lang('ScoreForXUserIsYScore', 'ImsLtiPlugin'),
                    $user->getId(),
                    $resultScore
                )
            );
    }
}
