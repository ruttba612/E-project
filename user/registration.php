<?php
session_start();
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=auranest_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    
    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email already registered!";
        } else {
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO customers (name, email, password, total_spent, status) VALUES (?, ?, ?, 0.00, 'active')");
            $stmt->execute([$name, $email, $password]);
            header("Location: login.php?registered=success");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Auranest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #f8e1e9, #f5c6cb);
        }
        .container {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            text-align: center;
            backdrop-filter: blur(8px);
        }
        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #f5c6cb;
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            font-size: 16px;
            background: #fef7f9;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .input-group input:focus {
            outline: none;
            border-color: #f0a8b0;
            box-shadow: 0 0 5px rgba(245, 198, 203, 0.5);
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #f5c6cb;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #f0a8b0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .toggle-link {
            margin-top: 20px;
            font-size: 14px;
            color: #f0a8b0;
            text-decoration: none;
            display: inline-block;
        }
        .toggle-link:hover {
            text-decoration: underline;
            color: #e89ca4;
        }
        .alert {
            background: #fef7f9;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #333;
        }
        @media (max-width: 480px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
            h2 {
                font-size: 24px;
            }
            .input-group input, .btn {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register to Auranest</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success'): ?>
            <div class="alert alert-success">Registration successful! Please login.</div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <label for="register-username">Username</label>
                <input type="text" id="register-username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="input-group">
                <label for="register-email">Email</label>
                <input type="email" id="register-email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label for="register-password">Password</label>
                <input type="password" id="register-password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <a href="login.php" class="toggle-link">Already have an account? Login</a>
    </div>
    <script>
        gsap.from(".container", { opacity: 0, y: 50, duration: 1, ease: "power2.out" });
        gsap.from(".input-group", { opacity: 0, x: -20, duration: 0.8, stagger: 0.2, ease: "power2.out" });
        gsap.from(".btn", {
            opacity: 0,
            scale: 0.9,
            duration: 0.5,
            ease: "elastic.out(1, 0.3)",
            onComplete: () => {
                gsap.to(".btn", {
                    scale: 1.05,
                    duration: 0.3,
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut",
                    paused: true,
                    onStart: function() {
                        this._targets[0].addEventListener("mouseenter", () => this.play());
                        this._targets[0].addEventListener("mouseleave", () => this.pause());
                    }
                });
            }
        });
        <?php if (isset($error)): ?>
        gsap.to(".container", { x: -10, duration: 0.1, repeat: 3, yoyo: true, ease: "power1.inOut" });
        <?php endif; ?>
    </script>
</body>
</html>