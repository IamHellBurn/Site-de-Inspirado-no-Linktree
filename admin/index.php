<?php
require_once '../config.php';

// Verificar login
if (!isLoggedIn() && !isset($_POST['login'])) {
    // Mostrar formulário de login
    include 'login.php';
    exit();
}

// Processar login
if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        redirect('index.php');
    } else {
        $error = "Credenciais inválidas!";
        include 'login.php';
        exit();
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('index.php');
}

$db = new Database();
$conn = $db->getConnection();
$user_id = $_SESSION['user_id'];

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_link'])) {
        // Adicionar novo link
        $title = sanitize($_POST['title']);
        $url = sanitize($_POST['url']);
        $icon = sanitize($_POST['icon']);
        
        $stmt = $conn->prepare("INSERT INTO links (user_id, title, url, icon, position_order) VALUES (?, ?, ?, ?, (SELECT COALESCE(MAX(position_order), 0) + 1 FROM links l WHERE l.user_id = ?))");
        $stmt->execute([$user_id, $title, $url, $icon, $user_id]);
        $success = "Link adicionado com sucesso!";
    }
    
    if (isset($_POST['update_profile'])) {
        // Atualizar perfil
        $profile_name = sanitize($_POST['profile_name']);
        $bio = sanitize($_POST['bio']);
        $background_color = sanitize($_POST['background_color']);
        $text_color = sanitize($_POST['text_color']);
        
        $stmt = $conn->prepare("UPDATE users SET profile_name = ?, bio = ?, background_color = ?, text_color = ? WHERE id = ?");
        $stmt->execute([$profile_name, $bio, $background_color, $text_color, $user_id]);
        $success = "Perfil atualizado com sucesso!";
    }
    
    if (isset($_POST['delete_link'])) {
        // Deletar link
        $link_id = $_POST['link_id'];
        $stmt = $conn->prepare("DELETE FROM links WHERE id = ? AND user_id = ?");
        $stmt->execute([$link_id, $user_id]);
        $success = "Link removido com sucesso!";
    }
    
    if (isset($_POST['toggle_link'])) {
        // Ativar/desativar link
        $link_id = $_POST['link_id'];
        $stmt = $conn->prepare("UPDATE links SET is_active = NOT is_active WHERE id = ? AND user_id = ?");
        $stmt->execute([$link_id, $user_id]);
        $success = "Status do link atualizado!";
    }
}

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar links
$stmt = $conn->prepare("SELECT * FROM links WHERE user_id = ? ORDER BY position_order ASC");
$stmt->execute([$user_id]);
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas
$stmt = $conn->prepare("SELECT SUM(clicks) as total_clicks, COUNT(*) as total_links FROM links WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Linktree</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .card h3 {
            margin-bottom: 20px;
            color: #333;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        }

        .btn-small {
            padding: 8px 15px;
            font-size: 12px;
        }

        .link-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }

        .link-info {
            flex-grow: 1;
        }

        .link-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .link-url {
            color: #666;
            font-size: 12px;
        }

        .link-actions {
            display: flex;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            display: block;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .preview-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px;
            border-radius: 50%;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }

        .preview-link:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-link"></i>
                Painel Administrativo
            </div>
            <div class="user-menu">
                <span>Olá, <?php echo htmlspecialchars($user['username']); ?>!</span>
                <a href="?logout" class="btn btn-small">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div class="container">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="stats-grid" style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['total_links'] ?: 0; ?></span>
            <div class="stat-label">Total de Links</div>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['total_clicks'] ?: 0; ?></span>
            <div class="stat-label">Total de Cliques</div>
        </div>
        <div class="stat-card">
            <span class="stat-number">
                <?php echo count(array_filter($links, function($l) { return $l['is_active']; })); ?>
            </span>
            <div class="stat-label">Links Ativos</div>
        </div>
    </div>

    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Perfil -->
            <div class="card">
                <h3><i class="fas fa-user"></i> Editar Perfil</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Nome do Perfil</label>
                        <input type="text" name="profile_name" value="<?php echo htmlspecialchars($user['profile_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" rows="3"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Cor de Fundo</label>
                        <input type="color" name="background_color" value="<?php echo $user['background_color']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Cor do Texto</label>
                        <input type="color" name="text_color" value="<?php echo $user['text_color']; ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn">
                        <i class="fas fa-save"></i> Salvar Perfil
                    </button>
                </form>
            </div>

            <!-- Adicionar Link -->
            <div class="card">
                <h3><i class="fas fa-plus"></i> Adicionar Link</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="title" required placeholder="Ex: Instagram">
                    </div>
                    <div class="form-group">
                        <label>URL</label>
                        <input type="url" name="url" required placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Ícone (Font Awesome)</label>
                        <select name="icon">
                            <option value="">Sem ícone</option>
                            <option value="fab fa-instagram">Instagram</option>
                            <option value="fab fa-facebook">Facebook</option>
                            <option value="fab fa-twitter">Twitter</option>
                            <option value="fab fa-youtube">YouTube</option>
                            <option value="fab fa-linkedin">LinkedIn</option>
                            <option value="fab fa-tiktok">TikTok</option>
                            <option value="fab fa-whatsapp">WhatsApp</option>
                            <option value="fas fa-globe">Website</option>
                            <option value="fas fa-envelope">Email</option>
                            <option value="fas fa-phone">Telefone</option>
                        </select>
                    </div>
                    <button type="submit" name="add_link" class="btn">
                        <i class="fas fa-plus"></i> Adicionar Link
                    </button>
                </form>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="main-content">
            <div class="card">
                <h3><i class="fas fa-list"></i> Gerenciar Links</h3>
                
                <?php if (empty($links)): ?>
                    <p>Nenhum link encontrado. Adicione seu primeiro link!</p>
                <?php else: ?>
                    <?php foreach ($links as $link): ?>
                        <div class="link-item">
                            <div class="link-info">
                                <div class="link-title">
                                    <?php if ($link['icon']): ?>
                                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($link['title']); ?>
                                    <?php if (!$link['is_active']): ?>
                                        <span style="color: #999; font-size: 12px;">(Inativo)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="link-url"><?php echo htmlspecialchars($link['url']); ?></div>
                                <div class="link-url">
                                    <i class="fas fa-mouse-pointer"></i> 
                                    <?php echo $link['clicks']; ?> cliques
                                </div>
                            </div>
                            <div class="link-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="link_id" value="<?php echo $link['id']; ?>">
                                    <button type="submit" name="toggle_link" class="btn btn-small" 
                                            style="background: <?php echo $link['is_active'] ? '#ffc107' : '#28a745'; ?>;">
                                        <i class="fas fa-<?php echo $link['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja remover este link?');">
                                    <input type="hidden" name="link_id" value="<?php echo $link['id']; ?>">
                                    <button type="submit" name="delete_link" class="btn btn-danger btn-small">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <a href="../index.php" class="preview-link" title="Ver Página Pública">
        <i class="fas fa-eye"></i>
    </a>

    <script>
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 3000);
            });
        });
    </script>
</body>
</html>
