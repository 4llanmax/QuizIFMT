-- Insere a programação e as 48 perguntas fornecidas no modelo do jogo, sem jogadores fictícios.
-- Redator: Allan Max
-- Data de criação: 15/11/2026
-- Status: Sem revisão
-- Data do Status: 15/11/2026

SET NAMES utf8mb4;
START TRANSACTION;
INSERT INTO configuracao (identificador, conteudo, padrao) VALUES (1, '{"total": 20, "segundos": 45, "alerta": 10, "distribuicao": {"geografia": 4, "historia": 4, "ciencias": 3, "esportes": 3, "cinema": 3, "musica": 3}}', '{"total": 20, "segundos": 45, "alerta": 10, "distribuicao": {"geografia": 4, "historia": 4, "ciencias": 3, "esportes": 3, "cinema": 3, "musica": 3}}');

INSERT INTO perguntas (tema, enunciado, alternativas, correta) VALUES
('geografia', 'Qual é o maior bioma do Brasil em extensão territorial?', '["Cerrado", "Amazônia", "Mata Atlântica", "Caatinga", "Pampa"]', 1),
('geografia', 'Qual é a capital da Austrália?', '["Sydney", "Melbourne", "Camberra", "Perth", "Brisbane"]', 2),
('geografia', 'Qual é o maior oceano do planeta?', '["Atlântico", "Índico", "Pacífico", "Ártico", "Antártico"]', 2),
('geografia', 'Qual estado brasileiro tem a maior área territorial?', '["Pará", "Mato Grosso", "Amazonas", "Minas Gerais", "Bahia"]', 2),
('geografia', 'Qual é a montanha mais alta do mundo acima do nível do mar?', '["K2", "Everest", "Kilimanjaro", "Aconcágua", "Mont Blanc"]', 1),
('geografia', 'Em qual continente fica o Egito?', '["Ásia", "Europa", "África", "Oceania", "América"]', 2),
('geografia', 'Qual país tem a maior população da América do Sul?', '["Argentina", "Colômbia", "Brasil", "Peru", "Venezuela"]', 2),
('geografia', 'Qual é a capital do Canadá?', '["Toronto", "Vancouver", "Montreal", "Ottawa", "Quebec"]', 3),
('historia', 'Em que ano a esquadra de Pedro Álvares Cabral chegou ao Brasil?', '["1492", "1500", "1530", "1822", "1889"]', 1),
('historia', 'Quem proclamou a Independência do Brasil?', '["Dom Pedro I", "Dom Pedro II", "Tiradentes", "Deodoro da Fonseca", "Getúlio Vargas"]', 0),
('historia', 'Em que ano foi proclamada a República no Brasil?', '["1822", "1888", "1889", "1891", "1930"]', 2),
('historia', 'Quem assinou a Lei Áurea, que aboliu a escravidão no Brasil?', '["Dom Pedro II", "Princesa Isabel", "Deodoro da Fonseca", "José Bonifácio", "Dom João VI"]', 1),
('historia', 'Em que ano caiu o Muro de Berlim?', '["1961", "1979", "1989", "1991", "2001"]', 2),
('historia', 'Quem foi o primeiro ser humano a pisar na Lua?', '["Yuri Gagarin", "Buzz Aldrin", "Neil Armstrong", "Michael Collins", "John Glenn"]', 2),
('historia', 'Qual civilização construiu Machu Picchu?', '["Astecas", "Maias", "Incas", "Olmecas", "Toltecas"]', 2),
('historia', 'Em que ano terminou a Segunda Guerra Mundial?', '["1918", "1939", "1942", "1945", "1950"]', 3),
('ciencias', 'Qual planeta do Sistema Solar tem um dia mais longo que o seu próprio ano?', '["Mercúrio", "Vênus", "Marte", "Júpiter", "Netuno"]', 1),
('ciencias', 'Qual é o símbolo químico do ouro?', '["Ag", "Au", "Go", "Or", "Gd"]', 1),
('ciencias', 'Qual gás é o mais abundante na atmosfera da Terra?', '["Oxigênio", "Gás carbônico", "Nitrogênio", "Argônio", "Hidrogênio"]', 2),
('ciencias', 'Quantos ossos tem o corpo de um ser humano adulto?', '["106", "186", "206", "256", "306"]', 2),
('ciencias', 'Qual planeta é conhecido como Planeta Vermelho?', '["Vênus", "Marte", "Júpiter", "Saturno", "Mercúrio"]', 1),
('ciencias', 'Qual órgão do corpo humano produz a insulina?', '["Fígado", "Pâncreas", "Rim", "Estômago", "Baço"]', 1),
('ciencias', 'Qual é, aproximadamente, a velocidade da luz no vácuo?', '["300 km/s", "3 mil km/s", "30 mil km/s", "300 mil km/s", "3 milhões de km/s"]', 3),
('ciencias', 'Qual é a fórmula química da água?', '["CO₂", "H₂O", "O₂", "H₂O₂", "NaCl"]', 1),
('esportes', 'Quantas Copas do Mundo de futebol masculino o Brasil já venceu?', '["3", "4", "5", "6", "7"]', 2),
('esportes', 'Quantos jogadores de cada time ficam em quadra no vôlei?', '["5", "6", "7", "9", "11"]', 1),
('esportes', 'Qual cidade sediou os Jogos Olímpicos de 2016?', '["São Paulo", "Rio de Janeiro", "Londres", "Tóquio", "Pequim"]', 1),
('esportes', 'Quantos títulos mundiais de Fórmula 1 Ayrton Senna conquistou?', '["1", "2", "3", "4", "5"]', 2),
('esportes', 'Quantos pontos vale uma cesta feita de fora do arco no basquete?', '["1", "2", "3", "4", "5"]', 2),
('esportes', 'Qual é a duração do tempo regulamentar de uma partida de futebol?', '["60 minutos", "80 minutos", "90 minutos", "100 minutos", "120 minutos"]', 2),
('esportes', 'O torneio de Wimbledon é disputado em qual esporte?', '["Golfe", "Tênis", "Críquete", "Rúgbi", "Polo"]', 1),
('esportes', 'Qual país sediou a Copa do Mundo de futebol de 2014?', '["Alemanha", "África do Sul", "Brasil", "Rússia", "Catar"]', 2),
('cinema', 'Qual estúdio produziu a animação “Toy Story” (1995)?', '["DreamWorks", "Pixar", "Illumination", "Blue Sky", "Laika"]', 1),
('cinema', 'Em “O Rei Leão”, qual é o nome do pai de Simba?', '["Scar", "Mufasa", "Rafiki", "Timão", "Zazu"]', 1),
('cinema', 'Quem dirigiu o filme brasileiro “Central do Brasil” (1998)?', '["Fernando Meirelles", "Walter Salles", "Kleber Mendonça Filho", "José Padilha", "Glauber Rocha"]', 1),
('cinema', 'Qual filme venceu o Oscar de Melhor Filme na cerimônia de 2020?', '["1917", "Coringa", "Parasita", "Era uma vez em… Hollywood", "O Irlandês"]', 2),
('cinema', 'Quem criou a saga “Star Wars”?', '["Steven Spielberg", "George Lucas", "James Cameron", "Ridley Scott", "Christopher Nolan"]', 1),
('cinema', 'Qual é o nome do jovem bruxo protagonista dos livros de J. K. Rowling?', '["Gandalf", "Merlin", "Harry Potter", "Frodo", "Percy Jackson"]', 2),
('cinema', 'O filme “Cidade de Deus” (2002) se passa em qual cidade?', '["São Paulo", "Rio de Janeiro", "Salvador", "Recife", "Belo Horizonte"]', 1),
('cinema', 'Em que ano foi lançado o primeiro filme “Jurassic Park”?', '["1989", "1991", "1993", "1997", "2001"]', 2),
('musica', '“Garota de Ipanema” foi composta por Tom Jobim e…', '["Chico Buarque", "Vinicius de Moraes", "Caetano Veloso", "Gilberto Gil", "João Gilberto"]', 1),
('musica', 'Quantas notas tem a escala musical natural (dó a si)?', '["5", "6", "7", "8", "12"]', 2),
('musica', 'Qual instrumento tem, no modelo padrão, 88 teclas?', '["Órgão", "Acordeão", "Piano", "Cravo", "Teclado de 61 teclas"]', 2),
('musica', 'Luiz Gonzaga ficou conhecido como…', '["Rei do Baião", "Rei do Rock", "Rei da Voz", "Príncipe do Samba", "Poeta da Vila"]', 0),
('musica', 'Os Beatles surgiram em qual cidade inglesa?', '["Londres", "Manchester", "Liverpool", "Birmingham", "Leeds"]', 2),
('musica', 'Quantas cordas tem um violão comum?', '["4", "5", "6", "7", "12"]', 2),
('musica', 'Qual gênero musical nasceu na Jamaica e foi popularizado por Bob Marley?', '["Samba", "Reggae", "Blues", "Salsa", "Jazz"]', 1),
('musica', 'Quem compôs “As Quatro Estações”?', '["Mozart", "Bach", "Vivaldi", "Beethoven", "Chopin"]', 2);
COMMIT;
