<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>

<div class="tab-content active" id="users">
    <div class="search-box">
        <form class="form-comtrol" method="GET" action="/../CareSync-System/controllers/admin/userController.php">
        <input type="hidden" name="action" value="list">
        <input type="text" name="search" class="form-control" 
               placeholder="Search users..." 
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    </div>
</div>