<?php
/* For license terms, see /license.txt */

use Chamilo\CoreBundle\Entity\Session;
use Chamilo\UserBundle\Entity\User;
use Chamilo\CoreBundle\Entity\Course;
use Chamilo\PluginBundle\Entity\ImsLti\ImsLtiTool;

require_once __DIR__.'/../../main/inc/global.inc.php';
require './OAuthSimple.php';

api_protect_course_script(false);
api_block_anonymous_users(false);

$em = Database::getManager();

/** @var ImsLtiTool $tool */
$tool = isset($_GET['id']) ? $em->find('ChamiloPluginBundle:ImsLti\ImsLtiTool', intval($_GET['id'])) : 0;

if (!$tool) {
    api_not_allowed(true);
}

/** @var ImsLtiPlugin $imsLtiPlugin */
$imsLtiPlugin = ImsLtiPlugin::create();
/** @var Session $session */
$session = $em->find('ChamiloCoreBundle:Session', api_get_session_id());
/** @var Course $course */
$course = $em->find('ChamiloCoreBundle:Course', api_get_course_int_id());
/** @var User $user */
$user = $em->find('ChamiloUserBundle:User', api_get_user_id());

$siteName = api_get_setting('siteName');
$toolUserId = ImsLtiPlugin::generateToolUserId($user);

$params = [];
$params['lti_version'] = 'LTI-1p0';

if ($tool->isActiveDeepLinking()) {
    $params['lti_message_type'] = 'ContentItemSelectionRequest';
    $params['content_item_return_url'] = api_get_path(WEB_PLUGIN_PATH).'ims_lti/item_return.php';
    $params['accept_media_types'] = '*/*';
    $params['accept_presentation_document_targets'] = 'iframe';
    //$params['accept_unsigned'];
    //$params['accept_multiple'];
    //$params['accept_copy_advice'];
    //$params['auto_create']';
    $params['title'] = $tool->getName();
    $params['text'] = $tool->getDescription();
    $params['data'] = 'tool:'.$tool->getId();
} else {
    $params['lti_message_type'] = 'basic-lti-launch-request';
    $params['resource_link_id'] = $tool->getId();
    $params['resource_link_title'] = $tool->getName();
    $params['resource_link_description'] = $tool->getDescription();
}

$params['user_id'] = ImsLtiPlugin::generateToolUserId($user->getId());
$params['user_image'] = UserManager::getUserPicture($user->getId());
$params['roles'] = ImsLtiPlugin::getUserRoles($user);
$params['lis_person_name_given'] = $user->getFirstname();
$params['lis_person_name_family'] = $user->getLastname();
$params['lis_person_name_full'] = $user->getCompleteName();
$params['lis_person_contact_email_primary'] = $user->getEmail();

if (api_is_allowed_to_edit(false, true)) {
    $params['role_scope_mentor'] = ImsLtiPlugin::getRoleScopeMentor($course, $session);
}

$params['context_id'] = $course->getId();
$params['context_type'] = 'CourseSection';
$params['context_label'] = $course->getCode();
$params['context_title'] = $course->getTitle();
$params['launch_presentation_locale'] = api_get_language_isocode();
$params['launch_presentation_document_target'] = 'iframe';
$params['tool_consumer_info_product_family_code'] = 'Chamilo LMS';
$params['tool_consumer_info_version'] = api_get_version();
$params['tool_consumer_instance_guid'] = str_replace(['https://', 'http://'], '', api_get_setting('InstitutionUrl'));
$params['tool_consumer_instance_name'] = $siteName;
$params['tool_consumer_instance_url'] = api_get_path(WEB_PATH);
$params['tool_consumer_instance_contact_email'] = api_get_setting('emailAdministrator');

$oauth = new OAuthSimple(
    $tool->getConsumerKey(),
    $tool->getSharedSecret()
);
$oauth->setAction('post');
$oauth->setSignatureMethod('HMAC-SHA1');
$oauth->setParameters($params);
$result = $oauth->sign(array(
    'path' => $tool->getLaunchUrl(),
    'parameters' => array(
        'oauth_callback' => 'about:blank'
    )
));
?>
<!DOCTYPE html>
<html>
<head>
    <title>title</title>
</head>
<body>
<form action="<?php echo $tool->getLaunchUrl() ?>" name="ltiLaunchForm" method="post"
      encType="application/x-www-form-urlencoded">
    <?php
    foreach ($result["parameters"] as $key => $values) { //Dump parameters
        echo '<input type="hidden" name="'.$key.'" value="'.$values.'" />';
    }
    ?>
    <input type="submit" value="Press to continue to external tool"/>
</form>

<script language="javascript">
    document.ltiLaunchForm.submit();
</script>
</body>
</html>
