USE cipa_t1;

DROP TABLE IF EXISTS branconulo;
DROP TABLE IF EXISTS candidato;
DROP TABLE IF EXISTS voto;
DROP TABLE IF EXISTS eleicao;
DROP TABLE IF EXISTS documento;
DROP TABLE IF EXISTS funcionario;

CREATE TABLE documento (
  id_documento INT NOT NULL AUTO_INCREMENT,
  data_hora_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  data_inicio_documento DATE,
  data_fim_documento DATE,
  obs_documento VARCHAR(255),
  pdf_documento VARCHAR(255),
  titulo_documento VARCHAR(100),
  tipo_documento ENUM('Edital','Ata'),
  PRIMARY KEY (id_documento)
);

INSERT INTO documento VALUES
(8,'2026-01-07 19:41:42','2026-01-07','2026-01-08',NULL,'../uploads/docsdoc_695eb6f6ed9bc.pdf','ATA 1','Ata');

CREATE TABLE eleicao (
  id_eleicao INT NOT NULL AUTO_INCREMENT,
  data_inicio_eleicao DATE,
  data_fim_eleicao DATE,
  status_eleicao VARCHAR(100),
  votacao_autorizada TINYINT(1) DEFAULT 0,
  documento_fk INT NOT NULL,
  PRIMARY KEY (id_eleicao),
  FOREIGN KEY (documento_fk) REFERENCES documento(id_documento) ON DELETE CASCADE
);

INSERT INTO eleicao VALUES
(3,'2026-01-07','2026-01-08','ABERTA',0,8);

CREATE TABLE funcionario (
  id_funcionario INT NOT NULL AUTO_INCREMENT,
  nome_funcionario VARCHAR(100) NOT NULL,
  sobrenome_funcionario VARCHAR(100) NOT NULL,
  cpf_funcionario CHAR(11) NOT NULL,
  data_nascimento_funcionario DATE,
  data_contratacao_funcionario DATE,
  telefone_funcionario CHAR(11),
  matricula_funcionario CHAR(8) NOT NULL,
  cod_voto_funcionario CHAR(6),
  ativo_funcionario TINYINT(1) DEFAULT 1,
  adm_funcionario TINYINT(1) DEFAULT 0,
  votou_ultima_eleicao TINYINT(1) DEFAULT 0,
  email_funcionario VARCHAR(100),
  senha_funcionario VARCHAR(255),
  PRIMARY KEY (id_funcionario),
  UNIQUE (cpf_funcionario),
  UNIQUE (matricula_funcionario),
  UNIQUE (email_funcionario),
  UNIQUE (cod_voto_funcionario)
);



INSERT INTO funcionario VALUES
(1,'Papa Leguas','Oliveira','12345678901','1985-05-20','2022-01-15','11988887777','MAT00001','VOT001',1,1,0,'ricardo.oliveira@empresa.com','hash_senha_segura_1'),
(2,'Beatriz','Souza','98765432100','1992-11-02','2023-03-10','21977776666','MAT00002','VOT002',1,0,0,'beatriz.souza@empresa.com','hash_senha_segura_2'),
(3,'Lucas','Mendes','45678912344','1998-07-15','2024-01-20','31966665555','MAT00003','VOT003',0,0,0,'lucas.mendes@empresa.com','hash_senha_segura_3'),
(4,'Aninha','Silva','23456789012','1990-03-12','2021-05-10','71999990001','MAT00004','VOT004',1,0,'ana.silva@empresa.com','senha_hash_4'),
(7,'Daniel','Santos','56789012345','1982-12-05','2019-02-20','71999990004','MAT00007','VOT007',1,0,'daniel.santos@empresa.com','senha_hash_7'),
(8,'Eduarda','Lima','67890123456','2000-09-18','2024-02-10','71999990005','MAT00008','VOT008',1,0,'eduarda.lima@empresa.com','senha_hash_8'),
(14,'William','dos Santos Ferreira','00011122299','1997-01-01','2025-10-10','71999999999','MAT12345','VOT099',1,0,'will.santos97@hotmail.com','741'),
(16,'Breno','Cirqueira','77777777700','2026-01-02','2026-01-05','71999999996','MAT12346','VOT222',1,1,'breno@email.com','123456'),
(17,'Zezo','Zezin','43062436134','1997-01-01','2026-01-07','71993295042','MAT44571','VOT069',0,0,'zezo@email.com','1'),
(19,'Joaozin','Silva','43062436178','1997-01-01','2026-01-14','71993295000','MAT44411','VOT158',1,1,'jjsilva@email.com','jjj');

CREATE TABLE candidato (
  id_candidato INT NOT NULL AUTO_INCREMENT,
  foto_candidato VARCHAR(255),
  numero_candidato CHAR(4) UNIQUE,
  cargo_candidato VARCHAR(100),
  data_registro_candidato DATE DEFAULT (CURDATE()),
  status_candidato VARCHAR(100),
  quantidade_voto_candidato INT DEFAULT 0,
  usuario_fk INT NOT NULL,
  eleicao_fk INT NOT NULL,
  PRIMARY KEY (id_candidato),
  FOREIGN KEY (usuario_fk) REFERENCES funcionario(id_funcionario),
  FOREIGN KEY (eleicao_fk) REFERENCES eleicao(id_eleicao) ON DELETE CASCADE
);

CREATE TABLE branconulo (
  id_branco_nulo INT NOT NULL AUTO_INCREMENT,
  quantidade_branco INT DEFAULT 0,
  quantidade_nulo INT DEFAULT 0,
  eleicao_fk INT NOT NULL,
  PRIMARY KEY (id_branco_nulo),
  FOREIGN KEY (eleicao_fk) REFERENCES eleicao(id_eleicao) ON DELETE CASCADE
);

CREATE TABLE voto (
  id_voto INT NOT NULL AUTO_INCREMENT,
  data_hora_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  usuario_fk INT,
  PRIMARY KEY (id_voto),
  FOREIGN KEY (usuario_fk) REFERENCES funcionario(id_funcionario) ON DELETE CASCADE
);

-- Comando para aplicar constraint UNIQUE em banco existente:
-- ALTER TABLE candidato ADD UNIQUE (numero_candidato);

