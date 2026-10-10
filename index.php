<?php
require_once 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statement = $conn->prepare('INSERT INTO users (name, email) VALUES (?, ?)');
        $statement->bind_param('ss', $name, $email);
        $statement->execute();
        $statement->close();

        header('Location: index.php?msg=added');
        exit;
    }

    header('Location: index.php?msg=invalid');
    exit;
}

$users = $conn->query('SELECT * FROM users ORDER BY id DESC');
$messages = [
    'added' => ['success', 'User added successfully.'],
    'updated' => ['success', 'User updated successfully.'],
    'deleted' => ['success', 'User deleted successfully.'],
    'invalid' => ['danger', 'Enter a full name and a valid email address.'],
];
$message = $messages[$_GET['msg'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5">
        <h1 class="mb-4">User Management System</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($message[0], ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message[1], ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="mb-5" aria-labelledby="add-user-heading">
            <h2 id="add-user-heading" class="h4">Add User</h2>
            <form method="post" action="index.php" class="row g-3">
                <div class="col-md-5">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required maxlength="255">
                </div>
                <div class="col-md-5">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required maxlength="255">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </div>
            </form>
        </section>

        <section aria-labelledby="users-heading">
            <h2 id="users-heading" class="h4 mb-3">Registered Users</h2>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Joined</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users->num_rows === 0): ?>
                            <tr><td colspan="5" class="text-center">No users found.</td></tr>
                        <?php else: ?>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?= (int) $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-nowrap">
                                        <a href="edit.php?id=<?= (int) $user['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="delete.php?id=<?= (int) $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>