<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CookieHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('css/menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/feed.css') ?>">
</head>
<body>
    <?= $this->include('menu') ?>

    <div class="container-feed">

        <div class="dados-usuario">

        <h1 class="mensagem-perfil">
        Bem-vindo(a) ao seu perfil <?= esc($nomeUsuario) ?>!
        </h1><br>

        <h2>@<?= esc($loginUsuario) ?></h2><br>

        <div class="quantidade-receitas">
        <h3><?= count($receitas) ?> <?= count($receitas) == 1 ? 'receita cadastrada' : 'receitas cadastradas' ?></h3>
        <br>
        </div>

    </div>

    <h2 class="titulo-receitas">Minhas Receitas</h2>
        

    <div class="feed-receitas">
            <?php if (empty($receitas)): ?>

        <div class="sem-receitas">

            <h3>Você ainda não cadastrou nenhuma receita.</h3>

            <p>
                Suas receitas aparecerão aqui quando você cadastrar uma.
            </p>

        </div>

    <?php else: ?>

        <?php foreach($receitas as $receita): ?>

            <a href="<?= base_url('receita/visualizar/' . $receita['idReceita']) ?>" class="link-receita">

            <div class="card-receita">
                <div class="imagem">
                    <img src="<?= base_url('uploads/' . $receita['imagem']) ?>"
                    alt="<?= esc($receita['titulo']) ?>">
                </div>
                <div class="conteudo-receita">
                    <h2><?= $receita['titulo'] ?></h2>
                    <div class="usuario">
                        <?= $receita['nomeUsuario'] ?>
                    </div>
                    <div class="data">
                        <?= date('d/m/Y', strtotime($receita['data'])) ?>
                    </div>
                    <div class="tags">
                        <?php foreach ($receita['nomesTags'] as $tag): ?>
                            <span class="tag"><?= esc($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            </a>
        <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </div>


    <div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLogoutLabel">Confirmar saída</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja sair da sua conta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <a href="<?= site_url('logout') ?>" class="btn btn-danger">
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>
</html>