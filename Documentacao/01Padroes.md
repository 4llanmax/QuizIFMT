# Padrões

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 13/11/2026     |

## Objetivo

Este documento define a forma de nomear os elementos do projeto e escrever sua documentação. O destino dos arquivos está em [Estrutura](02Estrutura.md); a manutenção e o registro de adaptações estão em [Regras](00Regras.md).

## Em resumo

- Identificadores não têm acento nem palavras de ligação, e cada elemento tem um formato próprio. Veja [Formato por elemento](#formato-por-elemento).
- Os documentos fixos da raiz de `Documentacao` usam `[Numero][Assunto].md`; os demais usam `[Assunto].md`. Veja [Nomes dos documentos](#nomes-dos-documentos).
- Toda escrita ou atualização da documentação cumpre as cinco leis de voz, escopo, exemplos, força normativa e fronteiras. Veja [Leis de escrita](#leis-de-escrita).
- Todo documento começa com o título, o cabeçalho alinhado e o objetivo. Veja [Estrutura dos documentos](#estrutura-dos-documentos).
- Cada arquivo que aceita comentários documenta a si mesmo. Veja [Documentação nos arquivos com comentários](#documentação-nos-arquivos-com-comentários).
- Detalhamentos, fichas e documentos de ferramenta têm um conteúdo mínimo. Veja [Conteúdo dos detalhamentos](#conteúdo-dos-detalhamentos).
- Branches, commits, pull requests e issues seguem os documentos de ferramentas de ambiente. Veja [Ferramentas de ambiente](02Estrutura.md#ferramentas-de-ambiente).

## Composição dos nomes

O idioma dos nomes e textos e suas exceções seguem [Idioma do projeto](00Regras.md#idioma-do-projeto).

Identificadores DEVEM ser escritos sem acentos e sem cedilha, usando somente as palavras que expressem sua finalidade ou seu significado. É PROIBIDO incluir artigos, preposições, conjunções e pronomes, inclusive suas contrações, como `de`, `do`, `da`, `dos`, `das`, `para`, `com`, `em`, `no`, `na`, `a`, `o`, `as`, `os`, `e`, `ou` e `se`. A lista é exemplificativa: a proibição abrange as demais palavras dessas classes, independentemente de maiúsculas, minúsculas ou separadores. Essa composição vale para todos os nomes definidos pelo projeto; os formatos específicos estão em [Formato por elemento](#formato-por-elemento).

Singular e plural DEVEM corresponder ao que cada palavra designa: um elemento ou uma coleção. O tipo do identificador não determina sua flexão. Um mesmo conceito DEVE manter as mesmas palavras em suas referências; maiúsculas, minúsculas e separadores obedecem ao formato do elemento.

Nas fórmulas deste documento, termos entre colchetes representam partes substituíveis, não caracteres literais do nome. A fórmula `[Identificador][Recurso]`, aplicada aos formatos de [Formato por elemento](#formato-por-elemento), produz os exemplos hipotéticos de escrita `IdentificadorArquivo`, `identificadorArquivo`, `identificador_arquivo` e `IDENTIFICADOR_ARQUIVO`. Esses identificadores não representam declarações existentes.

Nas fórmulas de nomes, é PERMITIDO expressar a finalidade por uma ou mais palavras. É PROIBIDO deduzir dos exemplos uma quantidade obrigatória de palavras, uma flexão ou um sufixo. Quantidades, partes fixas e sufixos são obrigatórios somente quando a regra os declarar expressamente.

A omissão de palavras considera palavras completas, sem retirar letras de outras palavras ou siglas. Uma composição ambígua é substituída por um nome que expresse diretamente a finalidade.

As fórmulas da tabela seguinte descrevem a composição dos exemplos escolhidos. Aplicar a regra de composição ao nome e o formato obrigatório ao elemento correspondente.

| Aspecto ilustrado | Fórmula aplicada ao exemplo | Exemplo hipotético de escrita |
| --- | --- | --- |
| Omissão de preposição | `[Palavra][Palavra]` | `PastaArquivos`, em lugar de `PastaDeArquivos` |
| Omissão de pronome possessivo | `[Finalidade]` | `Parametros`, em lugar de `MeusParametros` |
| Omissão de artigo e preposição | `[acao][Finalidade]` | `lerParametrosConsulta`, em lugar de `lerOsParametrosDaConsulta` |
| Omissão de conjunção | `[Acao][Acao]` | `ImportacaoExportacao`, em lugar de `ImportacaoEExportacao` |

Títulos, descrições e textos para leitura usam espaços, acentos, cedilha e palavras de ligação.

## Formato por elemento

As fórmulas da coluna `Formato obrigatório` antecedem os exemplos. Todos os nomes da última coluna são hipotéticos e ilustram exclusivamente a escrita; não descrevem arquivos, classes, parâmetros ou tabelas existentes.

`Finalidade`, `finalidade` e `FINALIDADE` representam as palavras necessárias ao significado do nome, com a grafia e os separadores definidos em cada linha. As formas com separador também admitem uma palavra única. Formatos específicos e exceções estão indicados em [Casos especiais](#casos-especiais).

| Elemento | Formato obrigatório | Exemplos hipotéticos de escrita |
| --- | --- | --- |
| Pasta | `[Finalidade]`: inicial maiúscula em cada palavra, sem separadores | `ArquivosGerados` |
| Arquivo próprio | `[Finalidade].[extensao]`: inicial maiúscula em cada palavra; extensão minúscula | `ArquivoControle.php`, `TemaPrincipal.css` |
| Classe, interface, enumeração e trait | `[Finalidade]`: inicial maiúscula em cada palavra | `ArquivoControle`, `LeitorCSV` |
| Segmento de espaço de nomes | `[Pasta]`: inicial maiúscula em cada palavra | `Conversores` |
| Método, função, propriedade, parâmetro e variável | `[finalidade]`: primeira palavra com inicial minúscula; seguintes com inicial maiúscula | `lerArquivo`, `$posicaoRegistro` |
| Constante e caso de enumeração | `[FINALIDADE]`: maiúsculas, palavras separadas por sublinhado | `CAMPO_FICHA`, `CAMPOS_FICHA`, `PROCESSANDO` |
| Tabela, coluna e índice do banco | `[finalidade]`: minúsculas, palavras separadas por sublinhado | `registros`, `identificador_registro`, `registros_identificador_unico` |
| Chave de variável de ambiente, inclusive em `.env` | `[FINALIDADE]`: maiúsculas, palavras separadas por sublinhado | `BANCO_CAMINHO`, `SERVIDOR_PORTA` |
| Chave de configuração própria, nome de campo de formulário e parâmetro de consulta HTTP | `[finalidade]`: primeira palavra com inicial minúscula; seguintes com inicial maiúscula | `bancoCaminho`, `nomeArquivo`, `paginaAtual` |
| Classe e identificador HTML, utilizados por CSS ou JavaScript | `[finalidade]`: minúsculas, palavras separadas por hífen | `menu-principal`, `campo-arquivo` |
| Atributo personalizado `data-*` do HTML | `data-[finalidade]`: prefixo `data-`, seguido de palavras minúsculas separadas por hífen | `data-identificador-arquivo`, `data-pagina-atual` |
| Propriedade personalizada de CSS | `--[finalidade]`: prefixo `--`, seguido de palavras minúsculas separadas por hífen | `--cor-principal`, `--espaco-conteudo` |

Nos exemplos de constantes, o singular identifica um campo e o plural identifica uma coleção de campos da ficha. O exemplo de estado ilustra um nome com palavra única.

Um espaço de nomes identifica o caminho lógico de uma declaração PHP. Uma trait é um bloco de comportamento incorporado por classes.

Os formatos da tabela valem para nomes definidos pelo projeto. Chaves, campos e identificadores exigidos por ferramentas, gerenciadores ou integrações externas seguem [Grafias externas](#grafias-externas). Índices recebem nomes pela tabela, pelas colunas e pela finalidade, sem abreviar essas palavras. Nos campos de formulário, a sintaxe utilizada para representar coleções é documentada pelo componente; o nome próprio mantém o formato da tabela.

Uma chave própria escrita em `.env` DEVE usar o formato de variável de ambiente; sua representação interna DEVE usar o formato de chave de configuração. Na correspondência `[FINALIDADE]` → `[finalidade]`, com as grafias das linhas de variável de ambiente e de chave de configuração, o exemplo hipotético de escrita é `BANCO_CAMINHO` → `bancoCaminho`; essa conversão não declara uma chave aceita nem um carregador existente. O contrato do carregador DEVE documentar a correspondência efetivamente adotada. O nome de um atributo `data-*` segue o formato HTML da tabela; seu acesso pela propriedade `dataset` em JavaScript segue a conversão definida pelo navegador.

Cada arquivo PHP que declara uma classe, interface, enumeração ou trait DEVE declarar um único desses elementos. O nome do arquivo DEVE corresponder exatamente ao nome declarado, seguido de `.php`. Arquivos PHP sem essas declarações seguem o formato geral de arquivo próprio da tabela. `AutoCarregamento.php` é um arquivo físico fixo, não um exemplo hipotético. A exceção de composição desse nome está em [Casos especiais](#casos-especiais). O arquivo fixo `index.php` segue [Grafias externas](#grafias-externas).

Classes de cenários de teste DEVEM usar a fórmula `[Finalidade]Teste`, em arquivo `[Finalidade]Teste.php`, em que `Finalidade` identifica a unidade ou o cenário verificado. Exemplo hipotético de escrita: `LeituraArquivoTeste`, no arquivo `LeituraArquivoTeste.php`. Classes auxiliares dos testes DEVEM seguir `[Finalidade]`, em `[Finalidade].php`; é PROIBIDO terminar seu nome em `Teste`, sufixo pelo qual a configuração da suíte identifica os cenários. O arquivo de preparação compartilhada da suíte tem o caminho fixo prescrito `Testes/Apoio/Preparacao.php`, conforme [Testes](02Estrutura.md#testes). O prefixo de espaço de nomes dos testes está em [Subpastas e espaços de nomes](02Estrutura.md#subpastas-e-espaços-de-nomes).

### Nomes por responsabilidade

As divisões de [Divisões de responsabilidade](02Estrutura.md#divisões-de-responsabilidade) determinam o destino, sem impor sufixos adicionais aos nomes. Classes de `Controles`, `Servicos`, `Manipuladores`, `Provedores`, `Validacoes` e das demais divisões DEVEM usar `[Finalidade]`, em arquivo `[Finalidade].php`. Exemplo hipotético de escrita: `LeituraArquivo`, em `LeituraArquivo.php`; o nome não determina por si só a divisão responsável.

É PERMITIDO incluir uma palavra de responsabilidade na finalidade quando ela esclarece o nome, obedecendo ao formato geral. É PROIBIDO deduzir um sufixo obrigatório de exemplos, de outra área ou de uma convenção externa. Permanecem obrigatórios o sufixo `Teste` dos cenários, as fórmulas dos scripts de banco e os nomes físicos fixos explicitamente identificados.

### Siglas

Siglas DEVEM respeitar o formato do elemento. A tabela associa cada condição à grafia aplicável; suas fórmulas descrevem os exemplos escolhidos, sem fixar a quantidade de palavras ou siglas.

| Condição | Grafia obrigatória | Fórmula aplicada ao exemplo | Exemplo hipotético de escrita |
| --- | --- | --- | --- |
| Nome com inicial maiúscula, sem separadores | Siglas em maiúsculas, inclusive no início | `[SIGLA][Finalidade]`, `[Finalidade][SIGLA]` | `HTTPCliente`, `LeitorCSV` |
| Sigla inicial em nome com primeira palavra minúscula, sem separadores | Toda a sigla inicial em minúsculas | `[sigla][Finalidade]` | `httpCliente` |
| Sigla após a primeira palavra em nome com primeira palavra minúscula, sem separadores | Sigla em maiúsculas | `[finalidade][SIGLA]` | `clienteHTTP` |
| Siglas consecutivas após uma palavra ou em nome com inicial maiúscula, sem separadores | Siglas em maiúsculas | `[Finalidade][SIGLA][SIGLA]` | `LeitorXMLHTTP` |
| Grupo de siglas no início de nome com primeira palavra minúscula, sem separadores | Todo o grupo inicial em minúsculas | `[siglas][Finalidade]` | `xmlhttpCliente` |
| Formato em maiúsculas com sublinhado, como constantes | Maiúsculas e separadores do elemento | `[FINALIDADE]` | `HTTP_CLIENTE`, `LEITOR_XML_HTTP` |
| Formato em minúsculas com sublinhado, como nomes do banco | Minúsculas e separadores do elemento | `[finalidade]` | `leitor_xml_http` |
| Formato em minúsculas com hífen, como classes HTML | Minúsculas e separadores do elemento | `[finalidade]` | `leitor-xml-http` |

Quando siglas consecutivas tornarem os limites ou a finalidade ambíguos, separar as siglas por palavras completas que esclareçam o nome. Para a fórmula `[Finalidade][SIGLA][Finalidade][SIGLA]`, o exemplo hipotético de escrita é `ConversorXMLRespostaHTTP`.

### Grafias externas

Nomes externos preservados DEVEM usar a fórmula `[GrafiaOrigem]`: a grafia exata da origem, sem tradução nem ajuste de maiúsculas, separadores ou extensão. `README.md`, `AGENTS.md`, `LICENSE` e `index.php` são arquivos existentes que seguem essa forma. O alcance dessa forma está em [Idioma do projeto](00Regras.md#idioma-do-projeto).

O caminho `Configuracoes/Aplicacao/.env` também preserva a convenção externa do nome `.env`. É o destino permitido e não versionado para parâmetros privados, conforme [Parâmetros da aplicação](03Configuracoes.md#parâmetros-da-aplicação), e não identifica arquivo existente.

As grafias exigidas pelas ferramentas de ambiente estão definidas nos documentos de [Ferramentas de ambiente](02Estrutura.md#ferramentas-de-ambiente).

### Pastas de distribuições externas

A identificação de uma distribuição externa admite as fórmulas `[Nome-Versao]` e `[Nome]`. Compor `Nome` a partir das palavras do nome oficial conforme o formato geral de pasta, sem espaços e sem abreviações próprias. Exemplos hipotéticos de escrita: `BibliotecaExemplo-2.0.0` e `BibliotecaExemplo`, para o nome hipotético “Biblioteca Exemplo”. `Versao` conserva o identificador publicado pelo fornecedor, incluindo seus pontos; o hífen separa as partes.

É PERMITIDO omitir a versão somente quando o caminho permanece estável e recebe uma única versão instalada por vez. O nome DEVE incluir a versão quando o consumidor referencia uma distribuição específica ou quando há coexistência de versões. A ficha DEVE registrar a escolha e a versão instalada mesmo quando o caminho não a inclui.

Esse formato vale para as pastas de identificação criadas pelo projeto em `Externos` e nas famílias de `Publico/Ativos`. Uma distribuição dividida entre famílias DEVE manter o mesmo nome de pasta em cada uma. As fórmulas do caminho são `[FamiliaAtivos]/[Nome-Versao]` e `[FamiliaAtivos]/[Nome]`, conforme as condições anteriores; exemplos hipotéticos de escrita: `Publico/Ativos/CSS/BibliotecaExemplo-2.0.0` e `Publico/Ativos/CSS/BibliotecaExemplo`. É PROIBIDO abreviar a versão em uma subpasta própria, como na fórmula incorreta `[FamiliaAtivos]/[Nome]/V[VersaoSemPontos]` (exemplo hipotético de escrita incorreta: `Publico/Ativos/CSS/BibliotecaExemplo/V200`). Nomes internos fornecidos pelo terceiro seguem [Grafias externas](#grafias-externas). A organização e a identificação da distribuição estão em [Organização](05Externos.md#organização).

### Caminhos HTTP

Caminhos definidos pelo projeto usam palavras minúsculas, sem acentos e sem cedilha, separadas por hífen. Segmentos são separados por barra. A raiz é `/`; os demais caminhos não terminam em barra nem recebem extensão de arquivo PHP.

As fórmulas são `/[segmento]`, `/[segmento-composto]` e `/[segmento]/{[parametro]}`. Os caminhos abaixo são exemplos hipotéticos de escrita, não rotas implementadas nem operações previstas:

```text
/arquivos
/arquivos-temporarios
/arquivos/{identificador}
/arquivos/{identificadorArquivo}
```

Nos exemplos hipotéticos, `{identificador}` e `{identificadorArquivo}` representam a escrita de parâmetros de caminho. Seus nomes DEVEM seguir o formato de parâmetro: primeira palavra com inicial minúscula e seguintes com inicial maiúscula. O hífen dos segmentos literais não se aplica ao nome declarado do parâmetro. A sintaxe de declaração pertence ao contrato do roteador, sem pressupor sua implementação. Métodos HTTP conservam os nomes do protocolo, como `GET` e `POST`. Endereços definidos por integração externa mantêm a forma dessa integração.

Os caminhos concretos, o prefixo de URL e a estratégia de versão de cada área web são definidos no detalhamento da operação ou do componente. É PERMITIDO que o prefixo de URL difira do nome da pasta da área.

### Ativos públicos

A fórmula de localização de um ativo próprio é `Publico/Ativos/[Familia]/[Finalidade].[extensao]`, com os formatos gerais de cada elemento. Subpastas de assunto são OPCIONAIS; com elas, a fórmula é `Publico/Ativos/[Familia]/[Subpastas]/[Finalidade].[extensao]`. Exemplos hipotéticos de escrita, sem correspondência com arquivos existentes:

```text
Publico/Ativos/CSS/Site/TemaPrincipal.css
Publico/Ativos/Imagens/Marca/Logo.svg
```

Recursos de terceiros instalados nas famílias seguem a fórmula `Publico/Ativos/[Familia]/[Distribuicao]/[CaminhoOriginal]`, em que `Distribuicao` segue [Pastas de distribuições externas](#pastas-de-distribuições-externas) e `CaminhoOriginal` preserva as subpastas e os nomes fornecidos pelo terceiro. Exemplo hipotético de escrita: `Publico/Ativos/Fontes/Inter/Inter-Regular.woff2`. As famílias, seus formatos e o uso de subpastas estão em [Ativos](02Estrutura.md#ativos).

Arquivos minificados mantidos como arquivo DEVEM usar a fórmula `[Finalidade].min.[extensao]`; exemplo hipotético de escrita: `TemaPrincipal.min.css`. Versões minificadas fornecidas por terceiros mantêm o nome de origem. A marca `.min` é uma convenção externa adotada e não autoriza abreviar outras palavras dos nomes próprios. O que é entregue ao usuário segue [Arquivos entregues ao usuário](00Regras.md#arquivos-entregues-ao-usuário).

O formato de nome não autoriza um novo destino ou formato de entrega. A inclusão de HTML estático exige a definição de sua localização conforme [Publico](02Estrutura.md#publico) e [Ampliações & Adaptações](00Regras.md#ampliações--adaptações); o HTML produzido pelas visões PHP é tratado na geração da resposta.

### Arquivos das ferramentas

Na pasta `Ferramentas/[NomeFerramenta]`, o executável DEVE usar a fórmula `[NomeFerramenta].[extensao]`. A configuração própria, quando existir, DEVE usar a mesma fórmula, com a extensão exigida pela ferramenta. O documento de uso fica em `Documentacao/Ferramentas/[NomeFerramenta].md`, conforme [Nomes dos documentos](#nomes-dos-documentos). A automação localiza o executável e o documento por esse nome.

Os arquivos existentes e prescritos de cada ferramenta ficam identificados em seu documento de uso, não neste documento.

### Casos especiais

Aplicar cada formato específico somente ao alcance declarado. Fora dele, permanece o formato geral, conforme [Definições](00Regras.md#definições). A tabela indica os casos já definidos e a fonte responsável por sua escrita.

| Situação | Forma aplicável e alcance |
| --- | --- |
| Arquivo fixo de autocarregamento | Conservar `AutoCarregamento.php`. Nos textos para leitura, usar `autocarregamento`; a divisão do prefixo `auto` é uma exceção restrita ao nome desse arquivo existente. |
| Preparação compartilhada dos testes | Usar o caminho prescrito `Testes/Apoio/Preparacao.php`, obrigatório a partir do primeiro cenário, conforme [Testes](02Estrutura.md#testes). |
| Nomes recebidos de terceiros ou exigidos externamente | Preservar a grafia de origem nas condições de [Grafias externas](#grafias-externas). Essa permissão não se estende aos nomes próprios fora desse alcance. |
| Pastas de identificação de distribuições externas | Aplicar [Pastas de distribuições externas](#pastas-de-distribuições-externas), com versão somente nas condições definidas ali. |
| Arquivos minificados | Aplicar a marca de minificação conforme [Ativos públicos](#ativos-públicos). Nomes fornecidos por terceiros conservam a grafia de origem. |
| Classes de cenários de teste | Acrescentar o sufixo obrigatório conforme [Formato por elemento](#formato-por-elemento). Classes auxiliares não usam esse sufixo. |
| Documentos próprios | Aplicar os prefixos e as composições de [Nomes dos documentos](#nomes-dos-documentos) somente aos tipos de documento indicados. |
| Scripts de esquema e carga | Aplicar [Nomes dos scripts de banco de dados](#nomes-dos-scripts-de-banco-de-dados) em lugar do formato geral de arquivo próprio. |
| Executáveis e configurações das ferramentas | Aplicar [Arquivos das ferramentas](#arquivos-das-ferramentas), com o nome da ferramenta como finalidade. |
| Branches | Aplicar os nomes e o fluxo definidos em [Branches do projeto](Ambiente/GitHub.md#branches-do-projeto). |

## Nomes dos documentos

Os documentos gerais fixos na raiz de `Documentacao` DEVEM usar a fórmula `[Numero][Assunto].md`. Os seis nomes abaixo formam o conjunto existente desses documentos, não exemplos hipotéticos:

```text
00Regras.md
01Padroes.md
02Estrutura.md
03Configuracoes.md
04Ferramentas.md
05Externos.md
```

A numeração indica a ordem de leitura desses documentos fixos, com números únicos e largura uniforme de pelo menos dois dígitos. Zeros à esquerda completam a largura. Intervalos entre números são válidos. O número é seguido diretamente pelo assunto. Essa numeração não se aplica aos documentos acrescentados para detalhar componentes, testes, adaptações ou outros assuntos.

Os demais documentos próprios de `Documentacao` DEVEM usar a fórmula `[Assunto].md`, sem prefixo numérico. O nome fixo `Memorial.md` identifica o histórico e o estado atual do projeto, no destino definido em [Documentacao](02Estrutura.md#documentacao). O nome identifica um assunto único entre os documentos próprios de `Documentacao`, desconsiderando os prefixos dos documentos fixos e a extensão. A subdivisão usa a fórmula `[AssuntoPrincipal][Tema].md`; exemplos hipotéticos de escrita: `SiteRotas.md` e `SiteAcesso.md`.

Esses nomes são exemplos de composição, não arquivos obrigatórios nem documentos existentes. Antes de criar um detalhamento, delimitar seu conteúdo conforme [Onde documentar cada assunto](00Regras.md#onde-documentar-cada-assunto).

Para áreas e para o Núcleo, o caminho usa a fórmula `Documentacao/Aplicacao/[Area]/[Area][Tema].md`, com nome de arquivo `[Area][Tema].md` (exemplos hipotéticos de escrita: `SiteRotas.md`, `APIOperacoes.md` e `NucleoCarregamento.md`). Para testes, o caminho usa a fórmula `Documentacao/Testes/[Categoria]/Testes[Categoria][Tema].md` ou, quando houver subpastas espelhadas, `Documentacao/Testes/[Categoria]/[Subpastas]/Testes[Categoria][Tema].md`, com nome de arquivo `Testes[Categoria][Tema].md` (exemplos hipotéticos de escrita: `TestesApoioAmbiente.md`, `TestesUnitarioConversoes.md` e `TestesIntegracaoHTTP.md`). `Area` representa o nome da pasta da área ou `Nucleo`; `Categoria`, o nome da pasta da categoria de testes.

O `README.md` de pasta sem conteúdo apresenta sua finalidade e referencia a seção do documento geral ou do documento de ferramenta de ambiente que define seu uso, conforme [Pastas sem conteúdo](00Regras.md#pastas-sem-conteúdo). Não recebe prefixo numérico nem participa da comparação de nomes únicos. Seu título é o nome da pasta na forma de leitura, com acentos e espaços, ou uma descrição curta de sua finalidade. O README da raiz e os distribuídos por terceiros seguem as exceções dessa regra.

Os documentos próprios das ferramentas DEVEM usar a fórmula `Documentacao/Ferramentas/[NomeFerramenta].md` e descrever seu uso e instalação. As fichas próprias de recursos de terceiros DEVEM usar a fórmula `Ficha[Nome].md`, em que `[Nome]` é o mesmo `Nome` da pasta de identificação da distribuição, sem a versão, conforme [Pastas de distribuições externas](#pastas-de-distribuições-externas). A ficha de uma biblioteca de `Externos` fica em `Documentacao/Externos/Ficha[Nome].md`; a de um recurso de terceiros instalado em `Publico`, em `Documentacao/Publico/Externos/Ficha[Nome].md`. Exemplos hipotéticos de escrita: `Documentacao/Externos/FichaBiblioteca.md` e `Documentacao/Publico/Externos/FichaIcones.md`. As fichas identificam a distribuição e sua integração. Os destinos e seus limites estão em [Fichas e documentos de ferramentas](02Estrutura.md#fichas-e-documentos-de-ferramentas). Os documentos de `Documentacao/Ambiente` DEVEM usar a fórmula `[NomeFerramenta].md`, com o nome da ferramenta de ambiente. Esses formatos específicos não usam numeração; o nome da ferramenta ou do recurso segue a composição definida neste documento. Documentos fornecidos pelo terceiro conservam a grafia de origem. Os destinos estão em [Organização](04Ferramentas.md#organização) de Ferramentas e [Organização](05Externos.md#organização) de Externos.

Correspondência entre caminho e título; a situação física está explícita em cada linha:

| Arquivo | Título | Situação |
| --- | --- | --- |
| `Documentacao/01Padroes.md` | Padrões | Existente |
| `Documentacao/03Configuracoes.md` | Configurações | Existente |
| `Documentacao/Aplicacao/Site/SiteRotas.md` | Rotas do Site | Exemplo hipotético de escrita, conforme a fórmula de detalhamento de área |

## Nomes dos scripts de banco de dados

Os arquivos de `Dados/Esquemas` e `Dados/Cargas` DEVEM usar as fórmulas abaixo, em lugar do formato de arquivo próprio. Cada prefixo vale somente na pasta indicada. Os nomes da última coluna são exemplos hipotéticos de escrita.

| Conteúdo | Pasta | Fórmula do nome | Exemplo hipotético de escrita |
| --- | --- | --- | --- |
| Instalação do esquema | `Esquemas` | `I.AAAA.MM.DD.SSSS.ext` | `I.2026.09.15.0001.sql` |
| Manutenção do esquema, incluindo a transformação de dados inseparável dela | `Esquemas` | `M.AAAA.MM.DD.SSSS.ext` | `M.2026.09.16.0001.sql` |
| Reversão de manutenção do esquema | `Esquemas` | `R.AAAA.MM.DD.SSSS.ext` | `R.2026.09.16.0001.sql` |
| Carga inicial | `Cargas` | `I.AAAA.MM.DD.SSSS.ext` | `I.2026.09.15.0001.sql` |
| Correção ou migração exclusivamente de dados existentes | `Cargas` | `M.AAAA.MM.DD.SSSS.ext` | `M.2026.09.16.0001.sql` |
| Reversão de correção ou migração de dados | `Cargas` | `R.AAAA.MM.DD.SSSS.ext` | `R.2026.09.16.0001.sql` |
| Carga de dados fictícios de teste | `Cargas` | `T.AAAA.MM.DD.SSSS.ext` | `T.2026.09.15.0001.sql` |

`AAAA.MM.DD` é a data; `SSSS`, a sequência de quatro dígitos dentro da data e do prefixo; `ext`, a extensão minúscula. Uma reversão usa a mesma data e sequência da manutenção correspondente.

Os prefixos `I`, `M` e `R` se repetem em Esquemas e Cargas porque essas pastas têm sequências independentes; a pasta distingue a instalação do esquema da carga inicial e a manutenção do esquema da manutenção dos dados. O prefixo `T` é exclusivo de `Cargas`. Os destinos estão em [Esquemas e cargas](02Estrutura.md#esquemas-e-cargas).

## Documentação nos arquivos com comentários

Usar comentários no próprio arquivo para descrever sua finalidade e os detalhes necessários à manutenção, em todos os formatos que permitam comentários. A apresentação geral fica no início do arquivo; a documentação de classes, funções e métodos fica junto de suas declarações; a descrição de variáveis e de trechos de lógica fica próxima do código correspondente.

Identificar nomes e tipos, significado dos parâmetros e variáveis, valores aceitos, retornos, dependências, falhas e efeitos quando se aplicarem. A divisão entre os detalhes no código e a visão do conjunto em Markdown segue [Documentação do código e do projeto](00Regras.md#documentação-do-código-e-do-projeto).

O comentário de apresentação do arquivo inclui os campos `Redator`, `Data de criação`, `Status` e `Data do Status`, um por linha, com o preenchimento descrito em [Cabeçalho](#cabeçalho). O status e as datas se referem à documentação do arquivo fonte, conforme [Revisão da documentação](00Regras.md#revisão-da-documentação).

Usar a sintaxe válida para o formato do arquivo:

| Formato | Comentário de documentação |
| --- | --- |
| PHP, CSS e JavaScript | Bloco `/* ... */` ou `/** ... */` |
| HTML e XML, incluindo SVG | Bloco `<!-- ... -->` |
| SQL | Linhas iniciadas por `--` ou bloco `/* ... */`, conforme o gerenciador |
| Configurações | Sintaxe de comentário aceita pelo leitor do arquivo, como linhas iniciadas por `#` quando houver suporte |

Em arquivos PHP que produzem HTML, a documentação interna DEVE usar comentários PHP e permanecer fora da resposta. O tratamento dos comentários nos arquivos entregues ao navegador segue [Arquivos entregues ao usuário](00Regras.md#arquivos-entregues-ao-usuário).

O comentário de apresentação obedece à sequência `Finalidade` → `Redator` → `Data de criação` → `Status` → `Data do Status`. O bloco abaixo é um modelo sintático hipotético de documentação PHP, não uma implementação existente:

```php
<?php

/**
 * Finalidade do arquivo.
 *
 * Redator: Nome
 * Data de criação: DD/MM/AAAA
 * Status: Sem revisão
 * Data do Status: DD/MM/AAAA
 */
```

Arquivos ainda sem código ou documentação não recebem comentários apenas para reservar o cabeçalho; os campos são acrescentados quando sua documentação for escrita. Arquivos minificados entregues ao usuário não repetem esse cabeçalho: a documentação e seu status são mantidos no arquivo fonte correspondente.

Scripts de banco já incorporados a `Principal` preservam seu conteúdo histórico, incluindo comentários e metadados. As correções documentais desses scripts ficam no Markdown do assunto correspondente, conforme [Cuidados de manutenção](00Regras.md#cuidados-de-manutenção).

Quando o formato não aceitar comentários e houver informações necessárias para seu uso ou manutenção, documentar o arquivo no Markdown do assunto correspondente, identificando seu caminho e mantendo os metadados nesse documento, conforme [Documentação do código e do projeto](00Regras.md#documentação-do-código-e-do-projeto). Não acrescentar comentários inválidos a um formato que não os suporte.

## Leis de escrita

Toda escrita ou atualização de documentação própria DEVE cumprir as cinco leis abaixo, nos documentos Markdown e nos comentários de documentação dos arquivos. Documentação original de terceiros não está sujeita a elas.

1. **Voz própria e definitiva.** Escrever no presente do indicativo, tratando o projeto como sistema único e próprio, como em “Este diretório armazena…” e “A arquitetura adota…”. É PROIBIDO apresentar a documentação como base, modelo, esqueleto reutilizável ou instrução de adoção por outro projeto, e usar expressões como “nos futuros projetos” ou “adapte conforme o sistema”. A voz definitiva não declara implementado o que não existe; a situação física segue a lei 3.
2. **Escopo estrutural nos documentos gerais.** Os documentos numerados, os de ferramentas de ambiente e os READMEs de pasta documentam exclusivamente a árvore de diretórios, a responsabilidade de cada pasta, os contratos entre divisões e os padrões de nomenclatura e de manutenção. É PROIBIDO descrever neles regras de negócio, tabelas de domínio, endpoints ou implementações. Esse conteúdo pertence aos detalhamentos de `Documentacao/Aplicacao` e `Documentacao/Testes`, somente depois de implementado, conforme [Conteúdo dos detalhamentos](#conteúdo-dos-detalhamentos). Fichas e documentos de ferramenta descrevem somente a instalação efetiva.
3. **Fórmula antes do exemplo.** Todo nome usado para ilustrar um padrão DEVE ser precedido pela fórmula, com as partes substituíveis entre colchetes, e rotulado como `exemplo hipotético de escrita`, na mesma frase ou na frase seguinte. Exemplo hipotético de escrita da forma: “Fórmula `[Finalidade].php` (exemplo hipotético de escrita: `LeituraArquivo.php`).” É PROIBIDO citar solto um nome fictício que pareça arquivo existente ou nome obrigatório, ou usar exemplos para declarar funções, tabelas, operações ou integrações inexistentes. Toda descrição DEVE distinguir diretórios e arquivos fixos, destinos criados sob demanda e exemplos hipotéticos: listagens físicas usam os caminhos reais, e catálogos de destinos declaram que seus diretórios são criados com o primeiro arquivo, sem afirmar sua presença ou ausência. A situação física variável RESIDE EXCLUSIVAMENTE EM [Estado atual](Memorial.md#estado-atual), nos documentos de ferramentas, nos documentos de ferramentas de ambiente e nas fichas.
4. **Força normativa determinística.** Obrigações usam `DEVE`, `DEVEM`, `É OBRIGATÓRIO`, `RESIDE EXCLUSIVAMENTE EM` ou ordem no infinitivo; proibições usam `É PROIBIDO` ou instrução negativa. É PROIBIDO escrever regra da arquitetura com linguagem de sugestão, como “recomenda-se”, “é uma boa prática”, “idealmente”, “costuma ficar em”, “pode ser colocado” ou “sugere-se”. `OPCIONAL` e `PERMITIDO` identificam somente liberdade de escolha real, com seu alcance. O sentido de cada forma está em [Linguagem e exemplos](#linguagem-e-exemplos).
5. **Fronteiras excludentes e coerência física.** A responsabilidade de cada pasta DEVE declarar o que entra nela e o que não entra, sem sobreposição com as pastas vizinhas, como na fronteira entre `Servicos`, `Comuns/Classes` e `Comuns/Funcoes` de [Divisões de responsabilidade](02Estrutura.md#divisões-de-responsabilidade). Toda pasta apresentada como fixa DEVE corresponder exatamente ao nome e à hierarquia da árvore real, sem contradizer outro documento; cada definição tem uma única fonte, conforme [Definições](00Regras.md#definições).

## Estrutura dos documentos

Documentos próprios externos ao código DEVEM usar Markdown, extensão `.md` e codificação UTF-8. A sequência obrigatória é `Título` → `Cabeçalho` → `Objetivo` → `Seções do assunto`. O bloco abaixo é um modelo sintático hipotético de documento, sem arquivo correspondente:

```markdown
# Assunto

| Redator | Data de criação | Status      | Data do Status |
| ------- | --------------- | ----------- | -------------- |
| Nome    | DD/MM/AAAA      | Sem revisão | DD/MM/AAAA     |

## Objetivo

O que este documento apresenta e a que contexto se aplica.

## Título descritivo

Desenvolvimento do assunto.
```

### Cabeçalho

`Nome` identifica a pessoa responsável pelo texto e `DD/MM/AAAA` representa uma data. O cabeçalho usa uma tabela com as quatro colunas acima, logo após o título. Preservar essa identificação e a data de criação conhecidas; os autores e as contribuições permanecem identificados no histórico, conforme [Manutenção dos documentos](00Regras.md#manutenção-dos-documentos).

As colunas da tabela do cabeçalho são alinhadas com espaços para leitura em texto puro. Essa regra de alinhamento vale somente para o cabeçalho.

| Campo | Preenchimento |
| --- | --- |
| Redator | Pessoa responsável pelo texto, inclusive quando utiliza assistência de ferramentas |
| Data de criação | Data em que o documento foi criado |
| Status | `Sem revisão`, `Em revisão` ou `Revisado` |
| Data do Status | Data da criação do documento ou da última mudança de status |

O status identifica a fase da revisão documental da versão atual; sua data identifica o início desse estado. O preenchimento e as transições seguem [Revisão da documentação](00Regras.md#revisão-da-documentação).

As datas usam `DD/MM/AAAA`. As permissões de merge seguem [Trabalho local e permissões no remoto](Ambiente/GitHub.md#trabalho-local-e-permissões-no-remoto). Documentação original distribuída por terceiros conserva seu formato; fichas e documentos escritos pelo projeto seguem este padrão.

### Em resumo

Os documentos numerados da raiz de `Documentacao` têm, logo depois de `## Objetivo`, a seção `## Em resumo`. Ela reúne até 7 itens, cada um com uma frase e um link para a seção que contém a definição. O resumo não cria regra própria. READMEs, fichas e documentos de ferramenta não têm essa seção.

### README de pasta sem conteúdo

O README de pasta sem conteúdo DEVE seguir a sequência `Título da pasta` → `Cabeçalho` → `Objetivo` → `Referências`, com a finalidade da pasta e um link relativo para a seção que define seu uso. O bloco abaixo é um modelo sintático hipotético. Os campos do cabeçalho seguem [Cabeçalho](#cabeçalho); o ciclo de existência do arquivo segue [Pastas sem conteúdo](00Regras.md#pastas-sem-conteúdo).

```markdown
# Nome da pasta

| Redator | Data de criação | Status      | Data do Status |
| ------- | --------------- | ----------- | -------------- |
| Nome    | DD/MM/AAAA      | Sem revisão | DD/MM/AAAA     |

## Objetivo

O diretório destina-se ao conteúdo correspondente à sua responsabilidade; este README mantém a pasta no Git enquanto ela não contém outros arquivos versionados.

## Referências

O uso desta pasta está definido em [Título da seção]([caminhoRelativo]#[ancora]).
```

No bloco hipotético, a fórmula de referência é `[Título da seção]([caminhoRelativo]#[ancora])`; os campos entre colchetes representam valores substituíveis. Substituir a frase de finalidade pelo conteúdo específico da pasta e o destino da referência pela seção do documento geral ou do documento de ferramenta de ambiente correspondente. É PROIBIDO manter campos substituíveis no README efetivo. O modelo contém somente título, cabeçalho, objetivo e referências.

### Objetivo

Na seção `## Objetivo`, escrever primeiro uma frase que diga o que o documento apresenta e a qual recurso, componente ou situação se aplica. Quando outro documento tratar de um assunto relacionado necessário à compreensão ou ao uso, acrescentar uma segunda frase com o link, o assunto e o tipo de conteúdo disponível no destino. Usar termos que deixem explícito o alcance da referência, como “bibliotecas e recursos de terceiros” para dependências externas. READMEs de pasta sem conteúdo seguem o [README de pasta sem conteúdo](#readme-de-pasta-sem-conteúdo).

A localização das fichas está em [Nomes dos documentos](#nomes-dos-documentos). Para a fórmula `Documentacao/Externos/Ficha[Nome].md` (exemplo hipotético de escrita: `Documentacao/Externos/FichaBiblioteca.md`), o bloco seguinte demonstra somente a sintaxe de seu objetivo e de um link relativo; não documenta uma biblioteca instalada:

```markdown
## Objetivo

Identificar a distribuição local da biblioteca e a situação de sua integração com a aplicação. As orientações gerais para organizar e utilizar bibliotecas e recursos de terceiros no projeto estão em [Externos](../05Externos.md).
```

Nesse exemplo, a primeira frase delimita o conteúdo da ficha à biblioteca instalada no projeto. A segunda informa que o documento de destino contém orientações gerais de organização e uso de recursos de terceiros.

### Desenvolvimento

As seções seguem a ordem de compreensão e uso: termos antes dos conceitos que dependem deles; pré-requisitos antes dos passos; exemplos depois da explicação.

| Assunto | Conteúdo da explicação |
| --- | --- |
| Pasta ou componente | Finalidade, organização, responsabilidades e destinos relacionados |
| Configuração | Parâmetros, tipos, obrigatoriedade, valores aceitos, padrão e efeitos |
| Procedimento | Pré-requisitos, passos, resultado e conferência |
| Interface ou contrato | Entradas, saídas, restrições e erros |
| Conceito | Definição, relações e exemplos |

A tabela apresenta formas de desenvolvimento, sem uma lista fixa de seções para todos os assuntos. Os títulos descrevem seu conteúdo.

### Hierarquia e apresentação

- Um único título de nível 1 (`#`) identifica o documento.
- Títulos de nível 2 (`##`) identificam seções principais.
- Subdivisões usam os níveis seguintes, sem saltar níveis.
- Listas numeradas apresentam passos ou itens ordenados.
- Listas sem numeração apresentam itens sem ordem obrigatória.
- Tabelas apresentam itens com os mesmos campos de comparação.
- Código em linha identifica nomes literais, caminhos, comandos e valores.
- Blocos indicam sua linguagem; árvores de pastas usam `text`.

### Mapas de estrutura

Os desenhos de estrutura apresentam as pastas e sua hierarquia. As subpastas livres definidas em [Mapa geral](02Estrutura.md#mapa-geral) não aparecem nos desenhos. Arquivos aparecem com nome somente quando têm função operacional e nome e localização fixos, como `Publico/index.php` e `Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php`. O mapa geral também apresenta os seis documentos numerados e `Documentacao/Memorial.md`, identificados em [Nomes dos documentos](#nomes-dos-documentos).

Os demais documentos Markdown, READMEs, fichas e exemplos de classes não aparecem como arquivos nos desenhos. É PERMITIDO apresentar seus nomes no texto, em tabelas, links ou exemplos específicos de nomenclatura. A ausência desses arquivos no desenho não dispensa sua existência quando prevista.

### Linguagem e exemplos

Frases diretas identificam o elemento descrito e a condição de aplicação. Termos técnicos recebem explicação na primeira ocorrência ou link para sua definição.

| Forma | Sentido |
| --- | --- |
| `DEVE`, `DEVEM`, `É OBRIGATÓRIO`, `RESIDE EXCLUSIVAMENTE EM`, `exige` e instrução afirmativa no infinitivo, como “Conferir os links” e “Preparar a recuperação” | Obrigação no alcance e nas condições declarados; uma permissão DEVE ser expressa separadamente |
| `É PROIBIDO`, `NÃO DEVE` e instrução negativa, como “Não expor credenciais” | Proibição |
| `OPCIONAL` | Escolha dispensável no alcance explicitamente declarado |
| `PERMITIDO` | Alternativa autorizada nas condições explicitamente declaradas |

A força normativa é determinística: `DEVE`, `DEVEM`, `É OBRIGATÓRIO`, `RESIDE EXCLUSIVAMENTE EM` e `É PROIBIDO` destacam regras obrigatórias em maiúsculas; `OPCIONAL` e `PERMITIDO` destacam liberdade de escolha real.

A forma dos exemplos de nomenclatura e a distinção entre caminhos existentes, previstos e hipotéticos seguem a lei 3 de [Leis de escrita](#leis-de-escrita).

Regras de redação:

- Escrever frases e parágrafos curtos. Cerca de 25 palavras por frase e quatro frases por parágrafo são referências de leitura, não critérios de aprovação.
- Apresentar primeiro o caminho principal. Situações excepcionais ficam em uma subseção `Casos especiais` no fim da seção correspondente.
- Dizer cada aviso ou princípio uma única vez, no documento responsável; as demais menções apontam para ele.
- Usar fórmulas genéricas nas regras de organização de recursos substituíveis. Listagens físicas DEVEM usar os nomes reais dos caminhos existentes; versões e dados de instalação pertencem ao documento da ferramenta ou à ficha do recurso, conforme o caso.

Exemplos são identificados como exemplos. Valores substituíveis têm seu significado explicado. Exemplos executáveis informam a pasta de execução, os parâmetros necessários e o resultado esperado.

### Caminhos e referências

Caminhos no texto são relativos à raiz do projeto, salvo indicação de outro diretório de referência. Links são relativos ao documento que os contém e preservam a grafia do destino, inclusive maiúsculas e minúsculas.

Cada referência a seção usa um único link na fórmula `[[Título da seção]]([caminhoRelativo]#[ancora])` (exemplo hipotético de escrita: `[Composição dos nomes](01Padroes.md#composição-dos-nomes)`). Quando o link aponta para um documento Markdown inteiro, o texto DEVE ser o título de nível 1 do documento; quando aponta para um arquivo sem título Markdown, como `LICENSE` ou arquivo de licença distribuído por terceiro, o texto identifica o arquivo ou os termos correspondentes.

Referências aparecem junto da explicação que depende delas. É PERMITIDO reunir em um glossário ao final os termos já explicados ou referenciados antes de seu uso; ele serve à consulta e não substitui a apresentação necessária para compreender os conceitos anteriores.

## Conteúdo dos detalhamentos

Os detalhamentos DEVEM descrever somente componentes implementados e seus contratos concretos. Os campos abaixo são requisitos documentais, não declarações de componentes existentes. É PROIBIDO preencher esses campos com operações, parâmetros ou resultados inventados. A organização e os nomes seguem [Nomes dos documentos](#nomes-dos-documentos) e [Documentacao](02Estrutura.md#documentacao). É PERMITIDO acrescentar informações próprias do assunto sem repetir as definições gerais.

Requisitos e critérios de aceitação anteriores à implementação pertencem ao canal de revisão, conforme [Desenvolvimento seguro](00Regras.md#desenvolvimento-seguro) e [Testes antes da implementação](00Regras.md#testes-antes-da-implementação). Eles não autorizam apresentar um contrato planejado como funcionamento existente nos detalhamentos.

### Recursos técnicos

A documentação da área responsável apresenta os recursos técnicos em conjunto e as informações necessárias para utilizá-los. Os itens abaixo orientam essa visão de uso; não exigem repetir assinaturas, variáveis ou lógica interna dos arquivos, nem determinam a localização dos recursos.

| Recurso | Informações de uso do conjunto |
| --- | --- |
| Configuração | Fontes, gramática, precedência, tipos e falhas |
| Dependências | Registro, construção, duração das instâncias e resolução de contratos |
| Roteamento | Declaração, parâmetros, conflitos, métodos aceitos e respostas de erro |
| Renderização | Seleção da visão, dados explícitos e escape de saída |
| Logs | Campos, níveis, destino, fuso, sigilo e retenção |
| Banco | Contrato de conexão, parâmetros, transações e erros |
| Comunicação externa | Entrada, saída, tempo limite, falhas e condições de repetição |
| Eventos | Publicação, associação de ouvintes, ordem e propagação de falhas |
| Trabalhos | Execução imediata ou adiada, tentativas e repetição segura |

### Operação de API

| Item | Descrição |
| --- | --- |
| Endereço | Método, caminho e parâmetros |
| Entrada | Tipo de conteúdo, campos, tipos, obrigatoriedade e limites |
| Acesso | Autenticação, autorização e condições de uso |
| Resultado | Status, cabeçalhos e formato do corpo |
| Falhas | Formato de erro, identificação e situações possíveis |
| Coleções | Paginação, filtros e ordenação quando oferecidos |
| Repetição | Efeito de reenviar uma operação que altera dados |
| Compatibilidade | Versão e alterações aceitas pelos consumidores, quando houver versionamento |

Os exemplos usam dados fictícios e apresentam sucesso e falhas previstas. Tokens, credenciais e detalhes internos não aparecem nos exemplos públicos. Atualizações dos contratos seguem [Cuidados de manutenção](00Regras.md#cuidados-de-manutenção).

### Operação de interface web

O detalhamento de cada operação de uma área com interface web, como o Site e o Painel, identifica:

- Método, caminho, parâmetros e formatos de entrada;
- Condições de acesso, papéis, autenticação, autorização e filtros aplicáveis;
- Validação, serviço utilizado e resultados possíveis;
- Status, cabeçalhos, redirecionamento ou visão;
- Dados entregues à visão, mensagens de falha e comportamento em erro;
- Conflitos e efeitos persistentes nas alterações de dados;
- Testes de sucesso, erro e restrição de acesso.

A documentação de acesso descreve encerramento de sessão e proteção contra requisições indevidas conforme o mecanismo adotado. A existência da área ou de uma tela pública não fornece esses mecanismos nem autoriza automaticamente suas operações. Marca, conteúdo, menus, telas e operações são descritos junto das telas e dos fluxos correspondentes. Os endereços seguem [Caminhos HTTP](#caminhos-http).

### Comando e agendamento

| Campo | Informação |
| --- | --- |
| Invocação | Executável, arquivo de entrada e identificação do comando |
| Contexto | Diretório de execução, usuário e ambiente |
| Entrada | Argumentos, opções, formatos e valores padrão |
| Pré-requisitos | Configuração, permissões e serviços necessários |
| Efeitos | Dados lidos, alterados, criados ou removidos |
| Saída | Mensagens, resultado estruturado quando houver e códigos de término |
| Falhas | Condições de erro, interrupção e recuperação |
| Repetição | Efeito de repetir ou executar simultaneamente |
| Conferência | Como verificar o resultado |

O detalhamento descreve o arquivo de entrada, as invocações reais, os comandos disponíveis e sua forma de descoberta, sem presumir descoberta automática. O significado dos códigos de saída é explícito, incluindo sucesso, entrada inválida e falha de execução.

O agendamento informa frequência, fuso, usuário de execução, caminhos e destino das saídas. A configuração do agendador utiliza a mesma entrada e os mesmos contratos do uso manual. Operações destrutivas identificam seu alcance e recuperação. Os cuidados com credenciais seguem [Desenvolvimento seguro](00Regras.md#desenvolvimento-seguro).

### Ambiente de teste

O procedimento de cada conjunto identifica:

- Versões, extensões, serviços e permissões necessários;
- Endereços e parâmetros do ambiente de teste;
- Preparação de banco e arquivos isolados;
- Inicialização dos processos necessários;
- Execução dos cenários e coleta de resultados;
- Encerramento e limpeza dos recursos criados.

Cada utilitário de apoio informa entrada, recursos criados, saída e limpeza. É PERMITIDO organizar os cenários por área, divisão ou subpasta de assunto e agrupá-los por integração. Comandos de suítes específicas e receitas reproduzíveis dos ambientes efetivamente utilizados entram nos detalhamentos correspondentes. Os cuidados de execução seguem [Categorias e isolamento dos cenários](00Regras.md#categorias-e-isolamento-dos-cenários); a configuração do executor fica no documento da ferramenta de testes indicado em [Funções exigidas](04Ferramentas.md#funções-exigidas).

### Ficha de recurso de terceiros

A ficha de um recurso de terceiros contém, no mínimo:

| Campo | Conteúdo |
| --- | --- |
| Nome | Nome oficial do recurso |
| Versão | Versão ou identificador imutável da distribuição |
| Origem | Endereço e referência da distribuição utilizada |
| Licença | Identificação, caminho dos termos distribuídos e resultado da conferência conforme [Licenças](00Regras.md#licenças) |
| Justificativa | Motivo da adoção, conforme [Recursos de terceiros](00Regras.md#recursos-de-terceiros) |
| Finalidade | Função atendida no projeto |
| Integração | Componente que utiliza o recurso e forma de carregamento, quando houver |
| Requisitos | Versão de execução, extensões e serviços exigidos |
| Inclusão | Data de entrada no projeto |
| Alterações locais | Nenhuma, ou descrição dos arquivos modificados |
| Verificação | Procedimento usado para conferir a procedência, a integridade e a integração |

A ficha descreve a instalação efetiva e não substitui o texto da licença distribuída. É PERMITIDO acrescentar seções com peculiaridades do recurso, como caminhos instalados, obrigações da licença e cuidados de atualização.

### Documento de ferramenta

O documento de uso de cada ferramenta tem, no mínimo, as seções `Objetivo`, `Funcionamento`, `Instalação e configuração`, `Registro de instalação`, `Execução` e `Resultado`.

A seção `Registro de instalação` contém, no mínimo:

| Campo | Conteúdo |
| --- | --- |
| Versão | Versão instalada e forma de conferência |
| Origem | Endereço e referência da distribuição utilizada |
| Licença | Identificação, termos e resultado da conferência conforme [Licenças](00Regras.md#licenças) |
| Justificativa | Motivo da adoção, conforme [Recursos de terceiros](00Regras.md#recursos-de-terceiros) |
| Requisitos | Versão de execução, extensões e serviços exigidos |
| Executável | Caminho do executável |
| Configuração | Caminho da configuração utilizada |
| Integridade | Resumo esperado e sua fonte confiável |

A linha `Integridade` usa a fórmula abaixo, lida pela automação:

```text
| Integridade | SHA-256: `[RESUMO]`; fonte: https://[ENDERECO] |
```

`[RESUMO]` é substituído pelos 64 dígitos hexadecimais minúsculos do valor esperado; `[ENDERECO]`, pela fonte confiável que o comprova, como um resumo publicado ou uma assinatura oficial efetivamente validada contra o arquivo instalado. A fonte DEVE corresponder à versão instalada. Um resumo calculado apenas sobre o arquivo local ou sobre uma cópia baixada acompanha alterações locais, sem comprovar a procedência. Informações ainda não conferidas são registradas como `Pendente`, conforme [Manutenção dos documentos](00Regras.md#manutenção-dos-documentos).

### Documento de ferramenta de ambiente

O documento de cada ferramenta de ambiente tem, no mínimo, as seções `Objetivo`, `Pastas e arquivos no projeto` e `Escrita`. As regras de uso próprias da ferramenta ficam em seções seguintes.

A seção `Pastas e arquivos no projeto` identifica tudo o que a ferramenta acrescenta ao projeto em uma tabela com as colunas `Caminho`, `Situação` e `Finalidade`. A coluna `Situação` distingue diretórios e arquivos existentes, destinos permitidos ainda ausentes e conteúdo criado localmente sem versionamento. A seção `Escrita` define os nomes, o formato e os comentários de cada arquivo, apontando para as regras gerais deste documento sem repeti-las.

## Comunicação de ampliações e adaptações

É PERMITIDO apresentar a proposta de ampliação ou adaptação em uma issue ou, com o conteúdo preparado, no pull request. É PERMITIDO fazer o registro depois da edição local. Antes da aceitação do merge, preencher os campos pertinentes abaixo com o conteúdo preparado, as evidências disponíveis e as pendências identificadas, conforme [Ampliações & Adaptações](00Regras.md#ampliações--adaptações). O título identifica o tipo da proposta e a regra ou o assunto afetado. O corpo apresenta os campos abaixo:

| Campo | Conteúdo |
| --- | --- |
| Tipo | Ampliação de regra geral ou adaptação para uma situação específica |
| Necessidade | Requisito obrigatório que as regras atuais não permitem atender |
| Definição afetada | Documento, seção e trecho da regra que impede atender ao requisito |
| Alternativas avaliadas | Soluções que respeitam a estrutura e as regras documentadas, com a evidência de que cada uma falha no requisito |
| Proposta | Nova definição ou forma de proceder, com o texto proposto para a documentação |
| Alcance | Situações abrangidas e documentos, links e componentes afetados |
| Efeitos e verificação | Impactos previstos e forma de conferir a mudança |

O registro distingue a proposta da definição vigente em `Principal`. Apresentar a definição e o alcance preparados, as pendências, o resultado da avaliação e a aceitação ou recusa pelo responsável pela integração. Conflitos entre adaptações são descritos com links para as definições envolvidas, seus alcances e a solução proposta. Se a preparação ficar incompleta, informar os trechos já alterados, as etapas concluídas, o impedimento e as correções necessárias. A vigência segue [Atualização e vigência](00Regras.md#atualização-e-vigência).

As condições para propor e aplicar a mudança estão em [Ampliações & Adaptações](00Regras.md#ampliações--adaptações). A documentação da adaptação adotada segue o [Formato de adaptação](#formato-de-adaptação).

## Formato de adaptação

Os documentos de `Documentacao/Adaptacoes` seguem a [Estrutura dos documentos](#estrutura-dos-documentos). Depois de `## Objetivo`, cada campo abaixo constitui uma seção de nível 2 na fórmula `## [Campo]` (exemplos hipotéticos de escrita: `## Alcance` e `## Forma adotada`). O procedimento para registrar uma adaptação está em [Registro de adaptação](00Regras.md#registro-de-adaptação).

| Campo | Conteúdo |
| --- | --- |
| Definição afetada | Link para o padrão ou limite original |
| Alcance | Caminhos, componentes e situações abrangidos |
| Forma adotada | Como proceder nessa situação |
| Efeitos | Impactos em compatibilidade, segurança, dados, testes e manutenção |
| Verificação | Como conferir a adaptação |
| Condição de revisão | Quando houver, mudança na regra, no componente ou no uso que exija reavaliar a adaptação; omitir a seção quando não houver condição específica |

A vigência começa com a incorporação a `Principal`, conforme [Atualização e vigência](00Regras.md#atualização-e-vigência). A identificação desse merge e das alterações ou remoções posteriores fica nos pull requests e no histórico do Git; o documento da adaptação não registra essas datas. Uma condição de revisão não define prazo de expiração.

O documento da adaptação DEVE conter tudo o que é necessário para entender a diferença e aplicá-la: a regra afetada, as situações abrangidas e a forma de proceder. É PROIBIDO registrar uma justificativa genérica sem delimitação: o alcance DEVE indicar exatamente quais situações seguem a forma diferente.
