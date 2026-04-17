<?php
    require_once('domain/User.php');
    require_once('database/dbUsers.php');
    require_once('include/output.php');

    // Required imports for Cleave JS to work
    echo('<script src="https://nosir.github.io/cleave.js/dist/cleave.min.js"></script>');
    echo('<script src="https://nosir.github.io/cleave.js/dist/cleave-phone.i18n.js"></script>');
    $args = sanitize($_GET);
    if ($_SESSION['access_level'] >= 2 && isset($args['id'])) {
        $id = $args['id'];
        $editingSelf = $id == $_SESSION['_id'];
        // Check to see if user is a lower-level manager here
    } else {
        $editingSelf = true;
        $id = $_SESSION['_id'];
    }

    $person = retrieve_user($id);
    if (!$person) {
        echo '<main class="signup-form"><p class="error-toast">That user does not exist.</p></main></body></html>';
        die();
    }
    
?>
<main class="signup-form">
    <?php if (isset($updateSuccess)): ?>
        <?php if ($updateSuccess): ?>
            <div class="happy-toast">Profile updated successfully!</div>
        <?php else: ?>
            <div class="error-toast">An error occurred.</div>
        <?php endif ?>
    <?php endif ?>
    <?php if ($isAdmin): ?>
        <?php if (strtolower($id) == 'vmsroot') : ?>
            <div class="error-toast">The root user profile cannot be modified</div></main></body>
            <?php die() ?>
        <?php elseif (isset($_GET['id']) && $_GET['id'] != $_SESSION['_id']): ?>
            <!-- <a class="button" href="modifyUserRole.php?id=<?php echo htmlspecialchars($_GET['id']) ?>">Modify User Access</a> -->
        <?php endif ?>
    <?php endif ?>
    
    
    <div class="main-content-box">

    <form class="signup-form" method="post">
	<div class="text-center">
          <h2 class="mb-8">Edit Profile</h2>
            <div class="info-box">
              <p>An asterisk (*) indicates a required field.</p>
            </div>
	</div>
        <fieldset class="section-box">
            <h3 class="mt-2" id="login">Login Credentials</h3>
            <label for="username">Username <span>(Cannot be modified.)</span></label>
            <input type="text" id="username" name="username" value="<?php echo hsc($person->get_id()); ?>" disabled>

            <label>Password</label>
            <a class="button-signup" href='changePassword.php' style="color: var(--button-font-color); font-weight: bold; width: 28%;">Change Password</a>
        </fieldset>

        <fieldset class="section-box">
            <h3 class="mt-2" id="personal-info">Account Information</h3>
            <label for="first_name">* First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo hsc($person->get_first_name()); ?>" required placeholder="Enter your first name">

            <label for="last_name">* Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo hsc($person->get_last_name()); ?>" required placeholder="Enter your last name">

            <label for="email">* E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo hsc($person->get_email()); ?>" required placeholder="Enter your e-mail address">
            
            <div class="semester_select">
                <?php
                    $semester = $person->get_semester();
                    list($season, $year) = $parts = preg_split('/\s+/', trim($semester));
                ?>
                <div>
                    <label for="season">* Semester:</label>
                    <select id="season" name="season" required>
                        <option value="Fall" <?php if ($season === "Fall") {echo "selected";}?>>Fall</option>
                        <option value="Spring" <?php if ($season === "Spring") {echo "selected";}?>>Spring</option>
                    </select>
                </div>
                <div>
                    <label for="year">* Year:</label>
                    <select id="year" name="year" required>
                        <?php
                            $currentYear = date("Y");
                            $minYear = 2025;

                            for ($y = $currentYear; $y >= $minYear; $y--) {
                                $selected = ($y === $year) ? "selected" : "";
                                echo "<option value='$y' $selected>$y</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <p>Select the semester you enrolled in the course MKTG 427.</p>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" name="profile-edit-form" value="Update Profile" style="width: 50%; margin: auto; margin-top: +0.5rem;">
            <?php if ($editingSelf): ?>
                <a class="button cancel" href="viewProfile.php" style="width: 50%; margin: auto;">Cancel</a>
            <?php else: ?>
                <a class="button cancel" href="viewProfile.php?id=<?php echo htmlspecialchars($_GET['id']) ?>" style="width: 50%; margin: auto;">Cancel</a>
            <?php endif ?>
        </fieldset>
    </form>
    </div>
</main>
