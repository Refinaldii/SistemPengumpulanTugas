class PraktikumController {
    private $model;

    public function __construct() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'asisten') {
            header('Location: index.php?route=auth/login'); exit;
        }
        require_once __DIR__ . '/../models/Praktikum.php';
        $this->model = new Praktikum();
    }

    public function index() {
        $praktika = $this->model->getAll();
        require __DIR__ . '/../views/asisten/praktikum/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $desc  = trim($_POST['description']);
            $this->model->create($title, $desc, $_SESSION['user']['id']);
            header('Location: index.php?route=praktikum/index'); exit;
        }
        require __DIR__ . '/../views/asisten/praktikum/create.php';
    }

    public function edit() {
        $id = intval($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $desc  = trim($_POST['description']);
            $this->model->update($id, $title, $desc);
            header('Location: index.php?route=praktikum/index'); exit;
        }
        $praktikum = $this->model->getById($id);
        require __DIR__ . '/../views/asisten/praktikum/edit.php';
    }

    public function delete() {
        $id = intval($_GET['id']);
        $this->model->delete($id);
        header('Location: index.php?route=praktikum/index'); exit;
    }
}

// models/Praktikum.php
class Praktikum {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config.php';
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT p.*, u.full_name AS asisten FROM praktikum p JOIN users u ON p.created_by = u.id ORDER BY p.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM praktikum WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($title, $desc, $by) {
        $stmt = $this->db->prepare("INSERT INTO praktikum (title, description, created_by) VALUES (?, ?, ?)");
        return $stmt->execute([$title, $desc, $by]);
    }

    public function update($id, $title, $desc) {
        $stmt = $this->db->prepare("UPDATE praktikum SET title = ?, description = ? WHERE id = ?");
        return $stmt->execute([$title, $desc, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM praktikum WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// routes.php snippet
$routes['praktikum/index'] = ['PraktikumController', 'index'];
$routes['praktikum/create'] = ['PraktikumController', 'create'];
$routes['praktikum/edit']   = ['PraktikumController', 'edit'];
$routes['praktikum/delete'] = ['PraktikumController', 'delete'];
?>

<!-- views/asisten/praktikum/index.php -->
<?php require __DIR__ . '/../../layout.php'; ?>
<div class="container mx-auto p-4">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Manajemen Praktikum</h1>
    <a href="index.php?route=praktikum/create" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Tambah Praktikum</a>
  </div>
  <table class="min-w-full bg-white shadow rounded">
    <thead class="bg-gray-100">
      <tr>
        <th class="py-2 px-4 border">#</th>
        <th class="py-2 px-4 border">Judul</th>
        <th class="py-2 px-4 border">Asisten</th>
        <th class="py-2 px-4 border">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($praktika as $idx => $p): ?>
      <tr>
        <td class="py-2 px-4 border"><?= $idx+1 ?></td>
        <td class="py-2 px-4 border"><?= htmlspecialchars($p['title']) ?></td>
        <td class="py-2 px-4 border"><?= htmlspecialchars($p['asisten']) ?></td>
        <td class="py-2 px-4 border space-x-2">
          <a href="index.php?route=praktikum/edit&id=<?= $p['id'] ?>" class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 rounded">Edit</a>
          <a href="index.php?route=praktikum/delete&id=<?= $p['id'] ?>" onclick="return confirm('Hapus praktikum?');" class="px-2 py-1 bg-red-500 hover:bg-red-600 rounded text-white">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- views/asisten/praktikum/create.php -->
<?php require __DIR__ . '/../../layout.php'; ?>
<div class="container mx-auto p-4">
  <h1 class="text-xl font-semibold mb-4">Tambah Praktikum</h1>
  <form method="post" class="space-y-4">
    <div>
      <label class="block mb-1">Judul</label>
      <input type="text" name="title" class="w-full border p-2 rounded" required>
    </div>
    <div>
      <label class="block mb-1">Deskripsi</label>
      <textarea name="description" class="w-full border p-2 rounded" rows="4"></textarea>
    </div>
    <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">Simpan</button>
  </form>
</div>

<!-- views/asisten/praktikum/edit.php -->
<?php require __DIR__ . '/../../layout.php'; ?>
<div class="container mx-auto p-4">
  <h1 class="text-xl font-semibold mb-4">Edit Praktikum</h1>
  <form method="post" class="space-y-4">
    <div>
      <label class="block mb-1">Judul</label>
      <input value="<?= htmlspecialchars($praktikum['title']) ?>" type="text" name="title" class="w-full border p-2 rounded" required>
    </div>
    <div>
      <label class="block mb-1">Deskripsi</label>
      <textarea name="description" class="w-full border p-2 rounded" rows="4"><?= htmlspecialchars($praktikum['description']) ?></textarea>
    </div>
    <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">Update</button>
  </form>
</div>
