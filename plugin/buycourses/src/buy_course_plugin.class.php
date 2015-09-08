<?php
/* For license terms, see /license.txt */
/**
 * Description of buy_courses_plugin
 * @package chamilo.plugin.buycourses
 * @author Jose Angel Ruiz    <jaruiz@nosolored.com>
 * @author Imanol Losada      <imanol.losada@beeznest.com>
 * @author Alex Aragón      <alex.aragon@beeznest.com>
 */
/**
 * Plugin class for the BuyCourses plugin
 */
class BuyCoursesPlugin extends Plugin
{
    const PRODUCT_TYPE_COURSE = 1;
    const PRODUCT_TYPE_SESSION = 2;
    const PAYMENT_TYPE_PAYPAL = 1;
    const PAYMENT_TYPE_TRANSFER = 2;
    const SALE_STATUS_CANCELED = -1;
    const SALE_STATUS_PENDING = 0;
    const SALE_STATUS_COMPLETED = 1;

    /**
     *
     * @return StaticPlugin
     */
    static function create()
    {
        static $result = null;
        return $result ? $result : $result = new self();
    }

    protected function __construct()
    {
        parent::__construct(
            '1.0',
            'Jose Angel Ruiz - NoSoloRed (original author),
            Francis Gonzales and Yannick Warnier - BeezNest (integration),
            Alex Aragón - BeezNest (Design icons and css styles),
            Imanol Losada - BeezNest (introduction of sessions purchase)',
            array(
                'show_main_menu_tab' => 'boolean',
                'include_sessions' => 'boolean',
                'paypal_enable' => 'boolean',
                'transfer_enable' => 'boolean',
                'unregistered_users_enable' => 'boolean'
            )
        );
    }

    /**
     * This method creates the tables required to this plugin
     */
    function install()
    {
        require_once api_get_path(SYS_PLUGIN_PATH) . 'buycourses/database.php';
    }

    /**
     * This method drops the plugin tables
     */
    function uninstall()
    {
        require_once __DIR__.'/../config.php';
        $tablesToBeDeleted = array(
            TABLE_BUY_SESSION,
            TABLE_BUY_SESSION_COURSE,
            TABLE_BUY_SESSION_TEMPORARY,
            TABLE_BUY_SESSION_SALE,
            TABLE_BUY_COURSE,
            TABLE_BUY_COURSE_COUNTRY,
            TABLE_BUY_COURSE_PAYPAL,
            TABLE_BUY_COURSE_TRANSFER,
            TABLE_BUY_COURSE_TEMPORAL,
            TABLE_BUY_COURSE_SALE
        );
        foreach ($tablesToBeDeleted as $tableToBeDeleted) {
            $table = Database::get_main_table($tableToBeDeleted);
            $sql = "DROP TABLE IF EXISTS $table";
            Database::query($sql);
        }
        $this->manageTab(false);
    }

    /**
     * Get the currency for sales
     * @return array The selected currency. Otherwise return false
     */
    public function getSelectedCurrency()
    {
        return Database::select(
            '*',
            Database::get_main_table(BuyCoursesUtils::TABLE_CURRENCY),
            [
                'where' => ['status = ?' => true]
            ],
            'first'
        );
    }

    /**
     * Get a list of currencies
     * @return array The currencies. Otherwise return false
     */
    public function getCurrencies()
    {
        return Database::select(
            '*',
            Database::get_main_table(BuyCoursesUtils::TABLE_CURRENCY)
        );
    }

    /**
     * Save the selected currency
     * @param int $selectedId The currency Id
     */
    public function selectCurrency($selectedId)
    {
        $currencyTable = Database::get_main_table(
            BuyCoursesUtils::TABLE_CURRENCY
        );

        Database::update(
            $currencyTable,
            ['status' => 0]
        );
        Database::update(
            $currencyTable,
            ['status' => 1],
            ['id = ?' => intval($selectedId)]
        );
    }

    /**
     * Save the PayPal configuration params
     * @param array $params
     * @return int Rows affected. Otherwise return false
     */
    public function savePaypalParams($params)
    {
        return Database::update(
            Database::get_main_table(BuyCoursesUtils::TABLE_PAYPAL),
            [
                'username' => $params['username'],
                'password' => $params['password'],
                'signature' => $params['signature'],
                'sandbox' => isset($params['sandbox'])
            ],
            ['id = ?' => 1]
        );
    }

    /**
     * Gets the stored PayPal params
     * @return array
     */
    public function getPaypalParams()
    {
        return Database::select(
            '*',
            Database::get_main_table(BuyCoursesUtils::TABLE_PAYPAL),
            ['id = ?' => 1],
            'first'
        );
    }

    /**
     * Save a transfer account information
     * @param array $params The transfer account
     * @return int Rows affected. Otherwise return false
     */
    public function saveTransferAccount($params)
    {
        return Database::insert(
            Database::get_main_table(BuyCoursesUtils::TABLE_TRANSFER),
            [
                'name' => $params['tname'],
                'account' => $params['taccount'],
                'swift' => $params['tswift']
            ]
        );
    }

    /**
     * Get a list of transfer accounts
     * @return array
     */
    public function getTransferAccounts()
    {
        return Database::select(
            '*',
            Database::get_main_table(BuyCoursesUtils::TABLE_TRANSFER)
        );
    }

    /**
     * Remove a transfer account
     * @param int $id The transfer account ID
     * @return int Rows affected. Otherwise return false
     */
    public function deleteTransferAccount($id)
    {
        return Database::delete(
            Database::get_main_table(BuyCoursesUtils::TABLE_TRANSFER),
            ['id = ?' => intval($id)]
        );
    }

    /**
     * Filter the registered courses for show in plugin catalog
     * @return array
     */
    private function getCourses()
    {
        $entityManager = Database::getManager();
        $query = $entityManager->createQueryBuilder();
        
        $courses = $query
            ->select('c')
            ->from('ChamiloCoreBundle:Course', 'c')
            ->leftJoin(
                'ChamiloCoreBundle:SessionRelCourse',
                'sc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c = sc.course'
            )
            ->where(
                $query->expr()->isNull('sc.course')
            )
            ->getQuery()
            ->getResult();

        return $courses;
    }

    /**
     * Get the item data
     * @param int $productId The item ID
     * @param int $itemType The item type
     * @return array
     */
    public function getItemByProduct($productId, $itemType)
    {
        $buyItemTable = Database::get_main_table(BuyCoursesUtils::TABLE_ITEM);
        $buyCurrencyTable = Database::get_main_table(BuyCoursesUtils::TABLE_CURRENCY);

        $fakeItemFrom = "
            $buyItemTable i
            INNER JOIN $buyCurrencyTable c
                ON i.currency_id = c.id
        ";

        return Database::select(
            ['i.*', 'c.iso_code'],
            $fakeItemFrom,
            [
                'where' => [
                    'i.product_id = ? AND i.product_type = ?' => [
                        intval($productId),
                        intval($itemType)
                    ]
                ]
            ],
            'first'
        );
    }

    /**
     * List courses details from the configuration page
     * @return array
     */
    public function getCoursesForConfiguration()
    {
        $courses = $this->getCourses();

        if (empty($courses)) {
            return[];
        }

        $configurationCourses = [];
        $currency = $this->getSelectedCurrency();

        foreach ($courses as $course) {
            $courseItem = [
                'course_id' => $course->getId(),
                'course_visual_code' => $course->getVisualCode(),
                'course_code' => $course->getCode(),
                'course_title' => $course->getTitle(),
                'course_visibility' => $course->getVisibility(),
                'visible' => false,
                'currency' =>  empty($currency) ? null : $currency['iso_code'],
                'price' => 0.00
            ];

            $item = $this->getItemByProduct($course->getId(), self::PRODUCT_TYPE_COURSE);

            if ($item !== false) {
                $courseItem['visible'] = true;
                $courseItem['currency'] = $item['iso_code'];
                $courseItem['price'] = $item['price'];
            }

            $configurationCourses[] = $courseItem;
        }

        return $configurationCourses;
    }

    /**
     * List sessions details from the buy-session table and the session table
     * @return array The sessions. Otherwise return false
     */
    public function getSessionsForConfiguration()
    {
        $buyItemTable = Database::get_main_table(BuyCoursesUtils::TABLE_ITEM);
        $buyCurrencyTable = Database::get_main_table(BuyCoursesUtils::TABLE_CURRENCY);

        $auth = new Auth();
        $sessions = $auth->browseSessions();

        $currency = $this->getSelectedCurrency();

        $items = [];

        $fakeItemFrom = "
            $buyItemTable i
            INNER JOIN $buyCurrencyTable c
                ON i.currency_id = c.id
        ";

        foreach ($sessions as $session) {
            $sessionItem = [
                'session_id' => $session->getId(),
                'session_name' => $session->getName(),
                'session_visibility' => $session->getVisibility(),
                'session_display_start_date' => null,
                'session_display_end_date' => null,
                'visible' => false,
                'currency' =>  empty($currency) ? null : $currency['iso_code'],
                'price' => 0.00
            ];

            if (!empty($session->getDisplayStartDate())) {
                $sessionItem['session_display_start_date'] = api_format_date(
                    $session->getDisplayStartDate()->format('Y-m-d h:i:s')
                );
            }

            if (!empty($session->getDisplayEndDate())) {
                $sessionItem['session_display_end_date'] = api_format_date(
                    $session->getDisplayEndDate()->format('Y-m-d h:i:s')
                );
            }

            $item = Database::select(
                ['i.*', 'c.iso_code'],
                $fakeItemFrom,
                [
                    'where' => [
                        'i.product_id = ? AND ' => $session->getId(),
                        'i.product_type = ?' => self::PRODUCT_TYPE_SESSION
                    ]
                ],
                'first'
            );

            if ($item !== false) {
                $sessionItem['visible'] = true;
                $sessionItem['currency'] = $item['iso_code'];
                $sessionItem['price'] = $item['price'];
            }

            $items[] = $sessionItem;
        }

        return $items;
    }

    /**
     * Get the user status for the session
     * @param int $userId The user ID
     * @param \Chamilo\CoreBundle\Entity\Session $session The session
     * @return string
     */
    private function getUserStatusForSession($userId, \Chamilo\CoreBundle\Entity\Session $session)
    {
        if (empty($userId)) {
            return 'NO';
        }

        $entityManager = Database::getManager();
        $scuRepo = $entityManager->getRepository('ChamiloCoreBundle:SessionRelCourseRelUser');

        $buySaleTable = Database::get_main_table(BuyCoursesUtils::TABLE_SALE);

        // Check if user bought the course
        $sale = Database::select(
            'COUNT(1) as qty',
            $buySaleTable,
            [
                'where' => [
                    'user_id = ? AND product_type = ? AND product_id = ?' => [
                        $userId,
                        self::PRODUCT_TYPE_SESSION,
                        $session->getId()
                    ]
                ]
            ],
            'first'
        );

        if ($sale['qty'] > 0) {
            return "TMP";
        }

        // Check if user is already subscribe to session
        $userSubscription = $scuRepo->findBy([
            'session' => $session,
            'user' => $userId
        ]);

        if (!empty($userSubscription)) {
            return 'YES';
        }

        return 'NO';
    }

    /**
     * Lists current user session details, including each session course details
     * @return array
     */
    public function getCatalogSessionList()
    {
        $auth = new Auth();
        $sessions = $auth->browseSessions();

        $entityManager = Database::getManager();
        $scRepo = $entityManager->getRepository('ChamiloCoreBundle:SessionRelCourse');
        $scuRepo = $entityManager->getRepository('ChamiloCoreBundle:SessionRelCourseRelUser');

        $sessionCatalog = array();
        // loop through all sessions
        foreach ($sessions as $session) {
            $sessionCourses = $session->getCourses();

            if (empty($sessionCourses)) {
                continue;
            }

            $item = $this->getItemByProduct($session->getId(), self::PRODUCT_TYPE_SESSION);

            if (empty($item)) {
                continue;
            }

            $sessionData = [
                'id' => $session->getId(),
                'name' => $session->getName(),
                'dates' => SessionManager::parseSessionDates([
                    'display_start_date' => $session->getDisplayStartDate(),
                    'display_end_date' => $session->getDisplayEndDate(),
                    'access_start_date' => $session->getAccessStartDate(),
                    'access_end_date' => $session->getAccessEndDate(),
                    'coach_access_start_date' => $session->getCoachAccessStartDate(),
                    'coach_access_end_date' => $session->getCoachAccessEndDate()
                ]),
                'price' => $item['price'],
                'currency' => $item['iso_code'],
                'courses' => [],
                'enrolled' => $this->getUserStatusForSession(api_get_user_id(), $session)
            ];

            foreach ($sessionCourses as $sessionCourse) {
                $course = $sessionCourse->getCourse();

                $sessionCourseData = [
                    'title' => $course->getTitle(),
                    'coaches' => []
                ];
                
                $userCourseSubscriptions = $session->getUserCourseSubscriptionsByStatus(
                    $course,
                    Chamilo\CoreBundle\Entity\Session::COACH
                );

                foreach ($userCourseSubscriptions as $userCourseSubscription) {
                    $user = $userCourseSubscription->getUser();
                    $sessionCourseData['coaches'][] = $user->getCompleteName();
                }

                $sessionData['courses'][] = $sessionCourseData;
            }

            $sessionCatalog[] = $sessionData;
        }

        return $sessionCatalog;
    }

    /**
     * Get the user status for the course
     * @param int $userId The user Id
     * @param \Chamilo\CoreBundle\Entity\Course $course The course
     * @return string
     */
    private function getUserStatusForCourse($userId, \Chamilo\CoreBundle\Entity\Course $course)
    {
        if (empty($userId)) {
            return 'NO';
        }

        $entityManager = Database::getManager();
        $cuRepo = $entityManager->getRepository('ChamiloCoreBundle:CourseRelUser');

        $buySaleTable = Database::get_main_table(BuyCoursesUtils::TABLE_SALE);

        // Check if user bought the course
        $sale = Database::select(
            'COUNT(1) as qty',
            $buySaleTable,
            [
                'where' => [
                    'user_id = ? AND product_type = ? AND product_id = ?' => [
                        $userId,
                        self::PRODUCT_TYPE_COURSE,
                        $course->getId()
                    ]
                ]
            ],
            'first'
        );

        if ($sale['qty'] > 0) {
            return "TMP";
        }

        // Check if user is already subscribe to course
        $userSubscription = $cuRepo->findBy([
            'course' => $course,
            'user' => $userId
        ]);

        if (!empty($userSubscription)) {
            return 'YES';
        }

        return 'NO';
    }

    /**
     * Lists current user course details
     * @return array
     */
    public function getCatalogCourseList()
    {
        $courses = $this->getCourses();

        if (empty($courses)) {
            return [];
        }

        $courseCatalog = [];

        foreach ($courses as $course) {
            $item = $this->getItemByProduct($course->getId(), self::PRODUCT_TYPE_COURSE);

            if (empty($item)) {
                continue;
            }

            $courseItem = [
                'id' => $course->getId(),
                'title' => $course->getTitle(),
                'code' => $course->getCode(),
                'course_img' => null,
                'price' => $item['price'],
                'currency' => $item['iso_code'],
                'teachers' => [],
                'enrolled' => $this->getUserStatusForCourse(api_get_user_id(), $course)
            ];

            foreach ($course->getTeachers() as $courseUser) {
                $teacher = $courseUser->getUser();
                $courseItem['teachers'][] = $teacher->getCompleteName();
            }

            //check images
            $possiblePath = api_get_path(SYS_COURSE_PATH);
            $possiblePath .= $course->getDirectory();
            $possiblePath .= '/course-pic.png';

            if (file_exists($possiblePath)) {
                $courseItem['course_img'] = api_get_path(WEB_COURSE_PATH)
                    . $course->getDirectory()
                    . '/course-pic.png';
            }

            $courseCatalog[] = $courseItem;
        }

        return $courseCatalog;
    }

    /**
     * Get course info
     * @param int $courseId The course ID
     * @return array
     */
    public function getCourseInfo($courseId)
    {
        $entityManager = Database::getManager();
        $course = $entityManager->find('ChamiloCoreBundle:Course', $courseId);

        if (empty($course)) {
            return [];
        }

        $item = $this->getItemByProduct($course->getId(), self::PRODUCT_TYPE_COURSE);

        if (empty($item)) {
            return [];
        }

        $courseInfo = [
            'id' => $course->getId(),
            'title' => $course->getTitle(),
            'code' => $course->getCode(),
            'visual_code' => $course->getVisualCode(),
            'teachers' => [],
            'price' => $item['price'],
            'currency' => $item['iso_code'],
            'course_img' => null
        ];

        $courseTeachers = $course->getTeachers();

        foreach ($courseTeachers as $teacher) {
            $courseInfo['teachers'][] = $teacher->getUser()->getCompleteName();
        }

        $possiblePath = api_get_path(SYS_COURSE_PATH);
        $possiblePath .= $course->getDirectory();
        $possiblePath .= '/course-pic.png';

        if (file_exists($possiblePath)) {
            $courseInfo['course_img'] = api_get_path(WEB_COURSE_PATH)
                . $course->getDirectory()
                . '/course-pic.png';
        }

        return $courseInfo;
    }

    /**
     * Get session info
     * @param array $sessionId The session ID
     * @return array
     */
    public function getSessionInfo($sessionId) 
    {
        $entityManager = Database::getManager();
        $session = $entityManager->find('ChamiloCoreBundle:Session', $sessionId);

        if (empty($session)) {
            return [];
        }

        $item = $this->getItemByProduct($session->getId(), self::PRODUCT_TYPE_SESSION);

        if (empty($item)) {
            return [];
        }

        $sessionDates = SessionManager::parseSessionDates([
            'display_start_date' => $session->getDisplayStartDate(),
            'display_end_date' => $session->getDisplayEndDate(),
            'access_start_date' => $session->getAccessStartDate(),
            'access_end_date' => $session->getAccessEndDate(),
            'coach_access_start_date' => $session->getCoachAccessStartDate(),
            'coach_access_end_date' => $session->getCoachAccessEndDate()
        ]);

        $sessionInfo = [
            'id' => $session->getId(),
            'name' => $session->getName(),
            'dates' => $sessionDates,
            'courses' => [],
            'price' => $item['price'],
            'currency' => $item['iso_code'],
            'image' => null
        ];

        $fieldValue = new ExtraFieldValue('session');
        $sessionImage = $fieldValue->get_values_by_handler_and_field_variable(
            $session->getId(),
            'image'
        );

        if (!empty($sessionImage)) {
            $sessionInfo['image'] = api_get_path(WEB_UPLOAD_PATH) . $sessionImage['value'];
        }

        $sessionCourses = $session->getCourses();

        foreach ($sessionCourses as $sessionCourse) {
            $course = $sessionCourse->getCourse();

            $sessionCourseData = [
                'title' => $course->getTitle(),
                'coaches' => []
            ];

            $userCourseSubscriptions = $session->getUserCourseSubscriptionsByStatus(
                $course,
                Chamilo\CoreBundle\Entity\Session::COACH
            );

            foreach ($userCourseSubscriptions as $userCourseSubscription) {
                $user = $userCourseSubscription->getUser();
                $sessionCourseData['coaches'][] = $user->getCompleteName();
            }

            $sessionInfo['courses'][] = $sessionCourseData;
        }

        return $sessionInfo;
    }

    /**
     * Get registered item data
     * @param int $itemId The item ID
     * @return array
     */
    public function getItem($itemId)
    {
        return Database::select(
            '*',
            Database::get_main_table(BuyCoursesUtils::TABLE_ITEM),
            [
                'where' => ['id = ?' => intval($itemId)]
            ],
            'first'
        );
    }

    /**
     * Register a sale
     * @param int $itemId The product ID
     * @param int $paymentType The payment type
     * @return boolean
     */
    public function registerSale($itemId, $paymentType)
    {
        if (!in_array($paymentType, [self::PAYMENT_TYPE_PAYPAL, self::PAYMENT_TYPE_TRANSFER])) {
            return false;
        }

        $entityManager = Database::getManager();

        $item = $this->getItem($itemId);

        if (empty($item)) {
            return false;
        }

        if ($item['product_type'] == self::PRODUCT_TYPE_COURSE) {
            $course = $entityManager->find('ChamiloCoreBundle:Course', $item['product_id']);

            if (empty($course)) {
                return false;
            }

            $productName = $course->getTitle();
        } elseif ($item['product_type'] == self::PRODUCT_TYPE_SESSION) {
            $session = $entityManager->find('ChamiloCoreBundle:Session', $item['product_id']);

            if (empty($session)) {
                return false;
            }

            $productName = $session->getName();
        }

        $values = [
            'currency_id' => $item['currency_id'],
            'date' => api_get_utc_datetime(),
            'user_id' => api_get_user_id(),
            'product_type' => $item['product_type'],
            'product_name' => $productName,
            'product_id' => $item['product_id'],
            'price' => $item['price'],
            'status' => self::SALE_STATUS_PENDING,
            'payment_type' => intval($paymentType)
        ];

        return Database::insert(BuyCoursesUtils::TABLE_SALE, $values);
    }

    /**
     * Get sale data by ID
     * @param int $saleId The sale ID
     * @return array
     */
    public function getSale($saleId)
    {
        return Database::select(
            '*',
            Database::get_main_table(BuyCoursesUtils::TABLE_SALE),
            [
                'where' => ['id = ?' => intval($saleId)]
            ],
            'first'
        );
    }

    /**
     * Get currency data by ID
     * @param int $currencyId The currency ID
     * @return array
     */
    public function getCurrency($currencyId)
    {
        return Database::select(
            '*',
            Database::get_main_table(BuyCoursesUtils::TABLE_CURRENCY),
            [
                'where' => ['id = ?' => intval($currencyId)]
            ],
            'first'
        );
    }

    /**
     * Update the sale status
     * @param int $saleId The sale ID
     * @param int $newStatus The new status
     * @return boolean
     */
    private function updateSaleStatus($saleId, $newStatus = self::SALE_STATUS_PENDING)
    {
        $saleTable = Database::get_main_table(BuyCoursesUtils::TABLE_SALE);

        return Database::update(
            $saleTable,
            ['status' => intval($newStatus)],
            ['id = ?' => intval($saleId)]
        );
    }

    /**
     * Complete sale process. Update sale status to completed
     * @param int $saleId The sale ID
     * @return boolean
     */
    public function completeSale($saleId)
    {
        $sale = $this->getSale($saleId);

        if ($sale['status'] == self::SALE_STATUS_COMPLETED) {
            return true;
        }

        $saleIsCompleted = false;

        switch ($sale['product_type']) {
            case self::PRODUCT_TYPE_COURSE:
                $course = api_get_course_info_by_id($sale['product_id']);

                $saleIsCompleted = CourseManager::subscribe_user($sale['user_id'], $course['code']);
                break;
            case self::PRODUCT_TYPE_SESSION:
                SessionManager::suscribe_users_to_session(
                    $sale['product_id'],
                    [$sale['user_id']],
                    api_get_session_visibility($sale['product_id']),
                    false
                );

                $saleIsCompleted = true;
                break;
        }

        if ($saleIsCompleted) {
            $this->updateSaleStatus($sale['id'], self::SALE_STATUS_COMPLETED);
        }

        return $saleIsCompleted;
    }

    /**
     * Update sale status to canceled
     * @param int $saleId The sale ID
     */
    public function cancelSale($saleId)
    {
        $this->updateSaleStatus($saleId, self::SALE_STATUS_CANCELED);
    }

    /**
     * Get payment types
     * @return array
     */
    public function getPaymentTypes()
    {
        return [
            self::PAYMENT_TYPE_PAYPAL => 'PayPal',
            self::PAYMENT_TYPE_TRANSFER => $this->get_lang('BankTransfer')
        ];
    }

    /**
     * Get a list of sales by the status
     * @param type $status
     * @return type
     */
    public function getSaleListByStatus($status = self::SALE_STATUS_PENDING)
    {
        $saleTable = Database::get_main_table(BuyCoursesUtils::TABLE_SALE);
        $currencyTable = Database::get_main_table(BuyCoursesUtils::TABLE_CURRENCY);
        $userTable = Database::get_main_table(TABLE_MAIN_USER);

        $innerJoins = "
            INNER JOIN $currencyTable c
                ON s.currency_id = c.id
            INNER JOIN $userTable u
                ON s.user_id = u.id
        ";

        return Database::select(
            ['c.iso_code', 'u.firstname', 'u.lastname', 's.*'],
            "$saleTable s $innerJoins",
            [
                'where' => ['s.status = ?' => intval($status)],
                'order' => 'id DESC'
            ]
        );
    }

    /**
     * Get the statuses for sales
     * @return array
     */
    public function getSaleStatuses()
    {
        return [
            self::SALE_STATUS_CANCELED => $this->get_lang('SaleCanceled'),
            self::SALE_STATUS_PENDING => $this->get_lang('SalePending'),
            self::SALE_STATUS_COMPLETED => $this->get_lang('SaleCompleted')
        ];
    }

    /**
     * Get the list of product types
     * @return array
     */
    public function getProductTypes()
    {
        return [
            self::PRODUCT_TYPE_COURSE => get_lang('Course'),
            self::PRODUCT_TYPE_SESSION => get_lang('Session')
        ];
    }

}
