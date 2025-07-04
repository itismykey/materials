<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$mysqli = new mysqli("localhost", "root", "usbw", "materials_db");
if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST['login_user'], $_POST['login_pass']) &&
    $_POST['login_user'] === 'admin' &&
    $_POST['login_pass'] === 'admin') {
    $_SESSION['admin'] = true;
}

$admin_mode = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

if ($admin_mode && isset($_POST['add'])) {
    $stmt = $mysqli->prepare("INSERT INTO materials (SampleName, Quantity, Program, Recipient) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $_POST['SampleName'], $_POST['Quantity'], $_POST['Program'], $_POST['Recipient']);
    $stmt->execute();
}

if ($admin_mode && isset($_GET['delete'])) {
    $stmt = $mysqli->prepare("DELETE FROM materials WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
}

// 搜尋處理
$where = array();
$values = array();
$types = '';

if (isset($_GET['search']) && isset($_GET['keyword']) && trim($_GET['keyword']) !== '') {
    $keyword = '%' . trim($_GET['keyword']) . '%';
    $fields = array('SampleName', 'Program', 'Recipient');

    foreach ($fields as $field) {
        if (isset($_GET['field_' . $field])) {
            $where[] = $field . " LIKE ?";
            $values[] = &$keyword;  // 用&來引用變數
            $types .= 's';
        }
    }
}

$query = "SELECT * FROM materials";
if (count($where) > 0) {
    $query .= " WHERE " . implode(" OR ", $where);
}
$query .= " ORDER BY id DESC";

$stmt = $mysqli->prepare($query);
if (count($values) > 0) {
    // 使用call_user_func_array來綁定參數
    call_user_func_array(array($stmt, 'bind_param'), array_merge(array($types), $values));
}

$stmt->execute();

// 確保$stmt是有效對象
if ($stmt) {
    $result = $stmt->get_result();
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $row_count = count($data);
} else {
    die('SQL執行錯誤');
}

?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8" />
    <title>物料管理系統</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f2f2f2; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        .admin-form, .login-form { background: rgba(255,255,255,0.95); padding: 20px; border-radius: 8px; margin-top: 20px; }
        input { padding: 5px; margin: 5px; }
        .btn { padding: 6px 12px; margin: 5px; cursor: pointer; }
        .delete-btn { color: red; }
        .login-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); display: flex; justify-content: center; align-items: center;
        }
        .login-box {
            background: white; padding: 20px; border-radius: 10px; text-align: center;
        }
        .top-bar {
            margin-bottom: 10px;
            text-align: right;
        }
        .search-box {
            background: white;
            padding: 10px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<h2>📦 物料管理系統</h2>

<div class="top-bar">
    <?php if ($admin_mode): ?>
        <form method="post" style="display:inline;">
            <button name="logout" class="btn">🚪 登出</button>
        </form>
    <?php else: ?>
        <button onclick="document.getElementById('loginBox').style.display='flex'" class="btn">🔒 管理模式</button>
    <?php endif; ?>
</div>

<?php if ($admin_mode): ?>
<form method="post" class="admin-form">
    <h3>➕ 新增物料資料</h3>
    SampleName: <input name="SampleName" required />
    Quantity: <input name="Quantity" type="number" required />
    Program: <input name="Program" required />
    Recipient: <input name="Recipient" required />
    <button name="add" class="btn">新增</button>
</form>
<?php endif; ?>

<!-- 🔍 搜尋功能 -->
<form method="get" class="search-box">
    <strong>🔍 搜尋：</strong>
    關鍵字：<input type="text" name="keyword" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" />
    <label><input type="checkbox" name="field_SampleName" <?php if (isset($_GET['field_SampleName'])) echo 'checked'; ?>> SampleName</label>
    <label><input type="checkbox" name="field_Program" <?php if (isset($_GET['field_Program'])) echo 'checked'; ?>> Program</label>
    <label><input type="checkbox" name="field_Recipient" <?php if (isset($_GET['field_Recipient'])) echo 'checked'; ?>> Recipient</label>
    <button class="btn" name="search">搜尋</button>
</form>

<p>資料筆數：<?php echo $row_count; ?></p>

<table>
    <tr>
        <th>ID</th>
        <th>SampleName</th>
        <th>Quantity</th>
        <th>Program</th>
        <th>Recipient</th>
        <?php if ($admin_mode): ?><th>操作</th><?php endif; ?>
    </tr>
    <?php if ($row_count > 0): ?>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['SampleName']); ?></td>
                <td><?php echo $row['Quantity']; ?></td>
                <td><?php echo htmlspecialchars($row['Program']); ?></td>
                <td><?php echo htmlspecialchars($row['Recipient']); ?></td>
                <?php if ($admin_mode): ?>
                    <td><a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('確定要刪除嗎?')">刪除</a></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="<?php echo $admin_mode ? 6 : 5; ?>">查無資料</td></tr>
    <?php endif; ?>
</table>

<!-- 登入視窗 -->
<div id="loginBox" class="login-overlay" style="display:none;">
    <form method="post" class="login-box">
        <h3>🔐 管理登入</h3>
        帳號：<input name="login_user" required /><br /><br />
        密碼：<input type="password" name="login_pass" required /><br /><br />
        <button class="btn">登入</button>
        <button type="button" class="btn" onclick="document.getElementById('loginBox').style.display='none'">取消</button>
    </form>
</div>

</body>
</html>
