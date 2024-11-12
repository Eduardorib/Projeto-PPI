CREATE TABLE anunciante
(
  id int PRIMARY KEY auto_incremen,
  nome varchar(50),
  cpf varchar(50) UNIQUE,
  email varchar(50) UNIQUE,
  senhaHash varchar(255),
  telefone varchar(50)
) ENGINE=InnoDB;


CREATE TABLE anuncio
(
   id int PRIMARY KEY auto_increment,
   marca varchar(50),
   modelo varchar(50),
   ano int,
   cor varchar(50),
   quilometragem int,
   descricao varchar(255),
   valor int,
   dataHora varchar(50),
   estado varchar(50),
   cidade varchar(50),
   idAnunciante int,
   FOREIGN KEY (idAnunciante) REFERENCES anunciante(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE interesse
(
   id int PRIMARY KEY auto_increment,
   nome varchar(50),
   telefone varchar(50),
   mensagem varchar(255),
   dataHora varchar(50),
   idAnuncio int,
   FOREIGN KEY (idAnuncio) REFERENCES anuncio(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE foto
(
  nomeArqFoto varchar(50) PRIMARY KEY,
  idAnuncio int,
  FOREIGN KEY (idAnuncio) REFERENCES anuncio(id) ON DELETE CASCADE
) ENGINE=InnoDB;
