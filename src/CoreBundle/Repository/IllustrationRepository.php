<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Repository;

use APY\DataGridBundle\Grid\Column\Column;
use APY\DataGridBundle\Grid\Grid;
use Chamilo\CoreBundle\Component\Utils\ResourceSettings;
use Chamilo\CoreBundle\Entity\Illustration;
use Chamilo\CoreBundle\Entity\Resource\AbstractResource;
use Chamilo\CoreBundle\Entity\Resource\ResourceFile;
use Chamilo\CoreBundle\Entity\Resource\ResourceNode;
use Chamilo\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class IllustrationRepository.
 */
final class IllustrationRepository extends ResourceRepository implements ResourceRepositoryInterface
{
    public function saveUpload(UploadedFile $file)
    {

        /** @var Illustration $resource */
        $resource = $this->create();
        $resource->setName($file->getClientOriginalName());

        return $resource;
    }

    public function saveResource(FormInterface $form, $course, $session, $fileType)
    {
        $newResource = $form->getData();
        $newResource
            //->setCourse($course)
            //->setSession($session)
            //->setFiletype($fileType)
            //->setTitle($title) // already added in $form->getData()
        ;

        return $newResource;
    }

    /**
     * @param $uploadFile
     */
    public function addIllustration(AbstractResource $resource, User $user, $uploadFile): ?ResourceFile
    {
        if (null === $uploadFile) {
            return null;
        }

        $illustrationNode = $this->getIllustrationNodeFromParent($resource->getResourceNode());
        $em = $this->getEntityManager();

        if ($illustrationNode === null) {
            $illustration = new Illustration();
            $em->persist($illustration);
            $this->addResourceNode($illustration, $user, $resource);
        } else {
            $illustration = $this->repository->findOneBy(['resourceNode' => $illustrationNode]);
        }

        //$this->addResourceToEveryone($illustrationNode);
        return $this->addFile($illustration, $uploadFile);
    }

    public function addIllustrationToUser(User $user, $uploadFile): ?ResourceFile
    {
        if (null === $uploadFile) {
            return null;
        }

        $illustrationNode = $this->getIllustrationNodeFromParent($user->getResourceNode());
        $em = $this->getEntityManager();

        if ($illustrationNode === null) {
            $illustration = new Illustration();
            $em->persist($illustration);
            $this->createNodeForResource($illustration, $user, $user->getResourceNode());
        } else {
            $illustration = $this->repository->findOneBy(['resourceNode' => $illustrationNode]);
        }

        //$this->addResourceToEveryone($illustrationNode);
        return $this->addFile($illustration, $uploadFile);
    }

    public function getIllustrationNodeFromParent(ResourceNode $resourceNode): ?ResourceNode
    {
        $nodeRepo = $this->getResourceNodeRepository();
        $resourceType = $this->getResourceType();

        /** @var ResourceNode $node */
        $node = $nodeRepo->findOneBy(
            ['parent' => $resourceNode, 'resourceType' => $resourceType]
        );

        return $node;
    }

    public function deleteIllustration(AbstractResource $resource)
    {
        $node = $this->getIllustrationNodeFromParent($resource->getResourceNode());

        if ($node !== null) {
            $this->getEntityManager()->remove($node);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $filter See: services.yaml parameter "glide_media_filters" to see the list of filters.
     */
    public function getIllustrationUrl(AbstractResource $resource, $filter = ''): string
    {
        return $this->getIllustrationUrlFromNode($resource->getResourceNode(), $filter);
    }

    public function getIllustrationUrlFromNode(ResourceNode $resourceNode, $filter = ''): string
    {
        $node = $this->getIllustrationNodeFromParent($resourceNode);

        if ($node !== null) {
            $params = [
                'id' => $node->getId(),
                'tool' => $node->getResourceType()->getTool(),
                'type' => $node->getResourceType()->getName(),
            ];
            if (!empty($filter)) {
                $params['filter'] = $filter;
            }

            return $this->getRouter()->generate(
                'chamilo_core_resource_view',
                $params
            );
        }

        return '';
    }

    public function getTitleColumn(Grid $grid): Column
    {
        return $grid->getColumn('name');
    }
}
