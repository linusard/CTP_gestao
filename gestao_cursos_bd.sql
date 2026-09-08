CREATE DATABASE IF NOT EXISTS gestao_cursos;
USE gestao_cursos;

CREATE TABLE IF NOT EXISTS areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
    -- Ex: 1 = SAUDE, 2 = ESTETICA, 3 = GASTRONOMIA, 4 = TECNOLOGIA, 5 = OUTRO
);

CREATE TABLE IF NOT EXISTS turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(50) NOT NULL UNIQUE
    -- 1 = MANHA, 2 = TARDE, 3 = NOITE
);

CREATE TABLE IF NOT EXISTS status_turma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(50) NOT NULL UNIQUE
    -- 1 = PLANEJADO, 2 = INSCRICOES ABERTAS, 3 = EM ANDAMENTO, 4 = CONCLUIDO, 5 = CANCELADO
);

CREATE TABLE IF NOT EXISTS origens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
    -- Senac, Senac+, Psg, Prefeitura, Senar, Senai, Outro
);

CREATE TABLE IF NOT EXISTS dias_semana (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(20) NOT NULL UNIQUE
    -- Todos os dias da semana para previnir
);

CREATE TABLE IF NOT EXISTS locais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(250) NOT NULL UNIQUE
    -- CTP, Parque de exposição(Senar), Instituto Federal, Outro
);

CREATE TABLE IF NOT EXISTS cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    area_id INT NOT NULL, 
    carga_horaria INT NOT NULL,
    
    idade_minima INT DEFAULT 16,
    escolaridade_minima VARCHAR(100) DEFAULT 'Não pedido',
    requer_cpf BOOLEAN DEFAULT TRUE,
    requer_comprovante_residencia BOOLEAN DEFAULT FALSE,
    requer_comprovante_escolaridade BOOLEAN DEFAULT FALSE,
    
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (area_id) REFERENCES areas(id)
);

CREATE TABLE IF NOT EXISTS turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    numero_turma VARCHAR(50) DEFAULT NULL,
    origem_id INT NOT NULL,
    
    data_inicio DATE NOT NULL,
    data_termino DATE NOT NULL,
    turno_id INT NOT NULL,
    
    data_limite_inscricao DATE,
    local_inscricao_id INT,
    vagas_estimadas INT,
    link_sistema_inscricao VARCHAR(500),
    link_planilha_matricula VARCHAR(500),
    local_aula_id INT,
    
    status_id INT DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
    FOREIGN KEY (origem_id) REFERENCES origens(id),
    FOREIGN KEY (turno_id) REFERENCES turnos(id),
    FOREIGN KEY (status_id) REFERENCES status_turma(id),
    FOREIGN KEY (local_inscricao_id) REFERENCES locais(id),
    FOREIGN KEY (local_aula_id) REFERENCES locais(id)
);

CREATE TABLE IF NOT EXISTS turma_dias_semana (
    turma_id INT NOT NULL,
    dia_semana_id INT NOT NULL,
    
    PRIMARY KEY (turma_id, dia_semana_id),
    FOREIGN KEY (turma_id) REFERENCES turmas(id) ON DELETE CASCADE,
    FOREIGN KEY (dia_semana_id) REFERENCES dias_semana(id) ON DELETE CASCADE
);