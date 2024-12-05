<?php
include 'conexao.php';
session_start();

//Verifica se a sessão está ativa
if(!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil'])) {
   
    header("Location: index.php?page=login");
    exit;
}
 
if ($_SESSION['perfil'] != "colaborador" && $_SESSION['perfil'] != "adm"){
    header("Location: index.php?page=verSaldo");
    exit;
}


// Variáveis para armazenar informações do doador
$doadorInfo = null;

// Verifica se foi enviado um POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['login'])) {
        // Verifica o email no banco de dados
        $login = $_POST['login'];
        $result = $conn->query("SELECT idUsuario, nome, coin FROM usuarios WHERE email = '$login'");

        if ($result) {
            $doadorInfo = $result->fetch_assoc();
            $_SESSION['doadorInfo'] = $doadorInfo;
        }
    }

    if (isset($_POST['cadastrar_doacao'])) {
        // Registra a doação no banco de dados
        $itens = json_decode($_POST['itens'], true);
        $senacoins_total = $_POST['senacoins_total'];
        $usuario_id = $_SESSION['usuario_id'];

        // Inicia transação
        $conn->begin_transaction();

        try {
            // Insere a doação na tabela 'doacao'
            $stmt = $conn->prepare("INSERT INTO doacao (idUsuario) VALUES (?)");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $doacao_id = $conn->insert_id; // Obtém o id da doação inserida

            // Insere os produtos na tabela 'doacaoproduto'
            foreach ($itens as $item) {
                $stmt = $conn->prepare("INSERT INTO doacaoproduto (idDoacao, idProduto, quantidade) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $doacao_id, $item['id_produto'], $item['quantidade']);
                $stmt->execute();
            }

            // Atualiza os Senacoins do usuário
            $stmt = $conn->prepare("UPDATE usuarios SET coin = coin - ? WHERE idUsuario = ?");
            $stmt->bind_param("ii", $senacoins_total, $usuario_id);
            $stmt->execute();

            // Comita a transação
            $conn->commit();

            echo json_encode(['sucesso' => true]);
        } catch (Exception $e) {
            $conn->rollback(); // Desfaz a transação em caso de erro
            echo json_encode(['erro' => $e->getMessage()]);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doação</title>
    <link rel="stylesheet" href="forms.css">
</head>
<body>
<div class="form-container">

    <h2>Doação</h2>

    <!-- Formulário para verificar o usuário -->
    <form action="doacao.php" method="post">
        <label for="login">Login</label>
        <input type="text" name="login" required>
        <button type="submit" name="verificarUsuario" value="click">Verificar usuário</button>
    </form>

    <!-- Exibe informações do doador -->
    <?php if ($doadorInfo): ?>
        <div class="verificacao">
            <strong>Nome:</strong> <?= htmlspecialchars($doadorInfo['nome']) ?> <br>
            <strong>Coin:</strong> <?= $doadorInfo['coin'] ?>
        </div>

        <!-- Formulário para selecionar a categoria e quantidade do produto -->
        <div class="produtoSection">
            <label for="categorias">Classificação do Objeto Doado:</label>
            <select name="categorias" id="categorias">
                <option selected disabled value="">Selecione categoria</option>
                <option value="1">Acessórios -- 3 senacoins</option>
                <option value="2">Livros/DVD/CD/Disco -- 3 senacoins</option>
                <option value="3">Utensílios de cozinha -- 3 senacoins</option>
                <option value="4">Vestuário e Calçados -- 5 senacoins</option>
                <option value="5">Brinquedos e jogos -- 5 senacoins</option>
                <option value="6">Artigos automotivos -- 6 senacoins</option>
                <option value="7">Eletrônicos/Eletrodomésticos -- 10 senacoins</option>
                <option value="8">Mochilas -- 10 senacoins</option>
            </select>
        </div>

        <div class="quantidadeCoins">
            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" min="1" required>
            <button type="button" class="verificar" onclick="adicionarProduto()">Adicionar Produto</button>
        </div>

        <!-- Tabela para exibir os produtos adicionados -->
        <table class="tabelaProdutos" style="display: none;">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor Unitário</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <!-- Produtos serão exibidos aqui -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th id="totalSenacoins">0</th>
                </tr>
            </tfoot>
        </table>

        <!-- Botão para cadastrar a doação -->
        <button type="button" class="verificar" id="cadastrarDoacao" onclick="cadastrarDoacao()" style="display: none;">Cadastrar Doação</button>
    <?php endif; ?>
</div>

<script>
    // Função para adicionar produto à tabela
    function adicionarProduto() {
        const categoriaSelect = document.getElementById('categorias');
        const quantidade = document.getElementById('quantidade').value;
        if (!categoriaSelect.value || !quantidade) return; // Verifica se os campos estão preenchidos

        const categoriaNome = categoriaSelect.options[categoriaSelect.selectedIndex].text;
        const valorUnitario = parseInt(categoriaNome.split('--')[1].trim().split(' ')[0]);
        const subtotal = valorUnitario * quantidade;

        // Adiciona o produto na tabela
        const tabela = document.querySelector('.tabelaProdutos');
        const tbody = tabela.querySelector('tbody');
        const row = tbody.insertRow();
        row.insertCell(0).textContent = categoriaNome.split('--')[0].trim();
        row.insertCell(1).textContent = quantidade;
        row.insertCell(2).textContent = valorUnitario;
        row.insertCell(3).textContent = subtotal;
        row.cells[0].setAttribute('data-id-produto', categoriaSelect.value);

        tabela.style.display = 'table';
        document.getElementById('cadastrarDoacao').style.display = 'block';

        // Reseta o select e a quantidade
        categoriaSelect.selectedIndex = 0;
        document.getElementById('quantidade').value = '';

        atualizarTotal(subtotal);
    }

    // Função para atualizar o total na tabela
    function atualizarTotal(subtotal) {
        const totalElement = document.getElementById('totalSenacoins');
        const totalAtual = parseInt(totalElement.textContent);
        totalElement.textContent = totalAtual + subtotal;
    }

    // Função para cadastrar a doação no banco de dados
    function cadastrarDoacao() {
        const tabela = document.querySelector('.tabelaProdutos');
        const linhas = tabela.querySelectorAll('tbody tr');
        const itens = [];

        linhas.forEach(linha => {
            const colunas = linha.querySelectorAll('td');
            const id_produto = parseInt(colunas[0].getAttribute('data-id-produto'));
            const quantidade = parseInt(colunas[1].textContent);
            const valorUnitario = parseInt(colunas[2].textContent);

            itens.push({
                id_produto: id_produto,
                quantidade: quantidade,
                valor_unitario: valorUnitario,
                subtotal: quantidade * valorUnitario
            });
        });

        const senacoins_total = document.getElementById('totalSenacoins').textContent;

        // Envia a doação para o servidor via AJAX
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                cadastrar_doacao: true,
                senacoins_total: senacoins_total,
                itens: itens
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert("Doação cadastrada com sucesso!");
                window.location.href = "index.php"; // Redireciona após sucesso
            } else {
                alert("Erro ao cadastrar a doação: " + data.erro);
            }
        })
        .catch(error => {
            console.error('Erro durante a requisição:', error);
            alert('Ocorreu um erro ao tentar registrar a doação.');
        });
    }
</script>

</body>
</html>
