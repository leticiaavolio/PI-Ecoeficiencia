<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Programa Ecoeficiência</title>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <img src="imgs/ecoeficiencia.png" alt="Programa Ecoeficiência" class="logo">
            <nav>
                <ul>
                    <li><a href="?page=login">Login</a></li>
                    <li><a href="?page=cadastro">Cadastro de Usuários</a></li>
                    <li><a href="?page=doacao">Cadastro de Doação</a></li>
                    <li><a href="?page=troca">Troca</a></li>
                    <li><a href="?page=verSaldo">Visualizar Saldo</a></li>
                    <li><a href="?page=relatorio">Relatórios</a></li>
                    <li><a href="?page=logout">Sair</a></li>
                </ul>
            </nav>
        </div>
        <div class="main-content">
            <?php
            $page = isset($_GET['page']) ? $_GET['page'] : 'login';
            include "$page.php";
            ?>
        </div>
    </div>
</body>
</html>
