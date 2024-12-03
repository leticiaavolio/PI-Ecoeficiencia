<?php
include 'conexao.php'; 

session_start();

if(!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil'])) {
   
    header("Location: index.php?page=login");
    exit;
}
 
if ($_SESSION['perfil'] != "adm"){
    header("Location: index.php?page=verSaldo");
    exit;
}

$query1 = "SELECT u.nome AS nome_usuario, SUM(dp.quantidade) AS total_doado FROM usuarios u 
JOIN doacao d ON u.idUsuario = d.idUsuario JOIN doacaoProduto dp ON d.idDoacao = dp.idDoacao
GROUP BY u.idUsuario, u.nome ORDER BY total_doado DESC LIMIT 1";

$query2 = "SELECT SUM(dp.quantidade) AS total_quantidade_doada FROM doacaoProduto dp";

$query3 = "SELECT COUNT(*) AS total_trocas FROM troca";

$query4 = "SELECT p.idProduto, p.categorias, p.quantidade AS quantidade_inicial, 
COALESCE(SUM(dp.quantidade), 0) AS quantidade_doada, COALESCE(SUM(tp.quantidade), 0) 
AS quantidade_trocada, (p.quantidade + COALESCE(SUM(dp.quantidade), 0) - COALESCE(SUM(tp.quantidade), 0)) AS sobra
FROM produtos p LEFT JOIN doacaoProduto dp ON p.idProduto = dp.idProduto LEFT JOIN trocaProduto tp ON p.idProduto = tp.idProduto 
GROUP BY p.idProduto, p.categorias, p.quantidade";

$query5 = "SELECT p.categorias, COUNT(DISTINCT t.idTroca) AS total_trocas, SUM(tp.quantidade) AS total_itens_trocados
FROM troca t JOIN trocaProduto tp ON t.idTroca = tp.idTroca JOIN produtos p ON tp.idProduto = p.idProduto GROUP BY p.categorias";

// Executa as consultas e verifica erros
$result1 = mysqli_query($conn, $query1) or die("Erro na consulta 1: " . mysqli_error($conn));
$result2 = mysqli_query($conn, $query2) or die("Erro na consulta 2: " . mysqli_error($conn));
$result3 = mysqli_query($conn, $query3) or die("Erro na consulta 3: " . mysqli_error($conn));
$result4 = mysqli_query($conn, $query4) or die("Erro na consulta 4: " . mysqli_error($conn));
$result5 = mysqli_query($conn, $query5) or die("Erro na consulta 5: " . mysqli_error($conn));

// Processa os resultados
$maisDoaram = mysqli_fetch_assoc($result1) ?: ['nome_usuario' => 'Nenhum dado', 'total_doado' => 0];
$totalDoacoes = mysqli_fetch_assoc($result2)['total_quantidade_doada'] ?? 0;
$totalTrocados = mysqli_fetch_assoc($result3)['total_trocas'] ?? 0;
$produtosSobra = []; // Armazena dados para lista
while ($row = mysqli_fetch_assoc($result4)) {
    $produtosSobra[] = $row;
}
$categorias = [];
while ($row = mysqli_fetch_assoc($result5)) {
    $categorias[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Ecoeficiência</title>
    <link rel="stylesheet" href="relatorio.css">
</head>
<body>
    <div class="report-container">
        <h1>Relatórios</h1>
        <ul>
            <li>Pessoa que mais doou: <?php echo $maisDoaram['nome_usuario'] . " (" . $maisDoaram['total_doado'] . " doações)"; ?></li>
            <li>Quantidade total de produtos doados: <?php echo $totalDoacoes; ?></li>
            <li>Quantidade total de produtos trocados: <?php echo $totalTrocados; ?></li>
            <li>
                Quantidade de produtos em sobra:
                <ul>
                    <?php
                    foreach ($produtosSobra as $produto) {
                        echo "<li>" . $produto['categorias'] . ": " . $produto['sobra'] . "</li>";
                    }
                    ?>
                </ul>
            </li>
            <li>
                Quantidade de trocas por categoria:
                <ul>
                    <?php
                    foreach ($categorias as $categoria) {
                        echo "<li>" . $categoria['categorias'] . ": " . $categoria['total_trocas'] . " trocas</li>";
                    }
                    ?>
                </ul>
            </li>
        </ul>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
