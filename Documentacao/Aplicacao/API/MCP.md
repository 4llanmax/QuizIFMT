# MCP

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Descrever a conexão autenticada do MCP do BombQUIZ, a gestão do acervo e a consulta dos resultados. A API fornece respostas estruturadas; a página de autenticação pertence ao Painel. O envio dos arquivos permanece em [Hospedagem compartilhada](../../Implantacao/Hospedagem.md).

## Conexão

O endereço do MCP desta instalação é `https://maxmelo.com/mcp`. O cliente DEVE usar transporte HTTP e autenticação OAuth. O cliente descobre o servidor de autorização em `/.well-known/oauth-protected-resource` e `/.well-known/oauth-authorization-server`, relativos à URL base da instalação.

O cadastro do cliente usa `/oauth/registrar`. O cliente abre `/admin/mcp/autorizar` no navegador para a identificação do operador e a autorização da conexão. A emissão e a renovação de tokens usam `/oauth/token`; a revogação usa `/oauth/revogar`. O formulário apresenta o nome do cliente e o endereço de retorno antes do consentimento e recebe somente o e-mail e a senha definida para a conta.

A senha inicial permite autorizar a primeira conexão, obter tokens e renovar o acesso enquanto a conta permanecer ativa. O login DEVE preservar a senha definida para a conta; É PROIBIDO exigir sua substituição no fluxo de autorização. A conta do MCP é independente da conta de jogador e do acesso à programação do jogo. É PROIBIDO incluir credenciais e tokens em perguntas, arquivos versionados, chamadas de consulta ou respostas das ferramentas.

Para conectar, o operador DEVE cadastrar a URL do MCP no cliente que aceite servidor HTTP remoto com OAuth, abrir a página de autorização e concluir a identificação e o consentimento. Uma resposta 401 antes da autenticação apresenta a descoberta da autorização no cabeçalho `WWW-Authenticate`.

## Parâmetros privados

Os parâmetros ficam em `Configuracoes/Aplicacao/.env`, cuja gramática e precedência estão em [Inicialização web](../Nucleo/Inicializacao.md#gramática-dos-parâmetros).

| Nome externo → chave interna | Finalidade | Presença e proteção |
| --- | --- | --- |
| `MCP_URL_BASE` → `mcpURLBase` | URL pública HTTPS da instalação, sem o sufixo `/mcp` | OBRIGATÓRIO para habilitar o MCP; vazio mantém o atendimento desabilitado |
| `MCP_SENHA_INICIAL_HASH` → `mcpSenhaInicialHash` | Hash PHP da senha inicial fornecida pelo responsável para contas novas | Privado; É PROIBIDO gravar a senha em texto no modelo versionado |

O endereço público configurado, e não o cabeçalho `Host` recebido, determina os endereços da conexão. O operador DEVE habilitar o encaminhamento do cabeçalho `Authorization` no servidor de hospedagem.

## Perguntas e categorias

As ferramentas `listar_perguntas` e `obter_pergunta` consultam o acervo, incluindo gabarito, categoria e situação de arquivamento. `criar_perguntas` recebe de uma a cem perguntas em uma transação. `editar_pergunta` altera somente os campos informados. Cada pergunta DEVE conter enunciado, categoria existente, exatamente cinco alternativas e índice da alternativa correta de 0 a 4.

`arquivar_pergunta` recebe `arquivada=true` para impedir sorteio em novas rodadas e `arquivada=false` para restaurar a pergunta. `excluir_pergunta` remove o registro definitivamente e exige `confirmar=true`. O operador DEVE confirmar a exclusão definitiva antes da chamada. Perguntas já copiadas para partidas em andamento e resultados anteriores permanecem preservados.

As ferramentas `listar_categorias`, `criar_categoria`, `editar_categoria` e `excluir_categoria` administram as categorias. A chave identifica a categoria de forma estável; a edição modifica o nome de apresentação. Uma categoria só pode ser excluída quando estiver vazia e a chamada receber `confirmar=true`.

O sorteio usa somente perguntas ativas. A distribuição divide o total da rodada entre as categorias participantes; quando há resto, as perguntas adicionais são distribuídas por sorteio. Categorias sem perguntas ativas não entram na distribuição. A capacidade do acervo permanece sujeita à conferência do servidor antes de iniciar a partida.

## Consultas

| Ferramenta | Dados retornados |
| --- | --- |
| `listar_partidas` | Resultados encerrados, com filtro opcional por jogador |
| `obter_partida` | Resultado e desempenho por categoria de uma partida |
| `listar_jogadores` | Identificação dos jogadores, sem hashes de senha nem tokens |
| `consultar_estatisticas` | Totais, médias e capacidade do acervo |
| `consultar_ranking` | Até cem melhores resultados, um por jogador |
| `consultar_programacao` | Limites da rodada e capacidade das categorias |

As consultas paginadas usam `pagina` a partir de 1 e `limite` de 1 a 100, com padrão 50. Tempos de partidas são expressos em milissegundos. Não há ferramenta para executar SQL arbitrário nem para consultar senhas, tokens ou códigos de autorização.

## Usuários do MCP

`usuarios_listar`, `usuarios_criar` e `usuarios_desativar` exigem conta administradora. A criação recebe o e-mail e aplica a senha inicial correspondente ao hash privado configurado. A conta nova utiliza essa senha diretamente, sem troca obrigatória. `usuarios_desativar` exige o identificador e `confirmar=true`.

A conexão publica 19 ferramentas. Não há ferramenta nem campo público para o usuário alterar sua senha. A manutenção privada de uma senha pertence ao operador autorizado, DEVE armazenar somente o hash e revogar os códigos e tokens anteriores da conta. O campo `senha_inicial` permanece como informação sobre a origem da senha; É PROIBIDO usá-lo para bloquear autenticação, autorização, emissão ou renovação de tokens.

## Transporte e recusas

`MCP` atende a entrada HTTP, enquanto `Protocolo` interpreta JSON-RPC. O serviço exige Bearer válido em todas as chamadas, inclusive `initialize`. Quando presente, `Origin` DEVE corresponder à origem da URL base configurada. A conexão não cria sessão MCP persistente nem oferece fluxo SSE por GET; GET e DELETE autenticados recebem 405.

POST DEVE usar `Content-Type: application/json` e `Accept` com `application/json` e `text/event-stream`. O limite do corpo é 1 MiB. A resposta de uma requisição é JSON. Notificações e respostas JSON-RPC recebidas são aceitas com 202 e corpo vazio; uma notificação nunca executa ferramenta.

O servidor reconhece `initialize`, `ping`, `tools/list` e `tools/call`. As versões reconhecidas são `2025-11-25`, `2025-06-18` e `2025-03-26`. A negociação responde com a versão solicitada quando reconhecida; caso contrário, apresenta `2025-11-25`. Uma versão inválida no cabeçalho `MCP-Protocol-Version` produz 400.

Origem recusada produz 403; token ausente ou recusado, 401; formato de conteúdo inadequado, 415; formatos de resposta não aceitos, 406; mensagem excessiva, 413. JSON inválido e envelope JSON-RPC inválido produzem 400. Método desconhecido e parâmetros inválidos usam erros JSON-RPC. Recusas de negócio usam resultado de ferramenta com `isError=true`. Falhas técnicas usam mensagem genérica, sem detalhes de conexão ou pilha de execução.

O contrato segue os documentos oficiais de [transporte](https://modelcontextprotocol.io/specification/2025-11-25/basic/transports), [ciclo de vida](https://modelcontextprotocol.io/specification/2025-11-25/basic/lifecycle), [autorização](https://modelcontextprotocol.io/specification/2025-11-25/basic/authorization) e [ferramentas](https://modelcontextprotocol.io/specification/2025-11-25/server/tools) do MCP. Não há dependência externa nova para interpretar o protocolo.

## Verificação

Os cenários do transporte estão em `Testes/Unitario/API/MCPTeste.php`. Eles conferem autenticação obrigatória, origem, formatos, limites, negociação, envelopes, notificações sem efeitos, chamadas de ferramentas e ocultação de detalhes técnicos.

A verificação do transporte com dependências controladas não comprova a configuração HTTPS, o encaminhamento de `Authorization`, a página real do provedor nem a autorização de um cliente externo. O responsável pela implantação DEVE conferir a conexão completa no endereço hospedado antes de declarar sua disponibilidade.
