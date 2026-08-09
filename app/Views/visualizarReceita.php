<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CookieHub</title>
    <link rel="stylesheet" href="<?= base_url('css/menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/visualizarReceita.css') ?>">
</head>
<body>

    <?= $this->include('menu') ?>

    <?php if (session()->getFlashdata('sucesso')): ?>

        <div class="mensagem sucesso">
            <?= session()->getFlashdata('sucesso') ?>
        </div>

    <?php endif; ?>

    <div class="pagina-receita">

        <?php if ($receita['idUsuario'] == session()->get('idUsuario')): ?>
            <div class="acoes-receita">
                <a href="<?= base_url('receita/editar/' . $receita['idReceita']) ?>" class="botao-editar">Editar receita</a>
                <button type="button" class="botao-excluir" data-bs-toggle="modal" data-bs-target="#modalExcluir">Excluir receita</button>
            </div>

        <?php endif; ?>

        <div class="cabecalho-receita">

            <div class="informacoes-receita">

                <h1>
                    <?= esc($receita['titulo']) ?>
                </h1>

                <p>
                    Publicado por:
                    <?= esc($receita['nomeUsuario']) ?>
                </p>
                <p class="data-receita">
                    <?= date('d/m/Y', strtotime($receita['data'])) ?>
                </p>

                <div class="tags">

                    <?php foreach ($receita['nomesTags'] as $tag): ?>

                        <span>
                            <?= esc($tag) ?>
                        </span>

                    <?php endforeach; ?>

                </div>

            </div>

            <?php if (!empty($receita['imagem'])): ?>

                <img
                    src="<?= base_url('uploads/' . $receita['imagem']) ?>"
                    alt="<?= esc($receita['titulo']) ?>"
                    class="imagem-receita"
                >

            <?php endif; ?>

        </div>


        <section class="secao-ingredientes">

            <h2>Ingredientes</h2>

            <?php if (!empty($receita['ingredientes'])): ?>

                <div class="lista-ingredientes">

                    <?php foreach ($receita['ingredientes'] as $indice => $ingrediente): ?>

                        <div class="ingrediente">

                            <span class="nome-ingrediente">
                                <?= esc(ucfirst($ingrediente)) ?>
                            </span>

                            <span class="quantidade-ingrediente">

                                <?php if (isset($receita['quantidadeIngredientes'][$indice])): ?>

                                    <?= esc($receita['quantidadeIngredientes'][$indice]) ?> g

                                <?php endif; ?>

                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <p class="sem-informacoes">
                    Ingredientes não disponíveis para esta receita.
                </p>

            <?php endif; ?>

        </section>

        <section>

            <h2>Modo de preparo</h2>

            <p>
                <?= nl2br(esc($receita['legenda'])) ?>
            </p>

        </section>


       <section class="secao-nutricional">

            <h2>Informações nutricionais</h2>

            <?php if (!empty($receita['infosNutricionais'])): ?>

                <div class="nutrientes">

                    <div class="nutriente">
                        <strong>
                            <?= number_format($totalCalorias, 0, ',', '.') ?>
                        </strong>
                        <span>kcal</span>
                    </div>

                    <div class="nutriente">
                        <strong>
                            <?= number_format($totalProteinas, 1, ',', '.') ?> g
                        </strong>
                        <span>Proteínas</span>
                    </div>

                    <div class="nutriente">
                        <strong>
                            <?= number_format($totalCarboidratos, 1, ',', '.') ?> g
                        </strong>
                        <span>Carboidratos</span>
                    </div>

                    <div class="nutriente">
                        <strong>
                            <?= number_format($totalGorduras, 1, ',', '.') ?> g
                        </strong>
                        <span>Gorduras</span>
                    </div>

                </div>

            <?php else: ?>

                <p class="sem-informacoes">
                    Informações nutricionais não disponíveis para esta receita.
                </p>

            <?php endif; ?>

        </section>

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


    <div class="modal fade" id="modalExcluir" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Excluir receita</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja excluir esta receita?
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <a href=""
                    class="btn btn-danger">
                        Excluir
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>

</html>