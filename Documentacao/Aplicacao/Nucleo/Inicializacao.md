# Inicialização web

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Descrever configuração, conexão, sessão, seleção de área e renderização compartilhadas. O envio dos arquivos está em [Hospedagem compartilhada](../../Implantacao/Hospedagem.md).

## Entrada e composição

A entrada dinâmica é `Publico/index.php`. Ela registra o autocarregamento PSR-4 e fornece as descrições do Site e do Painel. `EntradaHTTP` prepara a sessão nativa e emite a resposta. `Inicializacao` lê parâmetros, abre a conexão e aciona `Web`.

`Web` seleciona a primeira descrição cujo prefixo corresponde a um segmento completo do caminho. A descrição de raiz é o destino padrão e DEVE ficar por último. As associações de método e caminho pertencem aos arquivos de `Rotas` das áreas. `RoteadorHTTP` encaminha corpo e contexto HTTP para integrações com autenticação própria, enquanto `Roteador` atende os formulários protegidos pelo CSRF comum. As integrações são responsáveis por validar método, origem, autenticação e formato antes de executar suas operações.

O autocarregamento associa exclusivamente `Aplicacao\` à árvore `Aplicacao`. As declarações de testes recebem registro independente em `Testes/Apoio/Preparacao.php`. A entrada lê no máximo 1 MiB mais um byte do corpo bruto, permitindo que o transporte recuse excesso antes de interpretar JSON. Os parâmetros específicos do MCP estão em [MCP](../API/MCP.md#parâmetros-privados).

## Gramática dos parâmetros

O leitor recebe o caminho explícito do arquivo e as variáveis do processo. O arquivo DEVE usar UTF-8 sem BOM e uma atribuição por linha no formato `CHAVE=valor`. Linhas vazias e iniciadas por `#` são ignoradas. Espaços nas extremidades são retirados. Aspas simples ou duplas correspondentes nas extremidades são removidas; aspas sem fechamento permanecem literais. Não há interpolação, escapes ou comentários ao final de valores. A última atribuição da chave no arquivo prevalece.

A precedência é processo, arquivo e padrão. Variáveis do processo não reconhecidas são ignoradas. Chaves desconhecidas no arquivo e linhas sem atribuição válida provocam indisponibilidade sem expor o conteúdo.

Parâmetros específicos são registrados pelas áreas por descrições explícitas. O Núcleo faz a leitura técnica e não define parâmetros administrativos. O registro do Painel está em [Programação](../Painel/Programacao.md#parâmetro-administrativo).

## Parâmetros comuns

Todos os parâmetros recebem valores do processo ou de `Configuracoes/Aplicacao/.env`. Os valores de `Exemplo.env` são fictícios.

| Nome externo → chave interna | Finalidade | Tipo e validação | Presença e padrão | Sigilo e valor do modelo |
| --- | --- | --- | --- | --- |
| `BANCO_HOST` → `bancoHost` | Endereço do MariaDB | Texto com letras ASCII, números, ponto, sublinhado, dois-pontos ou hífen | OPCIONAL; `127.0.0.1` | Interno; `127.0.0.1` |
| `BANCO_PORTA` → `bancoPorta` | Porta TCP | Inteiro de 1 a 65535 | OPCIONAL; 3306 | Interno; 3306 |
| `BANCO_NOME` → `bancoNome` | Banco previamente criado | Texto não vazio com letras ASCII, números ou sublinhado | OBRIGATÓRIO; sem padrão utilizável | Interno; `quizifmt`, substituir pelo nome autorizado |
| `BANCO_USUARIO` → `bancoUsuario` | Identidade de conexão | Texto não vazio | OBRIGATÓRIO; sem padrão utilizável | Interno; `usuario_quiz`, substituir |
| `BANCO_SENHA` → `bancoSenha` | Segredo da conexão | Texto literal, inclusive vazio quando o serviço permite | OPCIONAL; vazio | Secreto; vazio no modelo |
| `URL_PREFIXO` → `urlPrefixo` | Prefixo público dos links e formulários | Caminho iniciado e terminado por barra; segmentos com letras ASCII, números, sublinhado ou hífen; sem endereço externo | OPCIONAL; `/` | Público; `/` |

`obter` recebe somente uma chave interna reconhecida. O caminho da configuração tem como referência a pasta acima da entrada pública; não depende do diretório corrente do terminal.

## Conexão e falhas

A conexão usa PDO, driver `pdo_mysql`, `utf8mb4`, exceções e instruções preparadas nativas. O prazo de conexão configurado é de cinco segundos. Ela não cria bancos nem aplica SQL automaticamente. As áreas recebem a conexão pronta e mantêm seus próprios manipuladores e contratos.

Falhas de inicialização e persistência produzem HTTP 503 com mensagem genérica. O log técnico usa `error_log` do PHP e registra a categoria da falha, sem credenciais, SQL ou entrada do jogador. O destino efetivo, o acesso e a retenção desse armazenamento externo DEVEM ser identificados pela administração da hospedagem.

## Sessão e proteção HTTP

A sessão se chama `QUIZIFMT`, usa identificação estrita e cookie de sessão com `HttpOnly`, `SameSite=Lax` e caminho `/`. Em HTTPS, o cookie também usa `Secure`. O identificador é renovado após entrada, saída e exclusão; a identidade de uma conta excluída é revogada na próxima operação de outras sessões.

O atendimento exige HTTPS, exceto para origem local `127.0.0.1` ou `::1`. Cabeçalhos de procuradores reversos não substituem a informação do servidor. A hospedagem DEVE informar HTTPS ao PHP corretamente.

Sem persistência da sessão, a entrada recusa o atendimento com HTTP 503. O prazo e a conservação dos arquivos de sessão seguem a configuração do PHP do ambiente. Cada POST exige token CSRF aleatório correspondente à sessão. Token ausente, de tipo incorreto ou divergente produz HTTP 403.

Respostas dinâmicas usam UTF-8, `Cache-Control: no-store`, proteção contra incorporação em quadros, `nosniff` e política de conteúdo restrita à própria origem. Estilos em linha são permitidos para valores de apresentação; scripts em linha não são permitidos.

O controle técnico de acesso depende de `ContagemAcessos`. A implementação de banco é fornecida pelos provedores; o serviço não depende do manipulador concreto. A normalização do endereço mantém somente o prefixo público como estado técnico e não contém catálogo de ativos de uma área.

## Renderização

As visões retornam closures que recebem dados explícitos, escape HTML e inclusão de componentes. `Renderizacao` reutiliza a closure de cada visão na sua instância. As visões não consultam serviços ou banco.

O escape usa `ENT_QUOTES | ENT_SUBSTITUTE`. O HTML é minificado pela remoção de espaços entre tags. Somente caminhos escolhidos internamente pelos controles ou pelas estruturas são incluídos.
