<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }
    require_once('include/input-validation.php');
    
    $get = sanitize($_GET);

    // Was an ID supplied? If not redirect to dashboard
    if ($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET['id'])) {
        header('Location: index.php');
        die();
    }

    $id = $get['id'];
    // Does the person exist?
    require_once('domain/User.php');
    require_once('database/dbUsers.php');
    $thePerson = retrieve_user($id);
    if (!$thePerson) {
        echo "That user does not exist";
        die();
    }
    
    // Is user authorized to view this page?
    if ($accessLevel < 2) {
        header('Location: index.php');
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        require_once('database/dbMessages.php');
        $post = sanitize($_POST);
        $new_role = $post['s_role'];
        $new_status = $post['s_archival'];
        if (!valueConstrainedTo($new_role, ['Instructor', 'Student'])) {
            echo "Invalid role selected";
            die();
        }
        if (!valueConstrainedTo($new_status, ['1', '0'])) {
            echo "Invalid archival status selected";
            die();
        }
        if (empty($new_role)){
            // echo "No new role selected";
        }else if ($accessLevel >= 3) {

            update_role($id, $new_role);
            $typeChange = true;
            // echo "<meta http-equiv='refresh' content='0'>";
        }
        if ($accessLevel >= 3) {           
            update_user_archival_status($id, $new_status);
            $typeChange = true;
            // echo "<meta http-equiv='refresh' content='0'>";
        }

        
        if (isset($archivedChange) || isset($typeChange)) {
            header('Location: viewProfile.php?editSuccess&id=' . $_GET['id']);
            die();
        }
    }

    // make every submitted field SQL-safe except for password
    $ignoreList = array('password');
    $args = sanitize($_POST);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>UMW Alleviating Food Waste Volunteer Tracking | User Permissions</title>
        <style>
            .modUser{
                display: flex;
                flex-direction: column;
                gap: .5rem;
                padding: 0 0 4rem 0;
            }
            main.user-role {
                gap: 1rem;
                display: flex;
                flex-direction: column;
            }
            @media only screen and (min-width: 1024px) {
                .modUser {
                    width: 100%;
                }
                main.user-role {
                    /* align-items: center; */
                    margin: 0rem 16rem;
                    /* width: 50rem; */
                }
            }
        </style>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Modify Role</h1>
        <main class="user-role">
            <h2>Modify <?php echo $thePerson->get_first_name() . " " . $thePerson->get_last_name(); ?>'s Role</h2>

            <form class="modUser" method="post">
                <?php if (isset($typeChange) || isset($archivedChange) || isset($statusChange)): ?>
                    <div class="happy-toast">User's access is updated.</div>
                <?php endif ?>
                    <?php
                        // Provides drop down of the role types to select and change the role
			//other than the person's current role type is displayed
            if ($accessLevel == 3) {
				$roles = array('Student' => 'Student', 'Instructor' => 'Instructor');
                echo '<label for="role">Change Role</label><select id="role" class="form-select-sm" name="s_role">' ;
                // echo '<option value="" SELECTED></option>' ;
                $currentRole = $thePerson->get_role();
                foreach ($roles as $role => $typename) {
                    if($role != $currentRole) {
                        echo '<option value="'. $role .'">'. $typename .'</option>';
                    } else {
                        echo '<option value="'. $role .'" selected>'. $typename .' (current)</option>';
                    }
                }
                echo '</select>';

                // Drop down to select whether the user is archived or not
				$archivals = array('0' => 'Active', '1' => 'Archived');
                echo '<label for="archival">Change Archival Status</label><select id="archival" class="form-select-sm" name="s_archival">' ;
                // echo '<option value="" SELECTED></option>' ;
                $currentArchival = $thePerson->is_archived();
                foreach ($archivals as $archival => $typename) {
                    if($archival != $currentArchival) {
                        echo '<option value="'. $archival .'">'. $typename .'</option>';
                    } else {
                        echo '<option value="'. $archival .'" selected>'. $typename .' (current)</option>';
                    }
                }
                echo '</select>';
            }
        ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>" style="margin: auto;">
                <input type="submit" name="user_access_modified" value="Update" style="margin: auto;">
                <a class="button cancel" href="personSearch.php" style="margin: auto;">Cancel</a>
		</form>
        </main>
    </body>
</html>
