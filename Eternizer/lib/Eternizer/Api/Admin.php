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
 * This is the Admin api helper class.
 */
class Eternizer_Api_Admin extends Eternizer_Api_Base_Admin
{
	
    /**
     * get available Admin panel links
     *
     * @return array Array of admin links
     */
    public function getlinks()
    {
        $links = array();

        if (SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_READ)) {
            $links[] = array('url'   => ModUtil::url($this->name, 'user', 'main'),
                'text'  => $this->__('Frontend'),
                'title' => $this->__('Switch to user area.'),
                'class' => 'z-icon-es-home');
        }
        if (SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            $links[] = array('url'   => ModUtil::url($this->name, 'admin', 'view', array('ot' => 'entry')),
                'text'  => $this->__('Entries'),
                'title' => $this->__('Entry list'));
        }
        if (SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            $links[] = array('url'   => ModUtil::url($this->name, 'admin', 'import'),
                'text'  => $this->__('Import'),
                'title' => $this->__('Import old entries and attributes'));
        }
        if (SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            $links[] = array('url'   => ModUtil::url($this->name, 'admin', 'config'),
                'text'  => $this->__('Configuration'),
                'title' => $this->__('Manage settings for this application'));
        }
        return $links;
    }	
	
	
	public function import(array $args) {
		
		//get prefix
        $prefix = $this->serviceManager['prefix'];
        		
        //get host, db, user and pw        		
        $databases = ServiceUtil::getManager()->getArgument('databases');
		$connName = Doctrine_Manager::getInstance()->getCurrentConnection()->getName();
		$host = $databases[$connName]['host'];
		$dbname = $databases[$connName]['dbname'];
		$dbuser = $databases[$connName]['user'];
		$dbpassword = $databases[$connName]['password'];
        		
        // get old table of eternizer
        $table = $prefix . '_eternizer_entry';
        		
     	try {
       		$connect = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpassword);
        	} 
        		
       	catch (PDOException $e) {
       		$this->__('Connection to database failed');
       	}
   
        if($connect) {    
        	// ask the DB for entries in the old eternizer table	  		        		
        	// handle the access to the old eternizer table
			// build sql
			$query = "SELECT * FROM $table";	

        	// prepare the sql query
        	$sql = $connect->query($query);
       		
        	// rule the attribute names
       		if ($args['name'] != '') {
       			$name = $args['name'];
       		}
       		else {
       			$name = '';
       		}      			
       		if ($args['email'] != '') {
       			$email = $args['email'];
       		}
       		else {
       			$email = '';
       		}
       		if ($args['homepage'] != '') {
       			$homepage = $args['homepage'];
       		}
       		else {
       			$homepage = '';
       		}
       		if ($args['location'] != '') {
       			$location = $args['location'];
       		}
       		else {
       			$location = '';
       		}
       		
       		$datas = array();
       		
       		// walk through the result of the old table
       		foreach ($sql as $result) {

       		// set attribute fields
       		$attr_name = '';
       		$attr_email = '';
       		$attr_homepage = '';
       		$attr_location = '';
       		      			
       		if ($args['attributes']) {
       		
       		// select all attributes for the entries
       		// build and execute the select	
       		$query2 = "SELECT * FROM objectdata_attributes WHERE object_id = :id && object_type = 'Eternizer_entry'";
       		$sql2 = $connect->prepare($query2);
       		$sql2->bindParam(':id', $result['pn_id']);
       		       	
       		$sql2->execute();

       			// walk through the attributes for the entry
       			foreach ($sql2 as $result2) {

       				if ($name != '') {
       					if ($result2['attribute_name'] == $name) {
       						$attr_name = $result2['value'];
       					}
       				}
       				if ($email != '') {
       					if ($result2['attribute_name'] == $email) {
       						$attr_email = $result2['value'];
       					}
       				}
       				if ($homepage != '') {
       			    	if ($result2['attribute_name'] == $homepage) {
       						$attr_homepage = $result2['value'];
       					}
       				}
       				if ($location != '') {
       			    	if ($result2['attribute_name'] == $location) {
       						$attr_location = $result2['value'];	
       			    	}
       				}
       			}

       		$sql2 = NULL;
       		}
       			
       		$datas[] =   array(':id' => $result['pn_id'],
       					':ip' => $result['pn_ip'],
       				    ':name' => $attr_name,
       					':email' => $attr_email,
       					':homepage' => $attr_homepage,
       					':location' => $attr_location,
       					':text' => $result['pn_text'],
       					':notes' => $result['pn_comment'],
       					':obj_status' => $result['pn_obj_status'],
       					':createdUserId' => $result['pn_cr_uid'],
       					':updatedUserId' => $result['pn_lu_uid'],
       					':createdDate' => $result['pn_cr_date'],
       					':updatedDate' => $result['pn_lu_date']);		
       		}
       		
       		// clear the result rows
       		$sql = NULL;
       			
       		$query3 = "INSERT INTO eternizer_entry (id, ip, name, email, homepage, location, text, notes, obj_status, createdUserId, updatedUserId, createdDate, updatedDate) VALUES (:id, :ip, :name, :email, :homepage, :location, :text, :notes, :obj_status, :createdUserId, :updatedUserId, :createdDate, :updatedDate)";

       		//$query2 = "INSERT INTO eternizer_entry (ip, name, email, homepage, location, text, notes, obj_status, createdUserId, updatedUserId, createdDate, updatedDate) VALUES ('123456.444', 'Frank', '', '', '', 'Hallo', '', 'A', 2, 2, '', '')";
       		   			
       		$sql3 = $connect->prepare($query3);
    		
			//if ($results) {
        	foreach ($datas as $data) {
        		try {
        			$sql3->execute($data);
        		} catch (Exception $e) {
        			$this->__('Writing datas failed');
        		}       			
       			
        	}
        	
        	$sql3 = NULL;
        	// if checked delete the old table
        	if ($args['oldtable']) {
        		
        	$query4 = "DROP TABLE $table";
        		
        	$sql4 = $connect->prepare($query4);
        	$sql4->execute(); 

        	}
        	
        	$sql4 = NULL;
        	
			// delete the connection
       		$connect = NULL;
       		
			}
			else {
				LogUtil::registerError($this->__('No connection to database'));
			}

		return true;
	}
}
