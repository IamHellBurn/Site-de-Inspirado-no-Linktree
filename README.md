# Linktree Clone - Site de Links Sociais

Um clone simples e elegante do Linktree, criado com PHP e MySQL, permitindo que você gerencie seus links sociais em uma página única e personalizada.

## 🚀 Características

- ✨ Interface moderna e responsiva
- 🎨 Personalização de cores e perfil
- 📊 Analytics básico de cliques
- 🔒 Painel administrativo protegido
- 📱 Totalmente responsivo
- ⚡ Carregamento rápido
- 🎯 Fácil de usar e configurar

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior (ou MariaDB)
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL

## 🛠️ Instalação

### 1. Clone/Download do projeto
```bash
# Clone o repositório ou faça download dos arquivos
git clone https://github.com/IamHellBurn/Site-de-Inspirado-no-Linktree.git
cd Site-de-Inspirado-no-Linktree
```

### 2. Configuração do Banco de Dados

1. Crie um banco de dados MySQL:
```sql
CREATE DATABASE linktree_clone;
```

2. Execute o script SQL fornecido no arquivo `database_structure.sql`:
```bash
mysql -u seu_usuario -p linktree_clone < database_structure.sql
```

### 3. Configuração do PHP

1. Edite o arquivo `config.php` com suas credenciais de banco:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'linktree_clone');
```

### 4. Configuração do Servidor

1. Copie os arquivos para o diretório do seu servidor web
2. Certifique-se de que o arquivo `.htaccess` está no diretório raiz
3. Configure as permissões adequadas:
```bash
chmod 644 *.php
chmod 755 admin/
chmod 644 admin/*.php
```

### 5. Acesso ao Sistema

1. **Página Principal**: `http://seudominio.com/`
2. **Painel Admin**: `http://seudominio.com/admin/`

## 🔑 Login Padrão

- **Usuário**: `admin`
- **Senha**: `password`

⚠️ **IMPORTANTE**: Altere a senha padrão após o primeiro login!

## 📁 Estrutura de Arquivos

```
├── index.php              # Página principal
├── config.php             # Configurações do banco
├── .htaccess              # Configurações Apache
├── admin/                 # Painel administrativo
│   ├── index.php          # Dashboard admin
│   └── login.php          # Página de login
├── SQL
│   ├──  database_structure.sql # Estrutura do banco
└── README.md             # Este arquivo
```

## 🎨 Personalização

### Alterando o Visual
1. Acesse o painel administrativo
2. Vá em "Editar Perfil"
3. Personalize:
   - Nome do perfil
   - Biografia
   - Cores do tema
   - Foto de perfil (futuramente)

### Gerenciando Links
1. No painel admin, use "Adicionar Link"
2. Preencha:
   - Título do link
   - URL de destino
   - Ícone (Font Awesome)
3. Use os botões para ativar/desativar ou excluir links

## 🔧 Configurações Avançadas

### Múltiplos Usuários
Para suportar múltiplos usuários, você pode:
1. Modificar o `index.php` para aceitar parâmetros de usuário
2. Implementar sistema de registro
3. Adicionar rotas personalizadas

### Analytics Avançado
O sistema já coleta dados básicos de cliques. Para expandir:
1. Adicione Google Analytics
2. Implemente relatórios detalhados
3. Adicione gráficos no painel admin

### Upload de Imagens
Para habilitar upload de fotos de perfil:
1. Crie diretório `uploads/`
2. Configure permissões de escrita
3. Implemente formulário de upload

## 🛡️ Segurança

### Recomendações de Segurança
1. **Altere a senha padrão** imediatamente
2. **Use HTTPS** em produção
3. **Mantenha o PHP atualizado**
4. **Configure backups regulares**
5. **Monitor logs de acesso**

### Configurações de Produção
```php
// Em config.php, para produção:
ini_set('display_errors', 0);
error_reporting(0);
```

## 🚀 Deploy em Produção

### Hospedagem Compartilhada
1. Faça upload via FTP/cPanel
2. Importe o banco via phpMyAdmin
3. Configure as credenciais
4. Teste o funcionamento

### VPS/Cloud
1. Configure Apache/Nginx
2. Instale PHP e MySQL
3. Configure SSL/HTTPS
4. Configure firewall

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
- Verifique as credenciais em `config.php`
- Confirme se o MySQL está rodando
- Teste a conexão manualmente

### Página em Branco
- Ative exibição de erros temporariamente
- Verifique logs do servidor
- Confirme permissões de arquivos

### Links não Funcionam
- Verifique se o mod_rewrite está ativo
- Confirme se o `.htaccess` está presente
- Teste URLs completas

## 📈 Próximas Melhorias

- [ ] Upload de imagens de perfil
- [ ] Temas pré-definidos
- [ ] Analytics avançado com gráficos
- [ ] API para integração externa
- [ ] Sistema de múltiplos usuários
- [ ] Integração com redes sociais
- [ ] QR Code para compartilhamento
- [ ] Scheduler para links temporários

## 🤝 Contribuindo

1. Faça um Fork do projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 💰 Ajuda

Para ajudar financeiramente (🔑Pix):
```bash
901d17cd-2de8-4a1f-ae04-4b001ea23ab6
```

---

**Desenvolvido com ❤️ para a comunidade**

Divirta-se personalizando seu próprio hub de links! 🎉
