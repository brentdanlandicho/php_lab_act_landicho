<?php
require_once 'dbcon.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statement = $conn->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        $statement->bind_param('ssi', $name, $email, $id);
        $statement->execute();
        $statement->close();

        header('Location: index.php?msg=updated');
        exit;
    }

    $error = 'Enter a full name and a valid email address.';
} else {
    $error = '';
}

$statement = $conn->prepare('SELECT id, name, email FROM users WHERE id = ?');
$statement->bind_param('i', $id);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$user) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5">
        <h1 class="mb-4">Edit User</h1>
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="post" action="edit.php?id=<?= (int) $user['id'] ?>" class="row g-3">
            <div class="col-md-5">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="255">
            </div>
            <div class="col-md-5">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="255">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </main>
</body>
</html>