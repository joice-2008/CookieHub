
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= base_url('css/menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/cadastroReceita.css') ?>">
</head>
<body>
    <?= $this->include('menu') ?>


    <div class="cadastro-container">

        <div class="cabecalho-cadastro">
            <h1>Cadastrar receita</h1>
            <p>Compartilhe sua nova receita no CookieHub!</p>
        </div>

        <form action="<?= base_url('receita/salvar') ?>" method="post" enctype="multipart/form-data" class="form-receita">

            <section class="secao-form">

                <h2>Informações da receita</h2>

                <div class="campo">
                    <label for="titulo">Título da receita</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Ex: Bolo de cenoura com cobertura" required>
                </div>

                <div class="campo">
                    <label for="imagem">Imagem da receita</label>

                    <div class="upload-imagem">
                        <div>
                            <strong>Escolha uma imagem</strong>
                            <small>PNG, JPG ou JPEG</small>
                        </div>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                    </div>
                </div>

            </section>

            <section class="secao-form">

                <h2>Ingredientes</h2>

                <p class="descricao-secao">
                    Pesquise os ingredientes e informe a quantidade utilizada.
                </p>

                <div class="pesquisa-ingrediente">

                    <div class="campo campo-pesquisa">
                        <label for="pesquisaIngrediente">
                            Pesquisar ingrediente
                        </label>

                        <input
                            type="text"
                            id="pesquisaIngrediente"
                            placeholder="Digite o nome do ingrediente...">
                    </div>

                    <button
                        type="button"
                        id="btnPesquisar"
                        class="btn-principal btn-pesquisar">
                        Pesquisar
                    </button>

                </div>

                <div id="resultadoPesquisa" class="resultado-pesquisa"></div>


                <div class="ingrediente-selecionado">

                    <p id="ingredienteSelecionadoTexto">
                        Nenhum ingrediente selecionado.
                    </p>

                    <div class="adicionar-ingrediente">

                        <div class="campo">
                            <label for="quantidade">
                                Quantidade (g)
                            </label>

                            <input
                                type="number"
                                id="quantidade"
                                min="1"
                                placeholder="Ex: 200">
                        </div>

                        <button
                            type="button"
                            id="btnAdicionar"
                            class="btn-adicionar">
                            + Adicionar
                        </button>

                    </div>

                </div>


                <div class="tabela-container">

                    <table id="tabelaIngredientes">

                        <thead>
                            <tr>
                                <th>Ingrediente</th>
                                <th>Quantidade</th>
                                <th>Ação</th>
                            </tr>
                        </thead>

                        <tbody id="listaIngredientes"></tbody>

                    </table>

                </div>

                <div id="inputsOcultos"></div>

            </section>


            <section class="secao-form">

                <h2>Modo de preparo</h2>

                <div class="campo">

                    <label for="legenda">
                        Como preparar?
                    </label>

                    <textarea
                        name="legenda"
                        id="legenda"
                        rows="8"
                        placeholder="Descreva passo a passo como preparar sua receita..."></textarea>

                </div>

            </section>

            <section class="secao-form">

                <h2>Categorias</h2>

                <p class="descricao-secao">
                    Selecione as categorias que combinam com sua receita.
                </p>

                <div class="lista-tags">

                    <?php foreach ($tags as $tag): ?>

                        <label class="tag-checkbox">

                            <input
                                type="checkbox"
                                name="tags[]"
                                value="<?= $tag['idTag']; ?>">

                            <span>
                                <?= esc($tag['nome']); ?>
                            </span>

                        </label>

                    <?php endforeach; ?>

                </div>

            </section>

            <div class="acoes-form">
                <button type="submit" class="btn-salvar">
                    Salvar receita
                </button>
            </div>

        </form>
    </div>



    <script>
        let ingredientes = [];
        let ingredienteSelecionado = null;
        let resultadosPesquisa = [];

        const btnAdicionar = document.getElementById("btnAdicionar");
        const btnPesquisar = document.getElementById("btnPesquisar");
        const txtPesquisa = document.getElementById("pesquisaIngrediente");
        const txtQuantidade = document.getElementById("quantidade");
        const divResultados = document.getElementById("resultadoPesquisa");
        const textoSelecionado = document.getElementById("ingredienteSelecionadoTexto");
        const tabela = document.getElementById("listaIngredientes");
        const inputsOcultos = document.getElementById("inputsOcultos");

        btnPesquisar.addEventListener("click", pesquisarIngrediente);
        btnAdicionar.addEventListener("click", adicionarIngrediente);
    
        async function pesquisarIngrediente(){
            const pesquisa = document.getElementById("pesquisaIngrediente").value;
            if(pesquisa == ""){
                alert("Digite um ingrediente.");
                return;
            }
            const resposta = await fetch("<?= base_url('api/pesquisar/') ?>" + pesquisa);
            const resultados = await resposta.json();
            resultadosPesquisa = resultados;
            mostrarResultadosPesquisa(resultados);
        }

        function mostrarResultadosPesquisa(resultados){
            divResultados.innerHTML = "";
            if(resultados.length == 0){
            divResultados.innerHTML = "<p>Nenhum ingrediente encontrado.</p>";
            return;
            }

            resultados.forEach(function(item){
                divResultados.innerHTML += `
                <div style="margin-bottom:10px;">
                    ${item.nome.charAt(0).toUpperCase() + item.nome.slice(1)}
                    <button type="button" onclick="selecionarIngrediente(${item.id})"> Selecionar </button>
                </div>
                `;
            });
        }

        async function selecionarIngrediente(id){
            ingredienteSelecionado = resultadosPesquisa.find(function(item){
                return item.id == id;
            });
            const resposta = await fetch(
                "<?= base_url('api/informacoes/') ?>" +
                ingredienteSelecionado.id
            );
            const dadosNutricionais = await resposta.json();
            ingredienteSelecionado.calorias = dadosNutricionais.calorias;
            ingredienteSelecionado.proteinas = dadosNutricionais.proteinas;
            ingredienteSelecionado.carboidratos =  dadosNutricionais.carboidratos;
            ingredienteSelecionado.gorduras = dadosNutricionais.gorduras;

            const nomeIngrediente = ingredienteSelecionado.nome.charAt(0).toUpperCase() + ingredienteSelecionado.nome.slice(1);
            document.getElementById("ingredienteSelecionadoTexto").innerHTML = "<strong>Ingrediente:</strong> " + nomeIngrediente + "<br><small>" + ingredienteSelecionado.calorias + " kcal por 100 g</small>";

            document.getElementById("resultadoPesquisa").innerHTML = "";
        }


        function adicionarIngrediente(){
            const quantidade = document.getElementById("quantidade").value;
            if(ingredienteSelecionado == null){
                alert("Selecione um ingrediente.");
                return;
            }
            if(quantidade == ""){
                alert("Informe a quantidade.");
                return;
            }

            ingredientes.push({
                idApi: ingredienteSelecionado.id,
                nome: ingredienteSelecionado.nome,
                imagem: ingredienteSelecionado.imagem,
                quantidade: quantidade,
                calorias: ingredienteSelecionado.calorias,
                carboidratos: ingredienteSelecionado.carboidratos,
                proteinas: ingredienteSelecionado.proteinas,
                gorduras: ingredienteSelecionado.gorduras
            });

            atualizarTela();
            document.getElementById("quantidade").value = "";
            ingredienteSelecionado = null;
            document.getElementById("ingredienteSelecionadoTexto").innerHTML ="Nenhum ingrediente selecionado.";
            document.getElementById("pesquisaIngrediente").value = "";
        }

        function atualizarTela(){
            atualizarTabelaIngredientes();
            atualizarInputsOcultos();
        }

        function atualizarTabelaIngredientes(){
            tabela.innerHTML = "";
            ingredientes.forEach(function(item, indice){
                tabela.innerHTML += `
                    <tr>
                        <td>${item.nome}</td>
                        <td>${item.quantidade} g</td>
                        <td>
                            <button type="button" onclick="removerIngrediente(${indice})"> Remover</button>
                        </td>
                    </tr>
                `;
            });

        }

        function atualizarInputsOcultos(){
        inputsOcultos.innerHTML = "";
        ingredientes.forEach(function(item, indice){
            inputsOcultos.innerHTML += `
                <input type="hidden" name="ingredientes[${indice}][idApi]" value="${item.idApi}">
                <input type="hidden" name="ingredientes[${indice}][nome]" value="${item.nome}">
                <input type="hidden" name="ingredientes[${indice}][quantidade]" value="${item.quantidade}">
                <input type="hidden" name="ingredientes[${indice}][calorias]" value="${item.calorias}">
                <input type="hidden" name="ingredientes[${indice}][proteinas]" value="${item.proteinas}">
                <input type="hidden" name="ingredientes[${indice}][carboidratos]" value="${item.carboidratos}">
                <input type="hidden" name="ingredientes[${indice}][gorduras]" value="${item.gorduras}">
            `;
        });

        }
        function removerIngrediente(indice){
            ingredientes.splice(indice,1);
            atualizarTela();
        }

    </script>


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
</body>
</html>


