<?php
require_once 'config.php';

// Obter dados do usuário (por padrão, primeiro usuário)
$db = new Database();
$conn = $db->getConnection();

// Buscar usuário (pode ser expandido para múltiplos usuários)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = 1");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar links ativos ordenados por posição
$stmt = $conn->prepare("SELECT * FROM links WHERE user_id = 1 AND is_active = 1 ORDER BY position_order ASC");
$stmt->execute();
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar clique em link
if (isset($_GET['link']) && is_numeric($_GET['link'])) {
    $link_id = $_GET['link'];
    
    // Verificar se o link existe
    $stmt = $conn->prepare("SELECT * FROM links WHERE id = ? AND is_active = 1");
    $stmt->execute([$link_id]);
    $link = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($link) {
        // Registrar clique
        $stmt = $conn->prepare("UPDATE links SET clicks = clicks + 1 WHERE id = ?");
        $stmt->execute([$link_id]);
        
        // Registrar analytics
        $stmt = $conn->prepare("INSERT INTO click_analytics (link_id, ip_address, user_agent) VALUES (?, ?, ?)");
        $stmt->execute([$link_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
        
        // Redirecionar
        header("Location: " . $link['url']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['profile_name']); ?> - Meus Links</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: <?php echo $user['text_color']; ?>;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .bio {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.5;
        }

        .links-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .link-item {
            display: block;
            padding: 18px 25px;
            background: white;
            color: #333;
            text-decoration: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            border: 2px solid #f0f0f0;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .link-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .link-item i {
            margin-right: 12px;
            font-size: 18px;
            width: 25px;
            text-align: center;
        }

        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 12px;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .admin-link:hover {
            background: #555;
            transform: scale(1.1);
        }

        .stats {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 30px 20px;
            }
            
            .profile-image {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
            
            .profile-name {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-image">
            <?php if ($user['profile_image']): ?>
                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile">
            <?php else: ?>
                <i class="fas fa-user"></i>
            <?php endif; ?>
        </div>
        
        <h1 class="profile-name"><?php echo htmlspecialchars($user['profile_name']); ?></h1>
        
        <?php if ($user['bio']): ?>
            <p class="bio"><?php echo htmlspecialchars($user['bio']); ?></p>
        <?php endif; ?>
        
        <div class="links-container">
            <?php foreach ($links as $link): ?>
                <a href="?link=<?php echo $link['id']; ?>" class="link-item" target="_blank">
                    <?php if ($link['icon']): ?>
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                    <?php else: ?>
                        <i class="fas fa-link"></i>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($link['title']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <?php
        // Calcular total de cliques
        $stmt = $conn->prepare("SELECT SUM(clicks) as total_clicks FROM links WHERE user_id = 1");
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        
        <div class="stats">
            <i class="fas fa-chart-bar"></i>
            Total de cliques: <?php echo $stats['total_clicks'] ?: 0; ?>
        </div>
    </div>
    
    <a href="admin/" class="admin-link" title="Painel Administrativo">
        <i class="fas fa-cog"></i>
    </a>

    <script>
        // Adicionar efeito de partículas no fundo
        document.addEventListener('DOMContentLoaded', function() {
            // Efeito suave de hover nos links
            const links = document.querySelectorAll('.link-item');
            links.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                });
                
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>
