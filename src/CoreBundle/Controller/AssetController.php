<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use Chamilo\CoreBundle\Repository\AssetRepository;
use Chamilo\CoreBundle\Traits\ControllerTrait;
use Chamilo\CoreBundle\Traits\CourseControllerTrait;
use Chamilo\CoreBundle\Traits\ResourceControllerTrait;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/assets")
 */
class AssetController
{
    use CourseControllerTrait;
    use ResourceControllerTrait;
    use ControllerTrait;

    /**
     *  @Route("/{category}/{path}", methods={"GET"}, requirements={"path"=".+"})
     */
    public function showFile($category, $path, AssetRepository $assetRepository)
    {
        $filePath = $category.'/'.$path;
        $has = $assetRepository->getFileSystem()->has($filePath);
        if ($has) {
            $fileName = basename($filePath);
            $stream = $assetRepository->getFileSystem()->readStream($filePath);

            $response = new StreamedResponse(
                function () use ($stream): void {
                    stream_copy_to_stream($stream, fopen('php://output', 'wb'));
                }
            );
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $fileName
            );
            $response->headers->set('Content-Disposition', $disposition);
            //$response->headers->set('Content-Type', $mimeType ?: 'application/octet-stream');

            return $response;
        }
    }
}
