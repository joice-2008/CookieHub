
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?= $this->include('menu') ?>


    <h1>Cadastrar Receita</h1>

    <form action="<?= base_url('receita/salvar') ?>" method="post" enctype="multipart/form-data">

        <div>
            <label>Título</label><br>
            <input type="text" name="titulo" required>
        </div>

        <br>

        <div>
            <label>Imagem</label><br>
            <input type="file" name="imagem" accept="image/*">
        </div>

        <br>

        <hr>

        <h3>Ingredientes adicionados</h3>

    <label>Pesquisar ingrediente</label>

    <input
        type="text"
        id="pesquisaIngrediente">

    <button
        type="button"
        id="btnPesquisar">

    Pesquisar

    </button>

    <br><br>

    <div id="resultadoPesquisa"></div>

    <hr>

    <p id="ingredienteSelecionadoTexto">
    Nenhum ingrediente selecionado.
    </p>

    <label>Quantidade (g)</label>

    <input
        type="number"
        id="quantidade">

    <button
        type="button"
        id="btnAdicionar">

    Adicionar

    </button>

    <br><br>


        <table border="1" width="100%" id="tabelaIngredientes">
            <thead>
                <tr>
                    <th>Ingrediente</th>
                    <th>Quantidade (g)</th>
                    <th>Ação</th>
                </tr>
            </thead>

            <tbody id="listaIngredientes">

            </tbody>
        </table>

        <div id="inputsOcultos"></div>

        <br>

        <label>Modo de preparo</label><br>

        <textarea
            name="legenda"
            rows="8"
            cols="70"
            placeholder="Descreva o modo de preparo..."></textarea>

        <br><br>
    <h3>Tags</h3>

    <?php foreach ($tags as $tag): ?>

        <label>
            <input
                type="checkbox"
                name="tags[]"
                value="<?= $tag['idTag']; ?>">

            <?= esc($tag['nome']); ?>
        </label>

        <br>

    <?php endforeach; ?>

    <br>

    <button type="submit">
        Salvar Receita
    </button>

    </form>



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
                ${item.nome}
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

        document.getElementById("ingredienteSelecionadoTexto").innerHTML = "<strong>Ingrediente:</strong> "+ ingredienteSelecionado.nome +"<br><small>"+ ingredienteSelecionado.calorias +" kcal por 100 g</small>";

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


