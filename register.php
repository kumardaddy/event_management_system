<?php
require_once 'db_connect.php';
require_once 'functions.php';

// Initialize variables
$first_name = $last_name = $contact_number = $email = $password = $confirm_password = $event_interest = "";
$errors = [];
$success_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = sanitize_input($_POST["first_name"]);
    $last_name = sanitize_input($_POST["last_name"]);
    $contact_number = sanitize_input($_POST["contact_number"]);
    $email = sanitize_input($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $event_interest = sanitize_input($_POST["event_interest"]);
    
    // Validate first name
    if (empty($first_name)) {
        $errors["first_name"] = "First name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $first_name)) {
        $errors["first_name"] = "Only letters and white space allowed";
    }
    
    // Validate last name
    if (empty($last_name)) {
        $errors["last_name"] = "Last name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $last_name)) {
        $errors["last_name"] = "Only letters and white space allowed";
    }
    
    // Validate contact number
    if (empty($contact_number)) {
        $errors["contact_number"] = "Contact number is required";
    } elseif (!preg_match("/^[0-9]{10,}$/", $contact_number)) {
        $errors["contact_number"] = "Contact number must be at least 10 digits";
    }
    
    // Validate email
    if (empty($email)) {
        $errors["email"] = "Email is required";
    } elseif (!is_valid_email($email)) {
        $errors["email"] = "Invalid email format";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $errors["email"] = "Email already exists";
        }
    }
    
    // Validate password
    if (empty($password)) {
        $errors["password"] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors["password"] = "Password must be at least 8 characters";
    }
    
    // Validate confirm password
    if ($password !== $confirm_password) {
        $errors["confirm_password"] = "Passwords do not match";
    }
    
    // Validate event interest
    if (empty($event_interest)) {
        $errors["event_interest"] = "Please select an event interest";
    }
    
    // If no errors, insert user into database
    if (empty($errors)) {
        try {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, contact_number, email, password, event_interest) VALUES (:first_name, :last_name, :contact_number, :email, :password, :event_interest)");
            
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':event_interest', $event_interest);
            
            $stmt->execute();
            
            $success_message = "Registration successful! You can now <a href='login.php'>login</a>.";
            
            // Clear form data
            $first_name = $last_name = $contact_number = $email = $password = $confirm_password = $event_interest = "";
        } catch(PDOException $e) {
            $errors["db"] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="register-container">
        <div class="register-left">
            <a href="index.html" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
            <div class="register-info">
                <h1>Create your account</h1>
                <p>Join our community of event enthusiasts and organizers</p>
                
                <div class="benefits-card">
                    <h3>Why join our platform?</h3>
                    <ul class="benefits-list">
                        <li>
                            <div class="benefit-icon"><i class="far fa-calendar-alt"></i></div>
                            <span>Discover and join exciting events in your area</span>
                        </li>
                        <li>
                            <div class="benefit-icon"><i class="fas fa-user"></i></div>
                            <span>Connect with like-minded individuals</span>
                        </li>
                        <li>
                            <div class="benefit-icon"><i class="far fa-calendar-plus"></i></div>
                            <span>Create and manage your own events</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="register-right">
            <div class="form-card">
                <div class="form-header">
                    <h2>Sign Up</h2>
                    <p>Create an account to get started</p>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors["db"])): ?>
                    <div class="alert alert-error">
                        <?php echo $errors["db"]; ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-tabs">
                    <button class="tab-btn active" data-tab="email-tab">Email</button>
                    <button class="tab-btn" data-tab="social-tab">Social</button>
                </div>
                
                <div class="tab-content" id="email-tab">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="register-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" placeholder="John">
                                </div>
                                <?php if (isset($errors["first_name"])): ?>
                                    <span class="error"><?php echo $errors["first_name"]; ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" placeholder="Doe">
                                </div>
                                <?php if (isset($errors["last_name"])): ?>
                                    <span class="error"><?php echo $errors["last_name"]; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <input type="text" id="contact_number" name="contact_number" value="<?php echo $contact_number; ?>" placeholder="1234567890">
                            </div>
                            <?php if (isset($errors["contact_number"])): ?>
                                <span class="error"><?php echo $errors["contact_number"]; ?></span>
                            <?php endif; ?>
                        </div>
                        
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
                            <label for="password">Password</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="********">
                            </div>
                            <?php if (isset($errors["password"])): ?>
                                <span class="error"><?php echo $errors["password"]; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="********">
                            </div>
                            <?php if (isset($errors["confirm_password"])): ?>
                                <span class="error"><?php echo $errors["confirm_password"]; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="event_interest">Event Interest</label>
                            <select id="event_interest" name="event_interest">
                                <option value="" disabled selected>Select an event</option>
                                <option value="dance" <?php if ($event_interest == "dance") echo "selected"; ?>>Dance</option>
                                <option value="music" <?php if ($event_interest == "music") echo "selected"; ?>>Music</option>
                                <option value="poetry" <?php if ($event_interest == "poetry") echo "selected"; ?>>Poetry</option>
                                <option value="art" <?php if ($event_interest == "art") echo "selected"; ?>>Art</option>
                                <option value="tech" <?php if ($event_interest == "tech") echo "selected"; ?>>Technology</option>
                                <option value="food" <?php if ($event_interest == "food") echo "selected"; ?>>Food</option>
                                <option value="sports" <?php if ($event_interest == "sports") echo "selected"; ?>>Sports</option>
                                <option value="fashion" <?php if ($event_interest == "fashion") echo "selected"; ?>>Fashion</option>
                                <option value="literature" <?php if ($event_interest == "literature") echo "selected"; ?>>Literature</option>
                                <option value="photography" <?php if ($event_interest == "photography") echo "selected"; ?>>Photography</option>
                            </select>
                            <?php if (isset($errors["event_interest"])): ?>
                                <span class="error"><?php echo $errors["event_interest"]; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                        
                        <p class="form-footer">
                            Already have an account? <a href="login.php">Sign In</a>
                        </p>
                    </form>
                </div>
                
                <div class="tab-content" id="social-tab" style="display: none;">
                    <div class="social-buttons">
                        <button class="btn btn-social btn-google">
                            <i class="fab fa-google"></i> Continue with Google
                        </button>
                        <button class="btn btn-social btn-facebook">
                            <i class="fab fa-facebook-f"></i> Continue with Facebook
                        </button>
                        <button class="btn btn-social btn-github">
                            <i class="fab fa-github"></i> Continue with GitHub
                        </button>
                        <button class="btn btn-social btn-apple">
                            <i class="fab fa-apple"></i> Continue with Apple
                        </button>
                    </div>
                    
                    <div class="divider">
                        <span>Or continue with email</span>
                    </div>
                    
                    <p class="form-footer">
                        Already have an account? <a href="login.php">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>