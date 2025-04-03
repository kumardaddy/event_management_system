<?php
require_once 'db_connect.php';
require_once 'functions.php';

// Initialize variables
$email = "";
$errors = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = sanitize_input($_POST["email"]);
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]) ? true : false;
    
    // Validate email
    if (empty($email)) {
        $errors["email"] = "Email is required";
    } elseif (!is_valid_email($email)) {
        $errors["email"] = "Invalid email format";
    }
    
    // Validate password
    if (empty($password)) {
        $errors["password"] = "Password is required";
    }
    
    // If no errors, check credentials
    if (empty($errors)) {
        try {
            // Get user by email
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user["password"])) {
                    // Set session variables
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["user_name"] = $user["first_name"] . " " . $user["last_name"];
                    
                    // If remember me is checked, set cookie
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie("remember_token", $token, time() + (86400 * 30), "/"); // 30 days
                        
                        // Store token in database (in a real app)
                        // This is simplified for this example
                    }
                    
                    // Redirect to home page
                    header("Location: home.php");
                    exit();
                } else {
                    $errors["login"] = "Invalid email or password";
                }
            } else {
                $errors["login"] = "Invalid email or password";
            }
        } catch(PDOException $e) {
            $errors["db"] = "Login failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Event Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <a href="index.html" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
            <div class="login-info">
                <h1>Welcome back</h1>
                <p>Sign in to your account to continue</p>
                
                <div class="upcoming-events-card">
                    <h3>Upcoming Events</h3>
                    <div class="upcoming-event">
                        <div class="event-date">15</div>
                        <div class="event-details">
                            <h4>Music Festival 2025</h4>
                            <p>Central Park, Chicago</p>
                        </div>
                    </div>
                    <div class="upcoming-event">
                        <div class="event-date">22</div>
                        <div class="event-details">
                            <h4>Tech Conference</h4>
                            <p>Convention Center, Seattle</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="form-card">
                <div class="form-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>
                
                <?php if (isset($errors["login"]) || isset($errors["db"])): ?>
                    <div class="alert alert-error">
                        <?php echo isset($errors["login"]) ? $errors["login"] : $errors["db"]; ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-tabs">
                    <button class="tab-btn active" data-tab="email-tab">Email</button>
                    <button class="tab-btn" data-tab="social-tab">Social</button>
                </div>
                
                <div class="tab-content" id="email-tab">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="login-form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="john.doe@example.com">
                            </div>
                            <?php if (isset($errors["email"])): ?>
                                <span class="error"><?php echo $errors["email"]; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <div class="password-header">
                                <label for="password">Password</label>
                                <a href="#" class="forgot-password">Forgot password?</a>
                            </div>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="********">
                            </div>
                            <?php if (isset($errors["password"])): ?>
                                <span class="error"><?php echo $errors["password"]; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="remember" id="remember">
                                <span class="checkbox-custom"></span>
                                Remember me
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        
                        <p class="form-footer">
                            Don't have an account? <a href="register.php">Create Account</a>
                        </p>
                    </form>
                </div>
                
                <div class="tab-content" id="social-tab" style="display: none;">
                    <div class="social-buttons">
                        <button class="btn btn-social btn-google">
                            <i class="fab fa-google"></i> Sign in with Google
                        </button>
                        <button class="btn btn-social btn-facebook">
                            <i class="fab fa-facebook-f"></i> Sign in with Facebook
                        </button>
                        <button class="btn btn-social btn-github">
                            <i class="fab fa-github"></i> Sign in with GitHub
                        </button>
                        <button class="btn btn-social btn-apple">
                            <i class="fab fa-apple"></i> Sign in with Apple
                        </button>
                    </div>
                    
                    <div class="divider">
                        <span>Or continue with email</span>
                    </div>
                    
                    <p class="form-footer">
                        Don't have an account? <a href="register.php">Create Account</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>