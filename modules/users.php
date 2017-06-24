<?php
require_once (dirname(dirname(__FILE__)) . "/functions.php");
protectFile(basename(__FILE__));

switch ($screen) {
    case 'something1':
        echo "string";
    break;
	case 'something2':
        //select
        $users = new users();
        $users->selectAll($users);
        $res = $users->returnData();
        /*----------------------------------------------------------------------------*/
        //create
        $users = new users(array(
            "fullname" => $fullname,
            "username" => $username,
            "password" => $password,
            "email" => $email
        ));
        //verify register
        if ($users->existRegister('username', $username)) {
            printMSG('exist', 'warning');
            $duplicate = TRUE;
        }
        if ($duplicate != TRUE) {
            $users->insert($users);
            if ($users->lines == 1) {
                printMSG('ok', 'success');
            } else {
                printMSG('error', 'danger');
            }
        }
        /*----------------------------------------------------------------------------*/
        //update
        $id=$_GET['id'];
        $users = new users(array(
            "fullname" => $fullname,
            "username" => $username,
            "password" => $password,
            "email" => $email
        ));
        $users->valuepk = $id;
        $users->extra_select = "WHERE id_category=$id";
        $users->selectAll($users);
        $res = $users->returnData();

        $users->update($users);
        if ($users->lines == 1) {
            printMSG('ok', 'success');
        } else {
            printMSG('error', 'danger');
        }
        /*----------------------------------------------------------------------------*/
        $users = new users();
        $users->valuepk = $id;
        $users->delete($users);
        if ($users->lines == 1) {
            printMSG('ok', 'success');
        } else {
            printMSG('error', 'danger');
        }
        /*----------------------------------------------------------------------------*/
	break;
	default:
	   send_404();
	break;
}
?>
