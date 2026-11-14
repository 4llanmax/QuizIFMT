# Estrutura

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 12/11/2026     |

## Objetivo

Este documento apresenta o mapa de pastas e o destino de cada tipo de conteúdo. Nomes seguem [Padrões](01Padroes.md). A criação de regras gerais novas e as adaptações às regras existentes seguem [Ampliações & Adaptações](00Regras.md#ampliações--adaptações).

## Em resumo

- O mapa mostra os diretórios e arquivos estruturais; `[+]` permite incluir subpastas nas condições definidas, e `[*]` identifica os nomes que admitem alteração com prefixo preservado. Veja [Mapa geral](#mapa-geral).
- API e Painel admitem mudança de nome com prefixo preservado somente quando houver necessidade de coexistirem duas ou mais áreas do mesmo tipo. Veja [Áreas](#áreas).
- O Núcleo recebe somente o que é comum a todas as áreas a que se destina. Veja [Nucleo](#nucleo).
- Alterar uma área não obriga a reescrever outra área nem o Núcleo. Veja [Independência entre áreas](#independência-entre-áreas).
- Dentro de cada área e do Núcleo, o conteúdo é distribuído pelo catálogo de divisões. Veja [Divisões de responsabilidade](#divisões-de-responsabilidade).
- `Publico` é a raiz do servidor web, e os ativos ficam em quatro famílias. Veja [Publico](#publico).
- Scripts de banco ficam em `Dados/Esquemas` e `Dados/Cargas`, por gerenciador. Veja [Esquemas e cargas](#esquemas-e-cargas).

## Mapa geral

O mapa abaixo apresenta os diretórios e arquivos dos níveis de organização do projeto, incluindo API e Painel com seus nomes iniciais. As demais áreas e subpastas criadas dentro das permissões deste documento não são relacionadas no mapa. As pastas e os arquivos acrescentados por ferramentas de ambiente também não fazem parte do mapa; ficam definidos no documento de cada ferramenta, conforme [Ferramentas de ambiente](#ferramentas-de-ambiente). O estado de implementação dos componentes está em [Estado atual](Memorial.md#estado-atual).

A marca `[+]`, escrita depois de uma pasta, indica que é PERMITIDO acrescentar subpastas nela sem atualizar o mapa, as tabelas ou as demais definições deste documento, desde que sua finalidade e localização cumpram as condições da seção responsável. A marca é uma anotação do desenho e não integra o nome da pasta. É PROIBIDO interpretá-la como permissão para qualquer conteúdo ou para criar pastas antes das condições exigidas. A ausência da marca indica que este documento não permite acrescentar subpastas diretamente naquele destino.

```text
Aplicacao/ [+]
├── API/ [+] [*]
├── Comandos/ [+]
├── Nucleo/ [+]
│   └── Comuns/ [+]
│       └── Funcoes/ [+]
│           └── AutoCarregamento.php
├── Painel/ [+] [*]
└── Site/ [+]
Configuracoes/ [+]
└── Aplicacao/
    └── Exemplo.env
Dados/
├── Arquivos/ [+]
│   ├── BancoDados/ [+]
│   ├── Cache/ [+]
│   ├── Logs/ [+]
│   └── Uploads/ [+]
├── Cargas/ [+]
│   └── SQLite/
└── Esquemas/ [+]
    └── SQLite/
Documentacao/ [+]
├── 00Regras.md
├── 01Padroes.md
├── 02Estrutura.md
├── 03Configuracoes.md
├── 04Ferramentas.md
├── 05Externos.md
├── Memorial.md
├── Adaptacoes/
├── Ambiente/
├── Aplicacao/ [+]
│   ├── API/ [*]
│   ├── Comandos/
│   ├── Nucleo/
│   ├── Painel/ [*]
│   └── Site/
├── Externos/
├── Ferramentas/
├── Publico/
│   ├── Ativos/ [+]
│   └── Externos/
└── Testes/
    ├── Apoio/ [+]
    ├── Integracao/ [+]
    └── Unitario/ [+]
Externos/ [+]
Ferramentas/ [+]
Publico/
├── index.php
└── Ativos/
    ├── CSS/ [+]
    ├── Fontes/ [+]
    ├── Imagens/ [+]
    └── JavaScript/ [+]
Testes/
├── Apoio/ [+]
├── Integracao/ [+]
└── Unitario/ [+]
```

As permissões indicadas por `[+]` têm os seguintes limites:

| Destino marcado | Definição responsável pela inclusão de subpastas |
| --- | --- |
| `Aplicacao` | Novas áreas, conforme [Áreas](#áreas) |
| `Aplicacao/API` | Catálogo de [Divisões de responsabilidade](#divisões-de-responsabilidade) |
| `Aplicacao/Comandos` | Catálogo de [Divisões de responsabilidade](#divisões-de-responsabilidade), exceção de Comandos em [Áreas](#áreas) e organização por assunto em [Subpastas e espaços de nomes](#subpastas-e-espaços-de-nomes) |
| `Aplicacao/Nucleo` | Catálogo de [Divisões de responsabilidade](#divisões-de-responsabilidade) |
| `Aplicacao/Nucleo/Comuns` | Subdivisões do catálogo de [Divisões de responsabilidade](#divisões-de-responsabilidade) |
| `Aplicacao/Nucleo/Comuns/Funcoes` | Organização por assunto em [Subpastas e espaços de nomes](#subpastas-e-espaços-de-nomes) |
| `Aplicacao/Painel` | Catálogo de [Divisões de responsabilidade](#divisões-de-responsabilidade) |
| `Aplicacao/Site` | Catálogo de [Divisões de responsabilidade](#divisões-de-responsabilidade) |
| `Configuracoes` | Arquivos próprios de integrações, conforme [Configuracoes](#configuracoes) |
| `Dados/Arquivos` | Novos armazenamentos de execução, conforme [Dados](#dados) |
| `Dados/Arquivos/BancoDados` | Subpastas de execução, conforme [Dados](#dados) |
| `Dados/Arquivos/Cache` | Subpastas de execução, conforme [Dados](#dados) |
| `Dados/Arquivos/Logs` | Subpastas de execução, conforme [Dados](#dados) |
| `Dados/Arquivos/Uploads` | Subpastas de execução, conforme [Dados](#dados) |
| `Dados/Cargas` | Gerenciadores adicionais, conforme [Esquemas e cargas](#esquemas-e-cargas) |
| `Dados/Esquemas` | Gerenciadores adicionais, conforme [Esquemas e cargas](#esquemas-e-cargas) |
| `Documentacao` | Subdivisão dos assuntos, conforme [Documentacao](#documentacao) e [Outros documentos](#outros-documentos) |
| `Documentacao/Aplicacao` | Destinos de documentação das áreas, conforme [Áreas](#áreas) |
| `Documentacao/Publico/Ativos` | Espelhamento das pastas dos ativos próprios documentados, conforme [Documentacao](#documentacao) |
| `Documentacao/Testes/Apoio` | Detalhamentos implementados, conforme [Documentacao](#documentacao) |
| `Documentacao/Testes/Integracao` | Detalhamentos implementados, conforme [Documentacao](#documentacao) |
| `Documentacao/Testes/Unitario` | Detalhamentos implementados, conforme [Documentacao](#documentacao) |
| `Externos` | Subpastas livres, conforme a categoria definida abaixo e [Ferramentas e Externos](#ferramentas-e-externos) |
| `Ferramentas` | Subpastas livres, conforme a categoria definida abaixo e [Ferramentas e Externos](#ferramentas-e-externos) |
| `Publico/Ativos/CSS` | Subpastas livres, conforme a categoria definida abaixo e [Ativos](#ativos) |
| `Publico/Ativos/Fontes` | Subpastas livres, conforme a categoria definida abaixo e [Ativos](#ativos) |
| `Publico/Ativos/Imagens` | Subpastas livres, conforme a categoria definida abaixo e [Ativos](#ativos) |
| `Publico/Ativos/JavaScript` | Subpastas livres, conforme a categoria definida abaixo e [Ativos](#ativos) |
| `Testes/Apoio` | Organização do apoio, conforme [Testes](#testes) |
| `Testes/Integracao` | Organização dos cenários, conforme [Testes](#testes) |
| `Testes/Unitario` | Organização dos cenários, conforme [Testes](#testes) |

Uma inclusão que cumpra essas permissões DEVE seguir as definições existentes, sem acrescentar ao mapa ou às tabelas o nome de cada pasta criada. A documentação dos componentes, as fichas de terceiros, os documentos de ferramentas e as referências afetadas continuam obrigatórios nos casos definidos nas seções responsáveis. A dispensa de atualizar este documento não dispensa esses registros.

Para inclusões de pastas, a atualização deste documento é OBRIGATÓRIA somente quando a proposta estiver fora das inclusões permitidas e exigir uma regra geral nova ou uma referência a uma adaptação. Uma exceção de alcance específico DEVE seguir o registro de adaptação. Ambos os casos seguem [Ampliações & Adaptações](00Regras.md#ampliações--adaptações), com as referências afetadas e o [Bloqueio dos documentos numerados](00Regras.md#bloqueio-dos-documentos-numerados).

**Fixas — existentes:** é PROIBIDO renomear ou remover estes diretórios e arquivos.

- `Aplicacao`: `Site`, `Nucleo`, `Nucleo/Comuns`, `Nucleo/Comuns/Funcoes` (com `AutoCarregamento.php`) e `Comandos`.
- `Configuracoes`: `Aplicacao` (com `Exemplo.env`).
- `Dados`: `Arquivos`, `Arquivos/BancoDados`, `Arquivos/Cache`, `Arquivos/Logs`, `Arquivos/Uploads`, `Esquemas`, `Esquemas/SQLite`, `Cargas` e `Cargas/SQLite`.
- `Documentacao`: `Adaptacoes`, `Ambiente`, `Aplicacao`, `Aplicacao/Site`, `Aplicacao/Nucleo`, `Aplicacao/Comandos`, `Externos`, `Ferramentas`, `Publico`, `Publico/Ativos`, `Publico/Externos`, `Testes`, `Testes/Apoio`, `Testes/Integracao` e `Testes/Unitario`.
- `Externos` e `Ferramentas`.
- `Publico` (com `index.php`), `Ativos`, `Ativos/CSS`, `Ativos/Fontes`, `Ativos/Imagens` e `Ativos/JavaScript`.
- `Testes`: `Apoio`, `Integracao` e `Unitario`.

**Nomes com prefixo preservado — `[*]`:** é PERMITIDO modificar os nomes de API e Painel somente quando houver necessidade de coexistirem duas ou mais APIs ou dois ou mais painéis, conforme [Áreas](#áreas). Os nomes DEVEM manter exatamente o início `API` ou `Painel`, respectivamente. As pastas correspondentes de `Documentacao/Aplicacao`, também marcadas com `[*]`, DEVEM acompanhar os nomes das áreas. A marca não integra o nome da pasta.

**Livres — subpastas de `Ferramentas`, `Externos`, `Publico/Ativos/CSS`, `Publico/Ativos/Fontes`, `Publico/Ativos/Imagens` e `Publico/Ativos/JavaScript`:** não são fixas e não constam neste mapa. É PERMITIDO criar, renomear, trocar e remover a qualquer momento quantas subpastas forem necessárias para acomodar o conteúdo correspondente à finalidade do diretório, sem alterar este documento e observando [Recursos de terceiros](00Regras.md#recursos-de-terceiros). As subpastas de ferramentas e de recursos de terceiros são registradas no documento de uso da ferramenta e na ficha do recurso, nos destinos de [Fichas e documentos de ferramentas](#fichas-e-documentos-de-ferramentas); as subpastas de ativos próprios não exigem registro. Os nomes seguem [Pastas de distribuições externas](01Padroes.md#pastas-de-distribuições-externas) e [Ativos públicos](01Padroes.md#ativos-públicos).

**Sob demanda — divisões do catálogo:** não constam neste mapa, com exceção de `Aplicacao/Nucleo/Comuns` e `Aplicacao/Nucleo/Comuns/Funcoes`, que já existem como pastas fixas para abrigar `AutoCarregamento.php`. Cada uma das demais divisões é criada com seu primeiro arquivo de implementação, somente com o conteúdo e nas condições definidos neste documento, e removida com o último, conforme [Criação de pastas da aplicação](00Regras.md#criação-de-pastas-da-aplicação). Sua presença física se confere na árvore real.

Pastas fixas e áreas sem outros arquivos versionados mantêm um README, conforme [Pastas sem conteúdo](00Regras.md#pastas-sem-conteúdo). Pastas e arquivos gerados durante a execução não são versionados, conforme [Dados privados e armazenamentos](00Regras.md#dados-privados-e-armazenamentos). A criação e a remoção das divisões dentro das áreas seguem [Criação de pastas da aplicação](00Regras.md#criação-de-pastas-da-aplicação).

A organização admite novas áreas de aplicação, subpastas por assunto, bancos e ferramentas conforme as seções abaixo.

Diretórios e arquivos exigidos por programas e plataformas externos DEVEM ocupar os locais exigidos por eles. Os acrescentados por ferramentas de ambiente estão definidos em [Ferramentas de ambiente](#ferramentas-de-ambiente). `README.md` apresenta o projeto; `AGENTS.md` orienta agentes de IA; `LICENSE` contém sua licença. Configurações pessoais de editor não integram o código da aplicação.

Subpastas criadas durante a execução em `Dados/Arquivos` são conteúdo gerado; sua presença não acrescenta um diretório fixo ao mapa, e sua exclusão do versionamento segue [Dados](#dados).

## Aplicacao

`Aplicacao` é o destino do código PHP interno, conforme as responsabilidades das áreas. A entrada web fixa permanece em `Publico/index.php`; ativos do navegador e scripts de banco obedecem aos destinos próprios deste documento. Bibliotecas distribuídas por terceiros, dados de execução, credenciais e testes DEVEM permanecer em seus destinos próprios.

### Áreas

Na fórmula `Aplicacao/[Area]`, `[Area]` representa o nome da área, conforme [Formato por elemento](01Padroes.md#formato-por-elemento). A tabela define a situação e a responsabilidade de cada tipo de área.

| Pasta | Situação | Recebe |
| --- | --- | --- |
| `API` | Nome com prefixo preservado, marcado com `[*]` | Respostas estruturadas para outros programas |
| `Site` | Fixa | Interface web pública; é a área padrão da entrada web |
| `Nucleo` | Fixa | O que é comum a todas as áreas a que se destina, conforme [Nucleo](#nucleo) |
| `Comandos` | Fixa | Entrada pelo terminal, com tratamento próprio |
| `Painel` | Nome com prefixo preservado, marcado com `[*]` | Interface administrativa |
| `[Area]` | Acréscimo PERMITIDO | Responsabilidade própria, distinta das demais áreas |

O Núcleo ocupa uma pasta de `Aplicacao`, mas não é área de atendimento: não recebe entradas próprias e serve às demais. Nas regras deste documento, “área” designa API, Site, Comandos, Painel e as demais áreas acrescentadas; o Núcleo é sempre citado nominalmente.

É PERMITIDO acrescentar áreas com responsabilidade própria, cada uma com seu diretório em `Aplicacao` e o correspondente em `Documentacao/Aplicacao`.

A permissão de mudança de nome com prefixo preservado e de separação em áreas de mesmo tipo aplica-se exclusivamente a API e Painel, marcadas com `[*]` no [Mapa geral](#mapa-geral). É PERMITIDO renomeá-las somente quando uma necessidade do projeto exigir a coexistência de duas ou mais APIs ou de dois ou mais painéis. Enquanto houver uma única área de cada tipo, seus nomes DEVEM permanecer `API` e `Painel`. É PROIBIDO renomear uma área única apenas para identificar o assunto ou o público atendido. A especificidade do assunto ou do público, por si só, não justifica a renomeação nem a criação de outra área.

Quando essa necessidade existir, a fórmula para API é `API[Finalidade]` (exemplos hipotéticos de escrita: `APISite` e `APIPainel`). Para Painel, a fórmula é `Painel[Finalidade]` (exemplos hipotéticos de escrita: `PainelSite` e `PainelBlog`). Esses exemplos ilustram somente a nomeação de duas áreas de API ou de duas áreas de painel necessárias ao projeto; não declaram áreas existentes nem justificam sua criação. Cada área resultante DEVE conservar o prefixo e a responsabilidade do tipo original.

Ao acrescentar uma área ou aplicar essa mudança de nome ou separação, atualizar o diretório correspondente em `Documentacao/Aplicacao` e as referências afetadas, sem acrescentar os nomes resultantes à tabela de áreas nem ao [Mapa geral](#mapa-geral).

Quando houver necessidade de múltiplas APIs ou múltiplos painéis, cada área resultante DEVE ter responsabilidade própria. As divisões internas de cada área continuam seguindo o catálogo de [Divisões de responsabilidade](#divisões-de-responsabilidade).

É PROIBIDO remover uma área sem a substituição decorrente da separação permitida nesta seção.

HTTP é o protocolo usado nas requisições web. As áreas de API DEVEM fornecer respostas estruturadas para outros programas; as áreas de painel DEVEM fornecer a interface administrativa. É PERMITIDO à área pública conter operações autenticadas; “Site” não significa ausência de controle de acesso.

O código de entrada HTTP DEVE obedecer às responsabilidades de `Controles` e `Rotas`. A apresentação do Site e das áreas de painel DEVE obedecer às responsabilidades de `Visoes`. É PROIBIDO colocar `Visoes` em uma área de API, cuja responsabilidade é fornecer respostas estruturadas.

Um comando representa a entrada pelo terminal; um trabalho representa uma tarefa reutilizável. Os arquivos de entrada e os comandos DEVEM ficar em `Aplicacao/Comandos`, diretamente na área ou em subpastas por assunto. Eles DEVEM interpretar argumentos e opções, acionar serviços ou trabalhos e converter o resultado em saída e código de término. É PROIBIDO implementar neles regras de negócio ou duplicar a tarefa de um trabalho. As divisões do catálogo criadas em `Comandos` recebem somente conteúdo ligado à execução pelo terminal. É PROIBIDO dar a uma subpasta de assunto de Comandos o nome de uma divisão de [Divisões de responsabilidade](#divisões-de-responsabilidade).

### Entrada web

Toda requisição dinâmica da aplicação web DEVE passar exclusivamente por `Publico/index.php`; a entrega direta de ativos autorizados pertence ao servidor, conforme [Publico](#publico). A identificação da área web DEVE residir no Núcleo; o destino padrão de uma requisição sem identificação de área é o Site. O restante do atendimento DEVE pertencer à área identificada. A identificação seleciona a área; as associações de método e caminho às operações pertencem a `Rotas` dessa área. É PROIBIDO transferir essas associações específicas para o Núcleo. O mecanismo comum DEVE operar sobre contratos e descrições fornecidos pelas áreas, sem depender de suas implementações particulares.

A entrada pelo terminal tem tratamento próprio em `Comandos` e não passa pela entrada web. O funcionamento concreto das entradas é documentado nos detalhamentos de `Documentacao/Aplicacao`.

### Nucleo

O Núcleo DEVE receber exclusivamente o que é comum a todas as áreas a que se destina e atende todas igualmente. O autocarregamento PSR-4 e a identificação da área web pertencem a essa responsabilidade. Funções técnicas compartilhadas obedecem ao mesmo limite. O caminho `Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php` é o ponto fixo do autocarregamento.

O que varia conforme a área DEVE ficar na própria área, mesmo quando suas versões têm conteúdo idêntico. O código que interpreta a configuração específica de uma área, suas exceções e demais preparações específicas ficam nessa área, na divisão correspondente. Os valores de configuração residem em `Configuracoes`, conforme [Configuracoes](#configuracoes). O Núcleo não depende de implementações particulares das áreas.

Um componente do Núcleo DEVE manter o mesmo contrato e significado em todas as áreas às quais se destina. Condições específicas de uma área DEVEM ser fornecidas por ela, por contratos ou descrições explícitas; é PROIBIDO incorporá-las ao mecanismo comum como exceções por consumidor. A identificação da área web segue essa separação entre mecanismo e descrições de [Entrada web](#entrada-web).

Antes de colocar um componente no Núcleo, confirmar que ele atende igualmente todas as áreas a que se destina. A divisão, como `Servicos`, `Entidades`, `Manipuladores` ou `Transportes`, decorre do tipo de responsabilidade; a escolha entre o Núcleo e uma área decorre exclusivamente do critério de uso comum desta seção.

### Independência entre áreas

É PROIBIDO fazer uma alteração em uma área exigir a reescrita de outra área ou do Núcleo. Toda alteração no Núcleo DEVE ter sua compatibilidade com as áreas dependentes conferida. Alterações de contratos seguem [Cuidados de manutenção](00Regras.md#cuidados-de-manutenção); não autorizam dependências particulares de áreas dentro do Núcleo.

A forma de obtenção de dependências DEVE respeitar esse princípio e ser documentada no detalhamento do componente existente em `Documentacao/Aplicacao`. Cada arquivo PHP DEVE documentar seus detalhes junto do código; a documentação da área DEVE apresentar a função do conjunto e seu uso, conforme [Documentação do código e do projeto](00Regras.md#documentação-do-código-e-do-projeto).

### Divisões de responsabilidade

A tabela abaixo determina os destinos arquiteturais PERMITIDOS e a responsabilidade obrigatória de cada um. Não é uma listagem física nem uma exigência de criar todas as divisões: cada divisão é criada sob demanda, conforme [Mapa geral](#mapa-geral). É PROIBIDO criar uma divisão sem seu primeiro arquivo de implementação.

O catálogo é fechado: uma divisão fora dele exige [Ampliações & Adaptações](00Regras.md#ampliações--adaptações). Os arquivos de uma área ou do Núcleo DEVEM residir em uma divisão do catálogo; é PROIBIDO colocar arquivos ou subpastas de assunto diretamente em sua raiz, salvo o README de [Pastas sem conteúdo](00Regras.md#pastas-sem-conteúdo). `Comandos` é a única exceção, conforme [Áreas](#áreas). `Comuns` e `Visoes` são grupos: recebem exclusivamente as subdivisões indicadas no catálogo, e é PROIBIDO colocar arquivos ou subpastas de assunto diretamente neles.

Primeiro, identificar a área responsável pela função do arquivo. Depois, escolher a divisão correspondente no catálogo. A criação e a manutenção seguem [Criação de pastas da aplicação](00Regras.md#criação-de-pastas-da-aplicação).

Na coluna de localização, “Qualquer área” significa a área responsável pela função, ou o Núcleo quando o recurso for comum a todas as áreas a que se destina. “Áreas web” são as áreas atendidas pela entrada web. Os caminhos são relativos à área escolhida.

| Pasta | Para que serve e como utilizar | Localização |
| --- | --- | --- |
| `Comuns` | Agrupa apoio técnico em Blocos, Classes e Funcoes. Conteúdo com responsabilidade própria, como serviço ou persistência, fica na divisão correspondente | Qualquer área |
| `Comuns/Blocos` | Declarações exclusivas de `trait`: comportamentos técnicos incorporados por classes, sem representar um serviço, contrato ou entidade por si mesmos | Qualquer área |
| `Comuns/Classes` | Classes concretas ou abstratas de infraestrutura técnica com estado de objeto ou implementação de contrato técnico, sem responsabilidade de outra divisão, como os mecanismos de requisição, resposta, roteamento, renderização, conexão e transação de banco, acesso técnico a arquivos, cache, logs, carregamento de configuração e resolução de dependências. É PROIBIDO criar classes estáticas apenas para agrupar funções auxiliares sem estado; registros concretos de construção pertencem exclusivamente a Provedores | Qualquer área |
| `Comuns/Funcoes` | Funções auxiliares técnicas puras ou sem estado de objeto, sem identidade e sem responsabilidade específica de outra divisão. O ponto fixo de autocarregamento fica em Nucleo/Comuns/Funcoes | Qualquer área; autocarregamento no Núcleo |
| `Conectores` | Clientes e adaptadores próprios para integração e comunicação com sistemas e serviços externos fora do banco de dados da aplicação. Consultas e gravações no banco da aplicação pertencem a Manipuladores, mesmo em servidor externo; a biblioteca de terceiros fica em Externos | Qualquer área |
| `Contratos` | Declarações exclusivas de `interface` PHP que descrevem operações oferecidas pelas dependências. É PROIBIDO colocar classes concretas ou abstratas nesta divisão; as implementações ficam nas divisões correspondentes | Qualquer área |
| `Controles` | Adaptam a entrada HTTP, acionam validações estruturais e serviços e compõem a resposta. É PROIBIDO conter SQL, condições de negócio ou acesso direto a manipuladores | Áreas web |
| `Entidades` | Objetos com identidade, estado, relações e invariantes próprias. Protegem seu estado sem consultar persistência ou integrações; condições da operação ficam em Servicos | Qualquer área |
| `Enumeracoes` | Declarações exclusivas de `enum` PHP que representam conjuntos fechados de valores técnicos ou de negócio, como níveis de log ou estados de uma operação | Qualquer área |
| `Eventos` | Mensagens com os dados de um fato ocorrido. As reações ao fato ficam em Ouvintes | Qualquer área |
| `Excecoes` | Tipos de falha e seus tratadores. Tipos representam a falha; tratadores convertem a falha em resultado de erro para a entrada responsável. Controles utilizam esse resultado na resposta HTTP, sem duplicar a conversão. Somente tipos e tratamento comuns pertencem ao Núcleo | Qualquer área |
| `Filtros` | Tratamentos transversais do processamento HTTP, incluindo condições gerais de acesso e ajustes da resposta. Rotas indicam sua aplicação; condições específicas da operação pertencem a Servicos | Áreas web |
| `Manipuladores` | Consultas, gravações, instruções SQL e mapeamento entre registros do banco de dados da aplicação e objetos. Conexão e transação técnicas ficam em Comuns/Classes; acesso a sistemas externos fora do banco fica em Conectores; persistência é acionada por Servicos, sem decidir condições de negócio | Qualquer área |
| `Modelos` | Modelos de saída para e-mails, mensagens e arquivos produzidos pela operação. Recebem dados preparados e não executam regras de negócio | Qualquer área |
| `Ouvintes` | Adaptação da reação a um evento. Delegam operações a Servicos ou disparam Trabalhos; é PROIBIDO duplicar suas regras ou tarefas | Qualquer área |
| `Provedores` | Registros concretos de contratos, implementações e construção de dependências. Utilizam os mecanismos de Comuns/Classes sem executar casos de uso durante o registro | Qualquer área |
| `Rotas` | Associações entre método HTTP, caminho, controle e filtros da área. Mecanismos próprios da área ficam em Comuns/Classes | Áreas web |
| `Servicos` | Operações e suas condições de negócio. Coordenam persistência e integrações por contratos e respeitam as invariantes de Entidades; não interpretam HTTP ou argumentos de terminal | Qualquer área |
| `Trabalhos` | Unidades de tarefa com entrada explícita para execução imediata ou adiada. Delegam regras de negócio a Servicos, sem depender de requisição ativa; não interpretam requisições HTTP ou argumentos de terminal | Qualquer área |
| `Transportes` | Objetos tipados para transportar entradas e saídas de operações. Representam os dados do contrato atendido | Qualquer área |
| `Validacoes` | Verificações estruturais de presença, tipo, formato e limites dos dados. É PROIBIDO consultar persistência para decidir regras da operação; essas condições ficam em Servicos | Qualquer área |
| `Visoes` | Agrupa a apresentação em Componentes, Estruturas e Paginas, conforme o conteúdo utilizado | Áreas web com interface |
| `Visoes/Componentes` | Fragmentos reutilizados entre páginas da área | Áreas web com interface |
| `Visoes/Estruturas` | Estruturas que envolvem páginas, como a organização geral de cabeçalho, conteúdo e rodapé | Áreas web com interface |
| `Visoes/Paginas` | Conteúdo de uma tela e seus fragmentos exclusivos | Áreas web com interface |

A responsabilidade específica prevalece sobre o destino genérico `Comuns`; é PROIBIDO usar esse grupo como destino alternativo para conteúdo enquadrado em outra divisão. Traits RESIDEM EXCLUSIVAMENTE EM `Comuns/Blocos`; recursos técnicos com estado de objeto ou contrato de classe ficam em `Comuns/Classes`; funções auxiliares sem esses requisitos ficam em `Comuns/Funcoes`. Se um arquivo reunir responsabilidades de divisões distintas, ele DEVE ser separado antes de sua inclusão. Os nomes seguem [Nomes por responsabilidade](01Padroes.md#nomes-por-responsabilidade).

É PROIBIDO declarar trait que represente serviço, contrato, entidade ou regra de negócio; esse conteúdo pertence à divisão correspondente. Constantes DEVEM ser declaradas no elemento cujo contrato atendem, como classe, interface ou enumeração da divisão responsável; conjuntos fechados de valores ficam em `Enumeracoes`, e valores que variam por ambiente são parâmetros de `Configuracoes/Aplicacao`. É PROIBIDO criar arquivo ou classe apenas para agrupar constantes.

Entidades, transportes e eventos não leem requisição, sessão ou banco. Visões recebem dados preparados, fazem apresentação, condicionais simples e escape conforme o contexto de saída. Não consultam banco nem executam serviços e permanecem fora de Publico.

Para distinguir verificações, aplicar a responsabilidade do contrato: formato da entrada pertence a `Validacoes`; consistência do estado de objetos com identidade pertence a `Entidades`; decisão que depende da operação, de persistência ou de integração pertence a `Servicos`. `Transportes` e `Eventos` representam dados; não substituem essas verificações nem coordenam operações. Uma falha lançada por essas divisões tem seu tipo em `Excecoes`; capturá-la não transfere a responsabilidade que a originou ao tratador.

Modelos de saída seguem também esses limites de apresentação e ficam na área responsável pelo conteúdo. A apresentação das telas das áreas com interface continua em `Visoes`; o catálogo de Modelos não exige criar uma pasta antes de haver implementação.

### Subpastas e espaços de nomes

Subpastas por assunto são OPCIONAIS dentro de cada divisão e DEVEM preservar sua responsabilidade. A repetição de nomes é PERMITIDA somente para responsabilidades de alcances diferentes. O caminho completo identifica o elemento.

Declarações próprias de classes, interfaces, enumerações e traits em `Aplicacao` e `Testes` DEVEM usar como prefixo de espaço de nomes o nome de sua pasta na raiz, associado a ela uma única vez, conforme o PSR-4. A fórmula da aplicação é `Aplicacao\[Area]\[Divisao]\[Subpastas]\[Classe]` → `Aplicacao/[Area]/[Divisao]/[Subpastas]/[Classe].php`. A fórmula geral dos testes é `Testes\[Categoria]\[Subpastas]\[Classe]` → `Testes/[Categoria]/[Subpastas]/[Classe].php`. Nos cenários, `[Classe]` DEVE usar `[Finalidade]Teste`; classes auxiliares seguem o formato geral, conforme [Formato por elemento](01Padroes.md#formato-por-elemento). `[Area]` representa a pasta da área ou `Nucleo`; `[Subpastas]` representa zero ou mais segmentos OPCIONAIS, inclusive a área ou `Nucleo` nos testes; `[Divisao]` representa o caminho da divisão escolhida. Para classes de comandos diretamente na área ou em subpastas de assunto, a fórmula é `Aplicacao\Comandos\[Subpastas]\[Classe]` → `Aplicacao/Comandos/[Subpastas]/[Classe].php`.

Exemplos hipotéticos de escrita, sem classes ou arquivos existentes: `Aplicacao\Site\Comuns\Classes\LeitorArquivo` → `Aplicacao/Site/Comuns/Classes/LeitorArquivo.php`; `Testes\Unitario\Site\LeituraArquivoTeste` → `Testes/Unitario/Site/LeituraArquivoTeste.php`. Os segmentos intermediários DEVEM corresponder exatamente à hierarquia escolhida.

Segmentos e arquivo coincidem exatamente com a declaração, inclusive em maiúsculas e minúsculas. A correspondência PSR-4 desta seção não carrega funções, rotas, visões, modelos de saída ou pontos de entrada por sua simples presença no disco. Esses arquivos DEVEM ter inclusão ou execução explícita pelo componente responsável, documentada junto da implementação. Funções nomeadas DEVEM usar o espaço de nomes correspondente à pasta; arquivos sem declaração nomeada não exigem uma declaração artificial de espaço de nomes. Bibliotecas de terceiros mantêm seus próprios espaços de nomes, conforme [Integração com a aplicação](05Externos.md#integração-com-a-aplicação).

## Configuracoes

`Configuracoes/Aplicacao` recebe os parâmetros lidos pela aplicação. Os parâmetros de todas as áreas, do Núcleo e das integrações, incluindo endereços e credenciais, RESIDEM EXCLUSIVAMENTE EM `Configuracoes/Aplicacao`, conforme [Parâmetros da aplicação](03Configuracoes.md#parâmetros-da-aplicação).

É PERMITIDO criar em `Configuracoes` uma subpasta própria para uma integração somente quando ela exigir arquivos em formato próprio, que não se expressam como parâmetros, como certificados ou arquivos de credencial fornecidos pelo serviço. Essa subpasta não repete parâmetros de `Configuracoes/Aplicacao` nem configuração de ferramentas de desenvolvimento, e seu conteúdo privado segue [Dados privados e armazenamentos](00Regras.md#dados-privados-e-armazenamentos). Pastas acrescentadas a `Configuracoes` por ferramentas de ambiente seguem [Ferramentas de ambiente](#ferramentas-de-ambiente). Arquivos exigidos em outro local pela ferramenta permanecem nesse local.

Modelos sem segredos são versionados. Configurações privadas ficam fora de `Publico`. Código de negócio pertence a `Aplicacao`; configuração de ferramentas de desenvolvimento fica junto da ferramenta. Veja [Configurações](03Configuracoes.md).

## Dados

| Pasta | Conteúdo | Não recebe |
| --- | --- | --- |
| `Esquemas` | Scripts de criação e alteração da estrutura do banco | Dados de carga ou classes PHP |
| `Cargas` | Scripts de carga inicial, manutenção exclusivamente de dados existentes e carga de dados fictícios de teste | Estrutura de tabelas, transformação inseparável de mudança estrutural, credenciais ou dados pessoais reais incorporados ao conteúdo dos scripts |
| `Arquivos/BancoDados` | Bancos em arquivo e arquivos auxiliares de execução | Scripts de evolução do esquema |
| `Arquivos/Cache` | Conteúdo temporário reconstruível | Fonte única de dados permanentes |
| `Arquivos/Logs` | Logs da execução | Credenciais e dados pessoais desnecessários |
| `Arquivos/Uploads` | Arquivos recebidos, com acesso controlado | Código executável da aplicação |

Um banco mantido por servidor externo não usa arquivo em `BancoDados`. As subpastas criadas durante a execução dentro desses quatro destinos seguem a exclusão do versionamento do respectivo destino. É PERMITIDO acrescentar diretamente em `Arquivos` uma subpasta para um novo armazenamento de execução, com finalidade, acesso e retenção descritos no detalhamento do componente responsável. Essa subpasta é criada pela preparação do ambiente ou pelo componente, não é versionada e DEVE receber, na mesma alteração, a regra de exclusão do versionamento correspondente, conforme [Dados privados e armazenamentos](00Regras.md#dados-privados-e-armazenamentos). Arquivos versionáveis de esquema e carga não são armazenamentos de execução e permanecem nos destinos correspondentes.

### Esquemas e cargas

Cada sistema gerenciador tem sua subpasta em Esquemas e Cargas. As subpastas do gerenciador padrão são fixas, conforme [Mapa geral](#mapa-geral), e permanecem mesmo com a adoção de outro gerenciador; o documento do gerenciador segue [Ferramentas de ambiente](#ferramentas-de-ambiente). Outro gerenciador recebe sua própria subpasta ao lado, nas fórmulas `Dados/Esquemas/[Gerenciador]` e `Dados/Cargas/[Gerenciador]`, em que `Gerenciador` é o nome do sistema atendido no formato de pasta de [Formato por elemento](01Padroes.md#formato-por-elemento) (exemplo hipotético de escrita: `Dados/Esquemas/PostgreSQL`). Há sequência independente por pasta. Os nomes dos arquivos seguem [Nomes dos scripts de banco de dados](01Padroes.md#nomes-dos-scripts-de-banco-de-dados).

Scripts de correção ou migração exclusivamente de dados existentes ficam em Cargas, na subpasta do gerenciador, com suas reversões quando aplicáveis. Uma transformação de dados inseparável de uma mudança estrutural DEVE integrar o script de manutenção em Esquemas. Os scripts descrevem as operações e não incorporam credenciais ou dados pessoais reais como valores versionados. Os nomes das tabelas, colunas e índices seguem [Formato por elemento](01Padroes.md#formato-por-elemento).

A aplicação dos arquivos trata cada finalidade separadamente: instalação e manutenção respeitam a sequência documentada, as dependências e o estado do banco; a reversão segue a ordem inversa das alterações efetivamente aplicadas. Cargas entram após os esquemas correspondentes. O nome cronológico não implica execução automática, repetição ou inserção retroativa. As condições de atualização e recuperação estão em [Cuidados de manutenção](00Regras.md#cuidados-de-manutenção).

Cargas com prefixo `T.` são exclusivas de bancos isolados de teste e nunca são aplicadas em produção. Instalação e manutenção em produção DEVEM excluir explicitamente esses arquivos.

## Publico

`Publico` é o único diretório de origem da entrega direta de arquivos pelo servidor web. O servidor DEVE apontar sua raiz de documentos para `Publico`, sem expor a raiz do projeto. Estar nesse diretório não autoriza a entrega de um arquivo: os bloqueios e o processamento exigidos permanecem obrigatórios.

`index.php` é o arquivo fixo reservado à entrada dinâmica. Ativos autorizados DEVEM ser entregues como arquivos estáticos. É PERMITIDO manter aqui arquivos de configuração reconhecidos pelo servidor adotado; isso não cria outra entrada PHP. Código interno, configuração privada, documentação e arquivos sujeitos a autorização DEVEM permanecer fora desta pasta.

É PERMITIDO acrescentar arquivos públicos exigidos por protocolos somente com seus caminhos e condições de acesso documentados na configuração de entrega. Outro ponto de entrada PHP em `Publico` ou uma organização diferente desta pasta exige [Ampliações & Adaptações](00Regras.md#ampliações--adaptações). A entrada pelo terminal reside exclusivamente em `Aplicacao/Comandos`.

### Ativos

As famílias diretamente abaixo de `Publico/Ativos` são estas quatro:

| Família | Conteúdo entregue | Extensões entregues |
| --- | --- | --- |
| `CSS` | Folhas de estilo | `css` |
| `JavaScript` | Scripts do navegador e mapas de origem expressamente habilitados, sem exposição de fontes protegidas | `js`, `mjs`, `map` |
| `Imagens` | Imagens e ícones | `png`, `jpg`, `jpeg`, `gif`, `webp`, `avif`, `svg`, `ico` |
| `Fontes` | Arquivos tipográficos | `woff2`, `woff`, `ttf`, `otf` |

Além dos arquivos dessas extensões, as famílias recebem somente os metadados preservados das distribuições externas, como licenças e READMEs, que não são entregues, conforme os parágrafos seguintes.

Cada família aceita subpastas livres em qualquer profundidade, por área, assunto, fornecedor, versão ou variante, conforme [Mapa geral](#mapa-geral). Isso não cria uma nova família. Para identificar distribuições externas, o nome e a versão ficam no mesmo nível, conforme [Pastas de distribuições externas](01Padroes.md#pastas-de-distribuições-externas); essa definição específica prevalece sobre a possibilidade geral de subpastas por versão. Subpastas internas fornecidas pelo terceiro conservam sua organização. Grafia e exemplos estão em [Ativos públicos](01Padroes.md#ativos-públicos).

É PERMITIDO preservar subpastas, nomes e metadados originais das distribuições externas, cumprindo as obrigações de licença e identificação. A entrega pública continua limitada à família e aos formatos habilitados. Por exemplo, uma licença junto a um arquivo tipográfico é metadado da distribuição, não um arquivo de fonte a entregar. Outra família ou formato segue [Ampliações & Adaptações](00Regras.md#ampliações--adaptações), conforme seu alcance, e exige a configuração correspondente do servidor.

Ativos são referenciados por endereço público, não pelo caminho interno do disco. Não contêm PHP executável, segredos ou conteúdo sujeito a autorização. Arquivos privados ficam fora de Publico e são entregues por uma operação autorizada. Validação no navegador complementa a validação no servidor.

A configuração de entrega bloqueia listagem, execução de código e acesso aos metadados não públicos. A presença de arquivos em uma distribuição não concede permissão de entrega. O que é entregue ao usuário segue [Arquivos entregues ao usuário](00Regras.md#arquivos-entregues-ao-usuário), e a configuração de caminhos e acessos está em [Entrega web](03Configuracoes.md#entrega-web).

Os originais documentados dos ativos próprios residem nestas famílias, conforme [Arquivos entregues ao usuário](00Regras.md#arquivos-entregues-ao-usuário), e DEVEM permanecer distinguíveis das versões processadas. Sua entrega sem processamento DEVE estar bloqueada. Mapas de origem são OPCIONAIS e exigem habilitação explícita por ambiente; é PROIBIDO utilizá-los para entregar arquivos fonte documentados, comentários internos ou dados protegidos, inclusive por conteúdo incorporado ao mapa ou por caminhos de recuperação.

## Ferramentas e Externos

`Ferramentas` contém as ferramentas de desenvolvimento adotadas e seus arquivos de apoio, em uma pasta por ferramenta. `Externos` contém as distribuições de bibliotecas instaladas. O critério de desempate entre as duas é a forma de uso: o que é executado como programa pertence a `Ferramentas`, mesmo quando o código dos testes usa suas classes; o que é carregado como biblioteca pelo código próprio, de produção ou de testes, pertence a `Externos`. Essas pastas recebem somente o conteúdo da ferramenta ou da distribuição, incluindo a documentação original de terceiros; os documentos e as fichas escritos pelo projeto ficam em [Fichas e documentos de ferramentas](#fichas-e-documentos-de-ferramentas). As pastas internas de `Ferramentas` e `Externos` são livres, conforme [Mapa geral](#mapa-geral): existem enquanto o recurso é adotado e DEVEM ser removidas com ele, junto com seu documento ou sua ficha e a conferência das referências afetadas. Ativos externos destinados ao navegador ficam em `Publico/Ativos`.

O código PHP próprio de integração DEVE residir em `Aplicacao`, na divisão responsável. Ativos destinados ao navegador permanecem em `Publico/Ativos`, conforme [Ativos](#ativos). Os procedimentos estão em [Ferramentas](04Ferramentas.md) e [Externos](05Externos.md).

## Testes

| Pasta | Conteúdo |
| --- | --- |
| `Unitario` | Cenários de uma unidade com dependências controladas |
| `Integracao` | Cenários de interação entre componentes ou infraestrutura real |
| `Apoio` | Preparação compartilhada da suíte, no arquivo prescrito `Testes/Apoio/Preparacao.php`, substitutos, dados fictícios consumidos diretamente pelo código dos testes e utilitários; scripts de carga aplicados a bancos de teste ficam em `Dados/Cargas`, conforme [Esquemas e cargas](#esquemas-e-cargas) |

O arquivo `Testes/Apoio/Preparacao.php` DEVE existir a partir do primeiro cenário: as declarações de `Testes` usam o prefixo de espaço de nomes próprio, cujo registro pertence a essa preparação, conforme [Subpastas e espaços de nomes](#subpastas-e-espaços-de-nomes).

É PERMITIDO organizar cada categoria por áreas, Núcleo, divisões e subpastas de assunto existentes. A suíte completa DEVE executar os testes de todas as áreas e do Núcleo. É PROIBIDO incluir cenários executáveis ou código de produção em `Apoio`. Banco, cache e arquivos de teste DEVEM usar destinos isolados dos dados reais.

Os cuidados de execução estão em [Categorias e isolamento dos cenários](00Regras.md#categorias-e-isolamento-dos-cenários), e o conteúdo dos procedimentos está em [Ambiente de teste](01Padroes.md#ambiente-de-teste). A ferramenta de testes adotada é indicada em [Funções exigidas](04Ferramentas.md#funções-exigidas).

## Documentacao

Os documentos gerais fixos na raiz de `Documentacao` apresentam os assuntos gerais do projeto e usam prefixos numéricos. Os demais documentos usam apenas o nome do assunto, conforme [Nomes dos documentos](01Padroes.md#nomes-dos-documentos).

`Documentacao/Aplicacao` recebe detalhamentos da finalidade, funcionalidades, contratos, integrações, configuração e uso dos componentes implementados. É PERMITIDO criar subpastas diretamente em `Documentacao/Aplicacao` somente para corresponder às áreas e ao Núcleo de `Aplicacao`. Cada uma dessas subpastas recebe somente documentos; é PROIBIDO criar subpastas dentro delas. As definições gerais ficam nos documentos numerados; os detalhes internos de cada arquivo PHP permanecem junto do código, conforme [Documentação do código e do projeto](00Regras.md#documentação-do-código-e-do-projeto).

`Documentacao/Memorial.md` é o destino fixo do histórico, das decisões registradas e do estado atual do projeto, conforme [Memorial do projeto](00Regras.md#memorial-do-projeto). Não define regras nem substitui os documentos numerados ou os detalhamentos técnicos. O `README.md` da raiz apresenta o projeto e aponta para a documentação; não recebe histórico, decisões ou inventário de implementação.

`Documentacao/Testes` recebe detalhamentos da preparação dos ambientes, dos cenários e da execução dos testes implementados. Suas subpastas acompanham as categorias de `Testes`: `Unitario`, `Integracao` e `Apoio`. É PERMITIDO espelhar áreas, Núcleo, divisões e subpastas de assunto implementadas dentro de cada categoria. As definições gerais ficam nos documentos numerados; o inventário de requisitos, contratos e cenários, sua relação com os testes e as evidências de cobertura ficam no detalhamento responsável, conforme [Alcance e evidências da cobertura](00Regras.md#alcance-e-evidências-da-cobertura).

`Documentacao/Publico` é fixa e existe. Ela recebe as pastas fixas `Ativos` e `Externos` e os documentos específicos necessários ao uso, geração, manutenção ou distribuição dos demais arquivos públicos. Cada documento DEVE ter nome de assunto, sem prefixo numérico. Os arquivos entregues ao navegador permanecem em `Publico`; a documentação interna DEVE permanecer fora da raiz pública.

`Documentacao/Publico/Ativos` recebe exclusivamente a documentação dos ativos próprios do projeto. Cada documento DEVE ficar na pasta que corresponde à localização do ativo documentado em `Publico/Ativos`, preservando a família, os nomes e a hierarquia das subpastas.

A correspondência segue a fórmula `Publico/Ativos/[Familia]/[Subpastas]` → `Documentacao/Publico/Ativos/[Familia]/[Subpastas]`, em que `[Subpastas]` representa zero ou mais níveis existentes nos ativos. É PERMITIDO criar uma subpasta de documentação somente quando a pasta correspondente existir em `Publico/Ativos` e houver um primeiro documento necessário para colocar nela. É PROIBIDO criar subpastas apenas para subdividir a documentação, sem correspondência na estrutura dos ativos.

A necessidade de documentação segue [Documentação do código e do projeto](00Regras.md#documentação-do-código-e-do-projeto); a presença de um arquivo em `Publico/Ativos` não exige, por si só, um documento individual ou um catálogo. Os documentos seguem [Nomes dos documentos](01Padroes.md#nomes-dos-documentos) e [Estrutura dos documentos](01Padroes.md#estrutura-dos-documentos). É PROIBIDO colocar fichas de recursos de terceiros nesse destino.

`Documentacao/Publico/Externos` recebe exclusivamente as fichas dos recursos públicos de terceiros usados no projeto, conforme [Fichas e documentos de ferramentas](#fichas-e-documentos-de-ferramentas). As fichas RESIDEM EXCLUSIVAMENTE EM arquivos diretamente nessa pasta; é PROIBIDO criar subpastas ou colocar documentação de ativos próprios nela. O README previsto em [Pastas sem conteúdo](00Regras.md#pastas-sem-conteúdo) permanece somente enquanto não houver fichas.

Os dois destinos recebem somente documentação; é PROIBIDO duplicar neles os ativos ou os arquivos das distribuições, que permanecem em `Publico/Ativos`.

Os READMEs das subpastas seguem [Pastas sem conteúdo](00Regras.md#pastas-sem-conteúdo).

`Adaptacoes` recebe exclusivamente os documentos de adaptação, além do README previsto em [Pastas sem conteúdo](00Regras.md#pastas-sem-conteúdo) enquanto não houver nenhum. Esses documentos descrevem diferenças em relação aos padrões ou limites estabelecidos, com alcance e forma de aplicação. O procedimento de registro está em [Registro de adaptação](00Regras.md#registro-de-adaptação). Detalhes que seguem o padrão ficam no documento do assunto correspondente.

### Fichas e documentos de ferramentas

Os documentos e as fichas escritos pelo projeto sobre ferramentas e recursos de terceiros ficam em três pastas fixas, separados das distribuições:

| Pasta | Recebe |
| --- | --- |
| `Documentacao/Ferramentas` | O documento de uso de cada ferramenta de `Ferramentas` |
| `Documentacao/Externos` | A ficha de cada biblioteca de `Externos` |
| `Documentacao/Publico/Externos` | A ficha de cada recurso de terceiros instalado em `Publico` |

Essas pastas contêm somente os arquivos soltos, um por ferramenta ou recurso; é PROIBIDO criar subpastas nelas. Os nomes seguem [Nomes dos documentos](01Padroes.md#nomes-dos-documentos). O conteúdo dessas pastas identifica as ferramentas e os recursos adotados; este documento não os relaciona. A documentação original fornecida pelo terceiro, como README, licença e histórico de versões, permanece na pasta da distribuição. A remoção de uma ferramenta ou de um recurso remove também seu documento ou sua ficha. Enquanto não houver ferramenta ou recurso, a pasta mantém seu README, conforme [Pastas sem conteúdo](00Regras.md#pastas-sem-conteúdo).

### Ferramentas de ambiente

`Documentacao/Ambiente` é fixa e reúne um documento por ferramenta de ambiente: programa ou plataforma utilizado pelo projeto sem ser incluído nele. Cada documento identifica as pastas e os arquivos que a ferramenta acrescenta ao projeto, sua situação, sua localização e sua escrita, além das regras de uso da ferramenta. O conteúdo mínimo está em [Documento de ferramenta de ambiente](01Padroes.md#documento-de-ferramenta-de-ambiente). O conteúdo da pasta identifica as ferramentas de ambiente adotadas; este documento não as relaciona. As regras de uso do versionamento e do repositório remoto, incluindo branches, commits, pull requests, issues e automação, ficam nos documentos das ferramentas correspondentes.

Uma ferramenta de ambiente adotada recebe seu documento nesta pasta. Sua remoção retira o documento e as pastas e os arquivos que ela acrescentou, salvo as pastas fixas do [Mapa geral](#mapa-geral). Ferramentas de desenvolvimento incluídas no projeto ficam em `Ferramentas`; detalhes de um ambiente de execução preparado seguem [Outros documentos](#outros-documentos).

### Outros documentos

É PERMITIDO criar pasta própria para outros assuntos ao dividi-los em documentos, conforme [Onde documentar cada assunto](00Regras.md#onde-documentar-cada-assunto). A pasta é criada com o primeiro documento necessário; não criar pastas ou arquivos apenas para reproduzir exemplos de nomes.

É PERMITIDO separar detalhes de um ambiente efetivamente preparado em uma pasta do assunto em `Documentacao`, quando essa subdivisão for necessária. As definições gerais continuam em [Configurações](03Configuracoes.md), com referência ao detalhamento. A configuração executável permanece em `Configuracoes` ou no local exigido pela ferramenta; o procedimento de uma ferramenta fica junto dela, e o de um ambiente exclusivo dos testes fica em `Documentacao/Testes`.

Documentação distribuída por terceiros permanece junto da distribuição.
