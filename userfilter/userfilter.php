<?php 
/**
 * Plugin Name: User Filter
 * Description: При активации плагина создаются пользователи из CSV файла (users.csv). В плагине реализован шорткод [user_filter].
 * Version: 1.0
 * Author: Mikhail Makarov
 * Author URI: https://nobody.com
*/

function user_filter_scripts() {
  wp_enqueue_style( 'user-filter-styles', '/wp-content/plugins/userfilter/css/styles.css');
  wp_enqueue_script( 'jquery-core', '/wp-includes/js/jquery/jquery.js' );
  wp_enqueue_script( 'fontawesome-icons', 'https://use.fontawesome.com/4e9c856628.js' );
  //wp_enqueue_script( 'user-filter-js', '/wp-content/plugins/userfilter/js/main.js' );
}
add_action( 'wp_enqueue_scripts', 'user_filter_scripts' );

add_shortcode( 'user_filter', 'user_filter_by_parameters' );
function user_filter_by_parameters() 
{
    global $wp_roles;
    $html ='
        <div class="user_filter_container">
            <label>Выберите роль пользователя: </label>
            <select>
    ';
    $html .= '<option value="">All roles</option>';
    if ( !isset( $wp_roles ) ) $wp_roles = new WP_Roles();
    $available_roles_names = $wp_roles->get_names();
    $available_roles_capable = array();
    foreach ($available_roles_names as $role_key => $role_name) { //we iterate all the names
        $html .= '<option value="'.$role_key.'">'.$role_name.'</option>';
    }
    $html .='
            </select>
        </div>
    ';
    $html .=' 
        <div class="user_table_container">
            <table>
                <thead>
                    <tr>
                        <th data-field-type="login">Имя пользователя <i class="fas fa-arrow-up"></i> <i class="fas fa-arrow-down"></i></th>
                        <th data-field-type="email">E-mail <i class="fas fa-arrow-up"></i> <i class="fas fa-arrow-down"></i></th>
                        <th>Роль</th>
                    </tr>
                </thead>
                <tbody>
    ';
    $users = get_users( [
	    'role'         => '',
    ] );
    foreach( $users as $user ){
        $html .= '
            <tr>
                <th>'.$user->data->user_login.'</th>
                <th>'.$user->data->user_email.'</th>
                <th>'.$user->roles[0].'</th>
            </tr>
        ';
    }
    $html .= '
                </tbody>
            </table>
         </div>
    ';
	echo $html;
}

function ajaxorderby() {
    check_ajax_referer( 'user-filter-nonce', 'nonce' );
    global $wp_roles;
    $json = json_decode(str_replace('\"', '"', $_POST['json']));
    $role = $json[0]->role;
    $orderby = $json[0]->orderby;
    $order = $json[0]->order;
    $result = '';
    $users = get_users( [
	    'role'         => $role,
	    'orderby'      => $orderby,
	    'order'        => $order,
    ] );
    $users_pull = '[';
    $count = 0;
    foreach( $users as $user ){
        if ($count > 0) { 
            $users_pull .=  ', '; 
        }
        $users_pull .= '{"login": "'.$user->data->user_login.'", "email": "'.$user->data->user_email.'", "role": "'.$user->roles[0].'" }';
	    $count++;
    }
    $users_pull .= ']';
	$result .= $users_pull;
    echo json_encode(array('status'=>true, 'json'=> $result));
	die();
}

function ajaxfilterrole() {
    check_ajax_referer( 'user-filter-nonce', 'nonce' );
    global $wp_roles;
    $json = json_decode(str_replace('\"', '"', $_POST['json']));
    $role = $json[0]->role;
    $result = '';
    $users = get_users( [
	    'role'         => $role,
    ] );
    $users_pull = '[';
    $count = 0;
    foreach( $users as $user ){
        if ($count > 0) { 
            $users_pull .=  ', '; 
        }
        $users_pull .= '{"login": "'.$user->data->user_login.'", "email": "'.$user->data->user_email.'", "role": "'.$user->roles[0].'" }';
	    $count++;
    }
    $users_pull .= ']';
	$result .= $users_pull;
    echo json_encode(array('status'=>true, 'json'=> $result));
	die();
}

function ajax_user_filter_init(){
    
    wp_register_script('user-filter-js', '/wp-content/plugins/userfilter/js/main.js', array('jquery') );
	wp_enqueue_script('user-filter-js');
	wp_localize_script( 'user-filter-js', 'user_filter_object', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce('user-filter-nonce')
	));
	add_action( 'wp_ajax_nopriv_ajaxfilterrole', 'ajaxfilterrole' );
	add_action( 'wp_ajax_ajaxfilterrole', 'ajaxfilterrole' );
	
	add_action( 'wp_ajax_nopriv_ajaxorderby', 'ajaxorderby' );
	add_action( 'wp_ajax_ajaxorderby', 'ajaxorderby' );
	
	
}
add_action('init', 'ajax_user_filter_init');

function user_filter_install() 
{
    //Import users to WP
    $handle = fopen(ABSPATH.'wp-content/plugins/userfilter/users.csv', 'r');
    
    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        // User data
        $info = array();
	    $info['user_nicename'] = $data[0];
	    $info['nickname'] = $data[0];
	    $info['display_name'] = $data[0];
	    $info['first_name'] = $data[0];
	    $info['user_login'] = $data[0];
	    $info['user_pass'] = 'dsljjEDF878!jjfh';
	    $info['user_email'] = $data[1];
	    $info['role'] = $data[2];
	    // Register the user
	    $user_register = wp_insert_user( $info );
	    if ( is_wp_error($user_register) ){
		    $error  = $user_register->get_error_codes()	;
    		if(in_array('empty_user_login', $error))
        		$result = 'Ошибка'.$user_register->get_error_message('empty_user_login');
        } else {
    		$result = 'Пользователь "'.$data[0].'" зарегистрирован.';
        }
    }
    fclose($handle);
}

function user_filter_uninstall() 
{
    // User list
    $handle = fopen(ABSPATH.'wp-content/plugins/userfilter/users.csv', 'r');
    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        $current_user = get_user_by('email', $data[1]);
        wp_delete_user( $current_user->ID );
    }
    fclose($handle);
}

register_activation_hook( __FILE__, 'user_filter_install');
register_deactivation_hook( __FILE__, 'user_filter_uninstall');