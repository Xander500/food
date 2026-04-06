<?php
    session_cache_expire(30);
    session_start();
    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    // redirect to index if already logged in
    if (isset($_SESSION['_id'])) {
        header('Location: index.php');
        die();
    }

    require_once('include/input-validation.php');
    require_once('database/dbUsers.php');
    require_once('database/dbinfo.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require_once('email/PHPMailer/PHPMailer/src/Exception.php');
    require_once('email/PHPMailer/PHPMailer/src/PHPMailer.php');
    require_once('email/PHPMailer/PHPMailer/src/SMTP.php');

    $submitted = false;
    $errors    = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $args = sanitize($_POST);

        if (!wereRequiredFieldsSubmitted($args, ['identifier'])) {
            $errors = 'Please enter your username or email address.';
        } else {
            $identifier = strtolower(trim($args['identifier']));

            // try username and then email
            $targetUser = retrieve_user($identifier);
            if (!$targetUser) {
                $con = connect();
                $stmt = $con->prepare("SELECT * FROM dbusers WHERE email = ? LIMIT 1");
                $stmt->bind_param("s", $identifier);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                mysqli_close($con);
                if ($row) {
                    $targetUser = make_a_user($row);
                }
            }

            $submitted = true;

            if ($targetUser) {
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
                $tempPassword = '';
                for ($i = 0; $i < 8; $i++) {
                    $tempPassword .= $chars[random_int(0, strlen($chars) - 1)];
                }

                $hash = password_hash($tempPassword, PASSWORD_BCRYPT);
                $con = connect();
                $stmt = $con->prepare("UPDATE dbusers SET password = ? WHERE id = ?");
                $userId = $targetUser->get_id();
                $stmt->bind_param("ss", $hash, $userId);
                $stmt->execute();
                $stmt->close();
                mysqli_close($con);

                $emailBody =
                    "Hello " . $targetUser->get_first_name() . ",\r\n\r\n"
                    . "A temporary password has been generated for your account:\r\n\r\n"
                    . "    Temporary Password: $tempPassword\r\n\r\n"
                    . "Please log in and change your password immediately. If you did not request this, please contact your administrator.";

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'volunteerimpacttracking@gmail.com';
                    $mail->Password   = 'nhsm asjv npkm jfqq';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('volunteerimpacttracking@gmail.com', 'UMW Alleviating Food Waste');
                    $mail->addAddress($targetUser->get_email());
                    $mail->Subject = 'Temporary Password';
                    $mail->Body    = $emailBody;

                    $mail->send();
                } catch (Exception $e) {
                    error_log('Mailer error for user ' . $targetUser->get_id() . ': ' . $mail->ErrorInfo);
                    die();
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">
        <style>* { font-family: Quicksand, sans-serif; }</style>
        <title>UMW Alleviating Food Waste Volunteer Tracking | Forgot Password</title>
    </head>
    <body>
        <div class="h-screen flex">

            <div class="hidden md:block md:w-1/2 bg-center rounded-r-[50px] bg-[#1F1F21]">
                <img src="images/UMW_campus.jpg" alt="Campus" style="height: 100%;">
            </div>

            <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-white relative">
                <div class="w-2/3 max-w-md flex flex-col items-center">

                    <div class="w-full flex justify-center mb-6">
                        <img src="<?php include('logo.php'); ?>" alt="Logo" class="w-full max-w-xs">
                    </div>

                    <?php if ($submitted): ?>

                        <div class="w-full text-center text-white bg-green-700 p-3 rounded-lg mb-4">
                            If an account matching that username or email exists, a temporary
                            password has been sent. Please check your email and log in.
                        </div>
                        <a href="login.php" class="text-[#759d3d] text-sm hover:underline">← Back to Login</a>

                    <?php else: ?>

                        <p class="text-gray-600 text-sm text-center mb-6">
                            Enter your username or email and we'll send you a temporary password.
                        </p>

                        <form class="w-full" method="post">
                            <?php if ($errors): ?>
                                <span class="text-white bg-red-700 text-center block p-2 rounded-lg mb-4">
                                    <?php echo $errors ?>
                                </span>
                            <?php endif ?>

                            <div class="mb-4">
                                <label class="block text-[#213e57] font-medium mb-2" for="identifier">
                                    Username or Email
                                </label>
                                <input
                                    class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#92c44c]"
                                    type="text"
                                    id="identifier"
                                    name="identifier"
                                    placeholder="Enter your username or email"
                                    required
                                    autocomplete="username"
                                >
                            </div>

                            <div class="flex justify-between items-center mb-4">
                                <a href="login.php" class="text-[#759d3d] text-sm hover:underline">← Back to Login</a>
                            </div>

                            <button class="cursor-pointer w-full bg-[#213e57] hover:bg-[#92c44c] text-white font-semibold py-3 rounded-lg transition duration-300">
                                Send Temporary Password
                            </button>
                        </form>

                    <?php endif ?>

                </div>
            </div>

        </div>
    </body>
</html>
