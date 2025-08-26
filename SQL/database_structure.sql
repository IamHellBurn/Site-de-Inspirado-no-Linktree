-- Criação do banco de dados
CREATE DATABASE linktree_clone;
USE linktree_clone;

-- Tabela de usuários/administradores
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_name VARCHAR(100) NOT NULL,
    bio TEXT,
    profile_image VARCHAR(255),
    background_color VARCHAR(7) DEFAULT '#ffffff',
    text_color VARCHAR(7) DEFAULT '#000000',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de links
CREATE TABLE links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(500) NOT NULL,
    icon VARCHAR(50),
    position_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    clicks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de cliques (para analytics básico)
CREATE TABLE click_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    link_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE CASCADE
);

-- Inserir usuário padrão para testes
INSERT INTO users (username, email, password, profile_name, bio) 
VALUES ('admin', 'admin@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Meu Perfil', 'Bem-vindo ao meu espaço digital!');

-- Inserir alguns links de exemplo
INSERT INTO links (user_id, title, url, icon, position_order) VALUES
(1, 'Instagram', 'https://instagram.com/usuario', 'fab fa-instagram', 1),
(1, 'YouTube', 'https://youtube.com/usuario', 'fab fa-youtube', 2),
(1, 'Website Pessoal', 'https://meusite.com', 'fas fa-globe', 3),
(1, 'LinkedIn', 'https://linkedin.com/in/usuario', 'fab fa-linkedin', 4);
