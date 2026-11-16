# Jogo

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Descrever o funcionamento do BombQUIZ, seus contratos HTTP, dados e controles de acesso. A configuração compartilhada está em [Inicialização web](../Nucleo/Inicializacao.md).

## Fluxo e regras

O jogador escolhe um nome de convidado ou entra em uma conta e confere a distribuição antes de começar. A programação padrão tem 20 perguntas, 45 segundos por pergunta, alerta nos últimos 10 segundos e distribuição de quatro perguntas de Geografia, quatro de História e três de cada um dos outros quatro temas.

O sorteio não repete perguntas na rodada e embaralha as cinco alternativas. Gabarito, perguntas futuras, identificação da partida e prazo ficam no servidor. O navegador recebe somente a pergunta atual. O gabarito dessa pergunta é apresentado depois da resposta ou do encerramento.

Um erro ou o prazo esgotado encerra a partida. O prazo exato já está expirado. A resposta correta soma um acerto e o intervalo entre a emissão e o recebimento da resposta, incluindo a comunicação. Erros, abandono e tempo esgotado não acrescentam tempo à pontuação. Após um acerto, o jogador confirma a próxima pergunta; esse intervalo de confirmação não integra a pontuação. A última resposta correta encerra a rodada com vitória.

Recarregar ou abrir outra aba não reinicia o prazo. Uma nova solicitação de início retoma a partida ainda ativa do mesmo jogador. Identificador e índice impedem respostas repetidas e formulários antigos sobre partidas novas. Uma operação posterior confere a expiração; não há trabalho permanente em segundo plano.

O resultado é gravado somente no encerramento. Sua chave única impede duplicação ao recarregar a página. Ao trocar de jogador ou sair da conta, uma partida ativa anterior é encerrada para seu proprietário original.

## Contas e apelidos

Convidados usam de 2 a 16 caracteres: letras, números, espaço, ponto, sublinhado ou hífen. Contas usam de 3 a 16 letras, números, ponto ou sublinhado. Espaços consecutivos de convidados são reduzidos. A comparação remove diferenças de caixa e acentos.

Um convidado não usa nome de uma conta. Uma conta não usa nome de outra conta nem nome de outro convidado no Top 100. A reserva usa transação e bloqueio do registro de configuração para serializar cadastros e mudanças. Convidados de sessões diferentes mantêm identidades distintas, mesmo com o mesmo nome.

O e-mail tem no máximo 254 bytes e formato válido. A senha tem de 8 a 72 bytes, sem byte nulo. O banco recebe somente o resultado de `password_hash`; a entrada compara com `password_verify`. A mensagem de entrada não distingue conta ausente de senha incorreta.

O cadastro de convidado vincula à conta somente seu último resultado persistido. Criar outra conta enquanto uma conta está ativa não transfere o histórico desta. Trocar o apelido mantém a identidade e os resultados. A exclusão exige a senha e remove a conta com seus resultados em transação.

## Ranking e histórico

O ranking público contém o melhor resultado de até 100 jogadores. A ordem é acertos decrescentes, tempo crescente, data crescente e identificador da partida. Uma conta recebe indicação de registro; e-mail, hash e token de convidado nunca integram a saída pública.

O recorde é consultado independentemente do Top 100. O painel mostra as últimas 300 partidas, evolução das dez últimas, seis registros recentes, média de acertos, tempo médio de respostas corretas e desempenho por tema dentro desse histórico. A programação não modifica resultados anteriores.

## Operações HTTP

Os caminhos são relativos a `URL_PREFIXO`. GET recebe somente o caminho. POST usa campos de formulário e exige `csrf`. Sucesso de POST produz HTTP 303, sem reenviar o formulário.

| Método e caminho | Entrada adicional | Acesso e resultado |
| --- | --- | --- |
| GET `/` | Nenhuma | Início e cinco melhores resultados |
| GET `/como-funciona`, `/privacidade` | Nenhuma | Regras e tratamento dos dados |
| POST `/identificar` | `nome` | Identifica convidado e direciona à rodada |
| GET `/rodada` | Nenhuma | Preparação; sem jogador, volta ao início |
| POST `/jogar` | Nenhuma | Inicia ou retoma a partida do jogador |
| GET `/jogo` | Nenhuma | Pergunta, confirmação de acerto ou resultado; confere expiração |
| POST `/responder` | `partida`, `indice` de 0 a 47, `alternativa` de 0 a 4 | Confere pergunta atual e prazo |
| POST `/avancar`, `/abandonar`, `/expirar` | `partida` | Altera somente a partida correspondente à sessão |
| GET `/ranking` | Nenhuma | Top 100 e posição do jogador quando presente |
| GET `/entrar`, `/cadastro` | Nenhuma | Formulários de conta |
| POST `/entrar` | `email`, `senha` | Autentica e renova sessão e CSRF |
| POST `/cadastro` | `nome`, `email`, `senha` | Cria conta e renova sessão e CSRF |
| GET `/conta` | Nenhuma | Exige conta existente; mostra histórico |
| POST `/apelido` | `nome` | Exige conta; altera somente seu apelido |
| POST `/excluir-conta` | `senha` | Exige conta e confirmação da senha |
| POST `/sair` | Nenhuma | Encerra partida ativa e remove acesso |

O identificador da partida é um texto aleatório de 32 dígitos hexadecimais emitido pelo servidor. O índice inicia em zero. Os controles não aceitam acertos, tempo ou gabarito fornecidos pelo cliente.

## Recusas e repetição

HTTP 403 representa CSRF inválido ou operação de conta sem acesso. HTTP 404 representa caminho inexistente; HTTP 405, método não associado. HTTP 409 informa ausência de partida na sessão. Entradas inválidas, nome reservado, senha incorreta e transições indevidas produzem HTTP 422. Falhas técnicas produzem HTTP 503.

Entrada e cadastro compartilham dez tentativas por origem de conexão em 15 minutos, persistidas independentemente da sessão. A tentativa seguinte recebe HTTP 429. A origem vem de `REMOTE_ADDR`; cabeçalhos do cliente não a substituem.

Repetir a gravação não cria novo resultado. Repetir resposta ou avançar sem acerto é recusado. A exclusão retira os registros definitivamente; recuperação depende do backup do ambiente.

## Dados e responsabilidades

Os manipuladores do Site usam `jogadores`, `perguntas`, `partidas` e a leitura de `configuracao`. `tentativas` pertence ao controle técnico compartilhado de acesso. Esquema e carga estão em [MariaDB](../../Ambiente/MariaDB.md).

O tratamento usa nome público e resultados; conta acrescenta e-mail e hash. Sessões contêm identidade e partida. A preferência de som é o único dado mantido em armazenamento local do navegador. Não há serviço externo de fontes, ícones ou análise.

Resultados permanecem no banco até a exclusão. O limite de 300 é de consulta, não uma limpeza física. A conta permite consultar, corrigir o apelido e excluir os dados. Pedidos de convidados sem sessão, fundamento do tratamento, responsável pelo atendimento, retenção de backups e procedimentos administrativos DEVEM ser definidos pela administração da produção antes da abertura pública; os testes não substituem esses procedimentos.

A instalação usa manifesto e ícone locais. O jogo exige comunicação com o servidor; páginas autenticadas, respostas e gabaritos não recebem cache de serviço de navegador. Recuperação de senha por e-mail não integra este contrato.

## Verificação

Cenários, métricas e limites estão em [Testes do jogo](../../Testes/Integracao/Jogo/TestesJogo.md). Os ativos seguem o procedimento de [Ativos](../../Ferramentas/Ativos.md).
