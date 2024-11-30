<?php

include 'conexao.php';

session_start();


// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pega o email e a senha do formulário
    $login = strtoupper($_POST['login']);
    $senha = $_POST['senha'];

   
    // Proteção contra injeção SQL
    $email = $conn->real_escape_string($login);
    $senha = $conn->real_escape_string($senha);

    // Query para verificar o email
    $sql = "SELECT * FROM usuarios WHERE email = '$login'";
    $result = $conn->query($sql);

    // Verifica se a consulta retornou algum resultado (ou seja, se o usuário foi encontrado no banco de dados)
    if ($result->num_rows > 0) {
        // Usuário encontrado no banco de dados
        $usuario = $result->fetch_assoc();

        // Verifica se a senha fornecida corresponde ao hash no banco de dados
        if (password_verify($senha, $usuario['senha'])) {
        // A função password_verify() verifica se a senha fornecida ($senha) corresponde ao hash armazenado no banco ($usuario['senha'])
        // Se a senha estiver correta, entra no bloco de código abaixo para redirecionar o usuário conforme o perfil

            // A senha está correta, redireciona conforme o perfil
            switch ($usuario['perfil']) {
                case 'colaborador':

                    // Armazena as informações do usuário na sessão
                    $_SESSION['usuario_id'] = ($usuario['idUsuario']);
                    $_SESSION['perfil'] = ($usuario['perfil']);
                    $_SESSION['login'] = ($usuario['email']);
                    // Redireciona o usuário para a página de doação
                    header("Location: index.php?page=doacao"); 
                     // Interrompe a execução do script para garantir que o redirecionamento ocorra imediatamente
                    exit();
                    
                case 'adm':
                    $_SESSION['usuario_id'] = ($usuario['idUsuario']); // Armazena o ID do usuário na sessão
                    $_SESSION['perfil'] = ($usuario['perfil']);// Armazena o perfil do usuário na sessão
                    $_SESSION['login'] = ($usuario['email']); // Armazena o email (login) do usuário na sessão
                    header("Location: index.php?page=relatorio"); 
                    exit();
                case 'doador':
                    
                    $_SESSION['usuario_id'] = ($usuario['idUsuario']);
                    $_SESSION['perfil'] = ($usuario['perfil']);
                    $_SESSION['login'] = ($usuario['email']);
                    header("Location: index.php?page=verSaldo");                     
                    exit();
                default:
                    echo "Perfil de usuário desconhecido.";
                    exit();                    
            }

           
        } else {
            echo "E-mail ou senha inválidos.";
        }
    } else {
        echo "E-mail ou senha inválidos.";
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="forms.css">

    <title>Login Colaboradores</title>
</head>

<body>

    <div class="form-container">
        <h2>Login</h2>

        <form method="post">
            <label for="login"></label>
            <input placeholder="Email" id="login" name="login" required>
            <label for="senha"></label>
            <input type="password" placeholder="Senha" id="senha" name="senha" required>
            <button type="submit">Entrar</button>
        </form>

    </div>

    </div>
</body>


</html>