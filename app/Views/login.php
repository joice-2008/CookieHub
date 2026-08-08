
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CookieHub</title>
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
</head>
<body>
    <div class="container">
        <a href="<?= base_url() ?>" class="voltar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Voltar
        </a>
 
        <div>
            <p class="titulo">Faça login</p>
        </div>
 
        <form action="<?= site_url('usuario/logar') ?>" method="post">
            <div>
                <label for="usuario" class="label">Usuário</label>
                <input type="text" class="campo" id="usuario" name="usuario">
            </div>
 
            <div>
                <label for="senha" class="label">Senha</label>
                <div class="campo-senha">
                    <input type="password" class="campo" id="senha" name="senha">
                    <button type="button" class="toggle-senha" id="toggleSenha" aria-label="Mostrar senha">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
 
            <div>
                <input type="submit" value="Entrar" id="botao">
            </div>
        </form>
 
        <p class="campoNTC">
            Ainda não tem conta?
            <a href="<?= base_url('cadastro') ?>" class="criar-conta">Criar conta</a>
        </p>
        <?php if (session()->getFlashdata('loginNaoEncontrado')): ?>
            <div class="mensagem-erro">
                <p><?= session()->getFlashdata('loginNaoEncontrado') ?></p>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('campoVazio')): ?>
            <div class="mensagem-erro">
                <p><?= session()->getFlashdata('campoVazio') ?></p>
            </div>
        <?php endif; ?>
    </div>

    
 
    <script>
        const toggleSenha = document.getElementById('toggleSenha');
        const campoSenha = document.getElementById('senha');
 
        toggleSenha.addEventListener('click', () => {
            const tipo = campoSenha.getAttribute('type') === 'password' ? 'text' : 'password';
            campoSenha.setAttribute('type', tipo);
        });
    </script>
</body>
</html>