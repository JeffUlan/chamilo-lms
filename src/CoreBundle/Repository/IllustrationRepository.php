<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Repository;

use Chamilo\CoreBundle\Entity\Illustration;
use Chamilo\CoreBundle\Entity\Resource\AbstractResource;
use Chamilo\CoreBundle\Entity\Resource\ResourceFile;
use Chamilo\CoreBundle\Entity\Resource\ResourceNode;
use Chamilo\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class IllustrationRepository.
 */
class IllustrationRepository extends ResourceRepository
{
    /**
     * @param AbstractResource $resource
     * @param User             $user
     * @param UploadedFile     $uploadFile
     *
     * @return ResourceFile
     */
    public function  addIllustration(AbstractResource $resource, User $user, $uploadFile)
    {
        $illustrationNode = $this->getIllustrationNodeFromResource($resource);
        $em = $this->getEntityManager();

        if ($illustrationNode === null) {
            $illustration = new Illustration();
            $em->persist($illustration);
            $illustrationNode = $this->addResourceNode($illustration, $user, $resource);
            $this->addResourceToEveryone($illustrationNode);
        }
        return $this->addFile($illustrationNode, $uploadFile);
    }

    /**
     * @param AbstractResource $resource
     *
     * @return ResourceNode
     */
    public function getIllustrationNodeFromResource(AbstractResource $resource)
    {
        $nodeRepo = $this->getResourceNodeRepository();
        $em = $this->getEntityManager();

        $resourceType = $em->getRepository('ChamiloCoreBundle:Resource\ResourceType')->findOneBy(
            ['name' => $this->getShortClassName()]
        );

        //var_dump($resource->getResourceNode()->getId());exit;

        /** @var ResourceNode $node */
        $node = $nodeRepo->findOneBy(
            ['parent' => $resource->getResourceNode(), 'resourceType' => $resourceType]
        );

        return $node;
    }

    /**
     * @param AbstractResource $resource
     */
    public function deleteIllustration(AbstractResource $resource)
    {
        $node = $this->getIllustrationNodeFromResource($resource);

        if ($node !== null) {
            $this->getEntityManager()->remove($node);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param AbstractResource $resource
     *
     * @return string
     */
    public function getIllustrationUrl(AbstractResource $resource)
    {
        $node = $this->getIllustrationNodeFromResource($resource);

        if ($node !== null) {
            return $this->router->generate(
                'core_tool_resource',
                ['id' => $node->getId(), 'filter' => 'course_picture']
            );
        }

        return '';
    }
}
