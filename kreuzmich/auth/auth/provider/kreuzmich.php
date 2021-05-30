<?php
/**
*
* @package phpBB Extension - Kreuzmich Auth
* @copyright (c) 2017 Christian Rubbert, Raphael Menke
* @license http://opensource.org/licenses/BSD-2-Clause BSD 2-Clause License
*
*/
namespace kreuzmich\auth\auth\provider;

use phpbb\request\request_interface;

/**
* Kreuzmich authentication provider for phpBB 3.1
*/
class kreuzmich extends \phpbb\auth\provider\base
{
	/**
	 * phpBB database driver
	 *
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;

	/**
	 * phpBB config
	 *
	 * @var \phpbb\config\config
	 */
	protected $config;

	/**
	 * phpBB request object
	 *
	 * @var \phpbb\request\request
	 */
	protected $request;

  /**
	 * Language object.
	 *
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * auth adapter settings
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Kreuzmich Authentication Constructor
	 *  - called when instance of this class is created
	 *
	 * @param	\phpbb\db\driver\driver_interface	$db					Database object
	 * @param	\phpbb\config\config 				$config				Config object
	 * @param	\phpbb\request\request 				$request			Request object
	 * @param	\phpbb\user 						$user				User object
	 */
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\request\request $request,
		\phpbb\language\language $language
	)
	{
 		$this->db = $db;
		$this->config = $config;
		$this->request = $request;
		$this->language = $language;
		
		$this->settings['http_user'] = (empty($this->config['kreuzmich_user_attribute'])) ? '' : $this->config['kreuzmich_user_attribute'];
		$this->settings['http_password'] = (empty($this->config['kreuzmich_password_attribute'])) ? '' : $this->config['kreuzmich_password_attribute'];
		$this->settings['url'] = (empty($this->config['kreuzmich_url_attribute'])) ? '' : 'https://' . $this->config['kreuzmich_url_attribute'];
		$this->settings['exp_user'] = (empty($this->config['kreuzmich_exp_user_attribute'])) ? 0 : $this->config['kreuzmich_exp_user_attribute'];
		$this->settings['new_user'] = (empty($this->config['kreuzmich_new_user_attribute'])) ? 0 : $this->config['kreuzmich_new_user_attribute'];
	}

	/**
	 * {@inheritdoc}
	 * - called when login form is submitted
	 */
	public function login($username, $password)
	{
	global $phpbb_container;
	
	$this->language->add_lang('kreuzmich', 'kreuzmich/auth');

    // Auth plugins get the password untrimmed.
    // For compatibility we trim() here.
    $password = trim($password);
        
    // do not allow empty password or username
    if (!$password)
    {
      return array(
        'status'    => LOGIN_ERROR_PASSWORD,
        'error_msg' => 'NO_PASSWORD_SUPPLIED',
        'user_row'  => array('user_id' => ANONYMOUS),
      );
    }
    
    if (!$username)
    {
      return array(
        'status'    => LOGIN_ERROR_USERNAME,
        'error_msg' => 'LOGIN_ERROR_USERNAME',
        'user_row'  => array('user_id' => ANONYMOUS),
      );
    }
    // do not allow umlauts in username
    $username_clean = utf8_clean_string($username);

    if (
      preg_match('/[ß]/', $username) == 1 ||
      preg_match('/[äÄöÖüÜ]/', $username_clean) == 1
    )
    {
      return array(
        'status'    => LOGIN_ERROR_USERNAME,
        'error_msg' => 'LOGIN_ERROR_USERNAME_UMLAUT',
        'user_row'  => array('user_id' => ANONYMOUS),
      );
    }
	//setup cURL for ext Auth
    $ch = curl_init($this->settings['url']);

    // HTTP login and password for authentication
    curl_setopt($ch, CURLOPT_USERPWD, $this->settings['http_user'] . ':' . $this->settings['http_password']);
	// login and password from login form
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('username' => $username, 'password' => $password)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $jsondata = @curl_exec($ch);
    $jsonobj = @json_decode($jsondata);

	curl_close($ch);

	
	// erroneous jsonobj
	// if !jsondata, jsonobj empty
	// if Kreuzmich Error 500, jsonobj = all HTML of Error 500 page, but no ->success
	if ( !isset($jsonobj->success) )
    {
		unset($jsonobj);
		return array(
			'status'    => LOGIN_ERROR_USERNAME,
			'error_msg' => 'LOGIN_ERROR_EXTERNAL_AUTH_KREUZMICH',
			'user_row'  => array('user_id' => ANONYMOUS),
      );
    }   
    
	
	// wrong username or password or not unlocked
	// priority over everything else
	if (!$jsonobj->success) 
	{
		unset($jsonobj);
		return array(
				'status'    => LOGIN_ERROR_USERNAME,
				'error_msg'    => 'LOGIN_ERROR_KREUZMICH',
				'user_row'    => array('user_id' => ANONYMOUS),
		);
	}

	
	// right username and password combination of an unlocked account
	
	// deny auth of expired users
	// check ACP settings & JSON attribute 'expired'
	if ( (!$this->settings['exp_user'])  && ($jsonobj->user->expired) )
	{
		unset($jsonobj);
		return array(
			'status'    => LOGIN_ERROR_USERNAME,
			'error_msg'    => 'LOGIN_ERROR_KREUZMICH_EXPIRED',
			'user_row'    => array('user_id' => ANONYMOUS),
		);
	}
		
	// if auth sucessful, find user or create new
    $sql = sprintf('SELECT user_id, username, user_password, user_passchg, user_email, user_type FROM %1$s WHERE username_clean = \'%2$s\'', USERS_TABLE, $this->db->sql_escape($username_clean));
	$result = $this->db->sql_query($sql);
	$row = $this->db->sql_fetchrow($result);
	$this->db->sql_freeresult($result);

	// fill custom profile fields
	$profile_fields = array ( 'pf_vorname' => $jsonobj->user->firstname, 'pf_nachname' => $jsonobj->user->lastname);

	// user exists
	if($row)
	{
		unset($jsonobj);
 		
		// check for inactive users
		if($row['user_type'] == USER_INACTIVE || $row['user_type'] == USER_IGNORE)
		{
			return array(
				'status'	=> LOGIN_ERROR_ACTIVE,
				'error_msg'	=> 'ACTIVE_ERROR',
				'user_row'	=> $row,
			);
		}

		// check if user is in Blocked for Forum group (group id = 47)
			$sql2 = sprintf('SELECT group_id FROM %1$s WHERE group_id = 47 AND user_id = \'%2$s\'', USER_GROUP_TABLE, $row['user_id']);
			$result2 = $this->db->sql_query($sql2);
			$row2 = $this->db->sql_fetchrow($result2);
			$this->db->sql_freeresult($result2);
			if ($row2){
				return array(
					'status'		=> LOGIN_ERROR_ACTIVE,
					'error_msg'		=> 'LOGIN_ERROR_KREUZMICH_BLOCKED',
					'user_row'		=> $row,
				);
			}
		
		// success, user loaded
		
		// update custom profile fields first
		$cp = $phpbb_container->get('profilefields.manager');
		$cp->update_profile_field_data($row['user_id'], $profile_fields);
		
		return array(
			'status'		=> LOGIN_SUCCESS,
			'error_msg'		=> false,
			'user_row'		=> $row,
			);
	} else {
  		// first login 
		// are new users allowed?
		if ($this->settings['new_user']) 
			// create new user
			return array(
				'status'		=> LOGIN_SUCCESS_CREATE_PROFILE,
				'error_msg'		=> false,
				'user_row'		=> $this->newUserRow($jsonobj->user->username, $jsonobj),
				'cp_data' 		=> $profile_fields,
			);
		else // no new users allowed, no login
			return array(
				'status'		=> LOGIN_ERROR_USERNAME,
				'error_msg'		=> 'LOGIN_ERROR_KREUZMICH_NO_NEW_USERS',
				'user_row'		=> array('user_id' => ANONYMOUS),
			);
	}
	
  }

	/**
	 * {@inheritdoc}
	 * - should return custom configuration options
	 */
	public function acp()
	{
		// these are fields in the config for this auth provider
		return array(
			'kreuzmich_user_attribute',
			'kreuzmich_password_attribute',
			'kreuzmich_url_attribute',
			'kreuzmich_exp_user_attribute',
			'kreuzmich_new_user_attribute',
		);
	}

	/**
	 * {@inheritdoc}
	 * - should return configuration options template
	 */
	public function get_acp_template($new_config)
	{
		$this->language->add_lang('kreuzmich', 'kreuzmich/auth');

		return array(
			'TEMPLATE_FILE'	=> '@kreuzmich_auth/auth_provider_kreuzmich.html',
			'TEMPLATE_VARS'	=> array(
				'AUTH_KREUZMICH_USER' => $new_config['kreuzmich_user_attribute'],
				'AUTH_KREUZMICH_PASSWORD' => $new_config['kreuzmich_password_attribute'],
				'AUTH_KREUZMICH_URL' => $new_config['kreuzmich_url_attribute'],
				'AUTH_KREUZMICH_EXP_USER' => $new_config['kreuzmich_exp_user_attribute'],
				'AUTH_KREUZMICH_NEW_USER' => $new_config['kreuzmich_new_user_attribute'],
			),
		);
	}

	/**
	 * This function generates an array which can be passed to the user_add function in order to create a user
	 *
	 * @param 	string	$username 	The username of the new user.
	 * @param 	array	$jsonobj 	The user data array given by Kreuzmich
	 * @return 	array 				Contains data that can be passed directly to the user_add function.
	 */
	private function newUserRow($username, $jsonobj)
	{
		// first retrieve default group id
		$sql = sprintf('SELECT group_id FROM %1$s WHERE group_name = \'%2$s\' AND group_type = \'%3$s\'', GROUPS_TABLE, $this->db->sql_escape('REGISTERED'), GROUP_SPECIAL);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if(!$row)
		{
			trigger_error('NO_GROUP');
		}

		// generate user account data
		return array(
			'username'		=> $username,
			'user_password'		=> '',
			'user_email'		=> $jsonobj->user->email,
			'group_id'		=> (int) $row['group_id'],
			'user_type'		=> USER_NORMAL,
			'user_new'		=> ($this->config['new_member_post_limit']) ? 1 : 0,
		);
	}
}
