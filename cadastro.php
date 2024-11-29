<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Captura os dados do formulário
    $nome = $_POST['nome'];
    $login = $_POST['login'];
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil']; 

    // Depuração: Verifique se os dados estão sendo recebidos corretamente
    // echo "Nome: $nome, Email: $email, Senha: $senha"; exit;

    // Verifique se a senha tem exatamente 4 dígitos e é numérica
    if (strlen($senha) === 4 && ctype_digit($senha)) {
        // Criptografar a senha
        $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

        // Usar uma consulta preparada para evitar SQL Injection
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", strtoupper($nome), strtoupper($login), $hashed_password, $perfil);
            
            // Execute a consulta e verifique se foi bem-sucedida
            if ($stmt->execute()) {
                /* echo "Cadastro realizado com sucesso!"; */

                //Aqui, garantimos que o redirecionamento só ocorra após a execução com sucesso
                header('Location: index.php?page=cadastro&msg=OK');
                exit;  /// Garanta que o script seja interrompido após o redirecionamento */
            } else {
                // Exibe um erro se a execução falhar
                echo "Erro na execução: " . $stmt->error;
            }

            // Fecha a declaração
            $stmt->close();
        } else {
            echo "Erro na preparação da declaração: " . $conn->error;
        }
    } else {
        // Se a senha não for válida, exiba uma mensagem de erro
        echo "A senha deve ter exatamente 4 dígitos numéricos.";
    }
}
?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="forms.css">
    <title>Cadastrar Usuário</title>
</head>

<body>
    <div class="form-container">

        <h2>Cadastro</h2>


        <form action="cadastro.php" method="post">
            <select name = "perfil">
                <option selected disabled value="">Selecionar Usuário</option>
                <option value="colaborador">Colaboradores</option>
                <option value="adm">Adm</option>
                <option value="doador">Doador</option>
            </select>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="login">Login:</label>
            <input placeholder= "Nome mais iniciais do Sobrenome Ex Paulo.sca" id="login" name="login" required>
            <label for="senha">Senha:</label>
            <input  placeholder= "Senha númerica, apenas 4 dígitos"  type="password" id="senha" name="senha" required>
            <button type="submit">Cadastrar</button>

        </form>

    </div>
</body>

</html>