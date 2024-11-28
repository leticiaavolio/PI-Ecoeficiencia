CREATE DATABASE bd_Ecoeficiencia;

USE bd_Ecoeficiencia;

CREATE TABLE usuarios (
idUsuario INT AUTO_INCREMENT NOT NULL,
nome VARCHAR(100) NOT NULL,
email VARCHAR(120) NOT NULL UNIQUE,
senha VARCHAR(4) NOT NULL,
perfil VARCHAR(20) NOT NULL,
coin INT NOT NULL,
CONSTRAINT PRIMARY KEY(idUsuario)
);

CREATE TABLE produtos(
idProduto INT AUTO_INCREMENT NOT NULL,
coin INT NOT NULL,
categorias VARCHAR(40) NOT NULL,
quantidade INT NOT NULL,
CONSTRAINT PRIMARY KEY(idProduto)
);

CREATE TABLE doacao(
idDoacao INT AUTO_INCREMENT NOT NULL,
idUsuario INT NOT NULL,
CONSTRAINT PRIMARY KEY(idDoacao),
CONSTRAINT fk_doacao_usuarios FOREIGN KEY(idUsuario) REFERENCES usuarios(idUsuario)
);

CREATE TABLE troca(
idTroca INT AUTO_INCREMENT NOT NULL, 
idUsuario INT NOT NULL,
CONSTRAINT PRIMARY KEY(idTroca),
CONSTRAINT fk_troca_usuarios FOREIGN KEY(idUsuario) REFERENCES usuarios(idUsuario)
);

CREATE TABLE doacaoProduto (
idDoacao INT NOT NULL,
idProduto INT NOT NULL,
quantidade INT NOT NULL,
CONSTRAINT fk_doacaoProduto_doacao FOREIGN KEY (idDoacao) REFERENCES doacao (idDoacao),
CONSTRAINT fk_doacaoProduto_produtos FOREIGN KEY (idProduto) REFERENCES produtos (idProduto)
);

CREATE TABLE trocaProduto (
idTroca INT NOT NULL,
idProduto INT NOT NULL,
quantidade INT NOT NULL,
CONSTRAINT fk_trocaProduto_troca FOREIGN KEY (idTroca) REFERENCES troca (idTroca),
CONSTRAINT fk_trocaProduto_produtos FOREIGN KEY (idProduto) REFERENCES produtos (idProduto)
);