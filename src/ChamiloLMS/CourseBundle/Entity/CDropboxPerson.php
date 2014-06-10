<?php

namespace ChamiloLMS\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CDropboxPerson
 *
 * @ORM\Table(name="c_dropbox_person")
 * @ORM\Entity
 */
class CDropboxPerson
{
    /**
     * @var integer
     *
     * @ORM\Column(name="c_id", type="integer", nullable=false)
     */
    private $cId;

    /**
     * @var integer
     *
     * @ORM\Column(name="file_id", type="integer", nullable=false)
     */
    private $fileId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iid;


}
