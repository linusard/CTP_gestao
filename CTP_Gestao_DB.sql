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

INSERT INTO areas (nome) VALUES 
    ('SAUDE'), 
    ('ESTETICA'), 
    ('GASTRONOMIA'), 
    ('TECNOLOGIA'), 
    ('OUTRO');

INSERT INTO turnos (descricao) VALUES 
    ('MANHA'), 
    ('TARDE'), 
    ('NOITE');

INSERT INTO status_turma (descricao) VALUES 
    ('PLANEJADO'), 
    ('INSCRICOES ABERTAS'), 
    ('EM ANDAMENTO'), 
    ('CONCLUIDO'), 
    ('CANCELADO');

INSERT INTO origens (nome) VALUES 
    ('Senac'), 
    ('Senac+'), 
    ('Psg'), 
    ('Prefeitura'), 
    ('Senar'), 
    ('Senai'), 
    ('Outro');

INSERT INTO dias_semana (nome) VALUES 
    ('Domingo'),
    ('Segunda-feira'), 
    ('Terça-feira'), 
    ('Quarta-feira'), 
    ('Quinta-feira'), 
    ('Sexta-feira'), 
    ('Sábado');

INSERT INTO locais (nome) VALUES 
    ('CTP'), 
    ('Parque de exposição(Senar)'), 
    ('Instituto Federal'), 
    ('Outro');

INSERT INTO cursos (nome, area_id, carga_horaria, idade_minima, escolaridade_minima, requer_cpf, requer_comprovante_residencia, requer_comprovante_escolaridade) VALUES 
    ('Curso de Cuidador de idosos', 1, 200, 18, 'Ensino Médio Completo', TRUE, TRUE, TRUE),
    ('Curso de Maquiagem Básica', 2, 150, 16, 'Ensino Fundamental Completo', TRUE, FALSE, FALSE),
    ('Curso de Bombons e trufas', 3, 300, 18, 'Ensino Médio Completo', TRUE, TRUE, FALSE),
    ('Curso de Implementação de loja virtual', 4, 250, 16, 'Ensino Médio Incompleto', TRUE, FALSE, FALSE),
    ('Curso de Patchwork', 5, 100, 16, 'Não pedido', FALSE, FALSE, FALSE);

INSERT INTO turmas (curso_id, numero_turma, origem_id, data_inicio, data_termino, turno_id, data_limite_inscricao, local_inscricao_id, vagas_estimadas, link_sistema_inscricao, link_planilha_matricula, local_aula_id, status_id) VALUES 
    (1, 'TURMA 01', 1, '2024-07-01', '2024-12-31', 1, '2024-06-15', 1, 30, 'https://sistema-inscricao.com/cuidador-de-idosos', 'https://planilha-matricula.com/cuidador-de-idosos', 1, 2),
    (2, 'TURMA 02', 2, '2024-08-01', '2024-11-30', 2, '2024-07-15', 2, 25, 'https://sistema-inscricao.com/maquiagem-basica', 'https://planilha-matricula.com/maquiagem-basica', 2, 2),
    (3, 'TURMA 03', 3, '2024-09-01', '2025-02-28', 3, '2024-08-15', 3, 20, 'https://sistema-inscricao.com/bombons-e-trufas', 'https://planilha-matricula.com/bombons-e-trufas', 3, 2),
    (4, 'TURMA 04', 4, '2024-10-01', '2025-03-31', 1, '2024-09-15', 4, 15, 'https://sistema-inscricao.com/loja-virtual', 'https://planilha-matricula.com/loja-virtual', 4, 2),
    (5, 'TURMA 05', 5, '2024-11-01', '2025-04-30', 2, '2024-10-15', NULL, NULL, NULL, NULL, NULL);

INSERT INTO turma_dias_semana (turma_id, dia_semana_id) VALUES 
    (1, 2), -- Segunda-feira
    (1, 4), -- Quarta-feira
    (2, 3), -- Terça-feira
    (2, 5), -- Quinta-feira
    (3, 6), -- Sexta-feira
    (4, 2), -- Segunda-feira
    (4, 4), -- Quarta-feira
    (5, 3); -- Terça-feira