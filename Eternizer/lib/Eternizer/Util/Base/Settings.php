<?php
/**
 * Eternizer.
 *
 * @copyright Michael Ueberschaer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package Eternizer
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Wed Jan 04 16:43:44 CET 2012.
 */

/**
 * Utility base class for settings helper methods.
 */
class Eternizer_Util_Base_Settings extends Zikula_AbstractBase
{
    public function handleModvarsPreSave() {
    	
      	$modvar = Eternizer_Util_Base_Settings::getModvars();

    	if ($modvar['ipsave'] == true) {
    		$ip = System::serverGetVar('REMOTE_ADDR');
    	}
    	
    	$this->setIp($ip);
    	
    	if ($modvar['moderate'] == 'guests') {
    		
    		$userid = $this->getCreatedUserId();
    		if ($userid == 1) {
    		$this->setObj_status('M');
    		}
    		else {
    			$this->setObj_status('A');
    		}
    	}
    	elseif ($modvar['moderate'] == 'all') {
    		
    		$this->setObj_status('M');
    	}
    	
    	return true;
    }
    
    public function handleModvarsPostPersist($args) {
    	
    	$modvar = Eternizer_Util_Base_Settings::getModvars();
    	
    	$userid = $this->getCreatedUserId();
    	
    	$ip = $args['ip'];
    	$text = $args['text'];
    	
    	$host = System::serverGetVar('HTTP_HOST') . '/';
    	$url = $host . ModUtil::url('Eternizer', 'user', 'view');
    	$editurl = $host . ModUtil::url('Eternizer', 'admin', 'edit', array('ot' => 'entry', 'id' => $ip));

    	$toaddress = $modvar['mail'];
    	
    	$messagecontent = array();
    	$messagecontent['toname'] = 'Michael Ueberschaer';
    	$messagecontent['toaddress'] = $toaddress;
    	$messagecontent['subject'] = __('New Entry');
    	$messagecontent['body'] = __('Another entry was created by a user!') . '<br /><br />' .
    	__('Text') . '<br />' . $text . '<br /><br />' . __('Visit our guestbook:') . 
    	'<br />' . '<a href="' . $url . '">'. $url . '</a><br />' . __('Moderate the entry:') . 
    	'<br />' . $editurl;
    	$messagecontent['html'] = true;
    	
        if(!ModUtil::apiFunc('Mailer', 'user', 'sendmessage', $messagecontent)) {
    		LogUtil::registerError(Zikula_Form_AbstractHandler::__('Unable to send message'));
    	}
    	
    	$message = Zikula_Form_AbstractHandler::__('Your entry was saved!');
    	
    	if ($modvar['moderate'] == 'guests') {

    			if ($userid == 1) {			
    				$message = Zikula_Form_AbstractHandler::__('Your entry was saved and must be confirmed by our team');
    			}
    	}
    	elseif ($modvar['moderate'] == 'all') {
    		
    		$message = Zikula_Form_AbstractHandler::__('Your entry was saved and must be confirmed by our team');
    	}
    	
    	LogUtil::registerStatus($message);
    	
    	return true;
    	
    }
    
    public function getModvars() {
    	
    	$modvar['ipsave'] = ModUtil::getVar('Eternizer', 'ipsave');
    	$modvar['moderate'] = ModUtil::getVar('Eternizer', 'moderate');
    	$modvar['mail'] = ModUtil::getVar('Eternizer', 'mail');
    	
    	return $modvar;
    }
}
