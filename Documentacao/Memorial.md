# Memorial

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 11/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Registrar estado atual, pendências e evidências da implementação. As definições permanecem em [Regras](00Regras.md#memorial-do-projeto) e nos documentos responsáveis.

## Estado atual

O jogo BombQUIZ está implementado nas áreas Site e Painel, com inicialização técnica no Núcleo, sessão nativa PHP e persistência no MariaDB. O conteúdo necessário do material fornecido está nos componentes da aplicação, nos ativos públicos e na carga SQL. A pasta `00ModeloJogo` está removida após a conferência da transferência e a autorização do responsável.

Site atende identificação de convidados, contas, rodada, respostas, expiração, resultado, ranking e histórico. Painel autentica a administração e altera a programação das novas rodadas. O [detalhamento do jogo](Aplicacao/Site/Jogo.md) e a [programação](Aplicacao/Painel/Programacao.md) apresentam os contratos.

A API implementa [MCP](Aplicacao/API/MCP.md) por HTTP, com autorização OAuth por página do Painel e gestão de usuários. A senha definida para a conta permite conexão e renovação de tokens sem troca obrigatória; o login não oferece alteração da senha, e o catálogo não publica ferramenta de troca. As 19 ferramentas administram perguntas e categorias e consultam jogadores, resultados, ranking e programação. Categorias com perguntas ativas participam da divisão equilibrada do total da rodada; os temas com perguntas adicionais são sorteados. Arquivamento e exclusão do acervo preservam partidas já iniciadas e resultados existentes. A habilitação depende dos parâmetros privados e das duas migrações descritas em [Atualização do MCP](Implantacao/Hospedagem.md#atualização-do-mcp).

Os scripts iniciais de esquema e carga estão nas pastas MariaDB de Dados. A carga contém 48 perguntas e a programação inicial, sem jogadores de exemplo. As pastas fixas de SQLite permanecem preservadas.

`Publico/index.php`, o autocarregamento, `Configuracoes/Aplicacao/Exemplo.env` e os ativos principais têm implementação. `Publico/.htaccess` autoriza somente as versões processadas, o ícone, o manifesto e as fontes usadas. Manrope, Orbitron e a fonte sólida do Font Awesome são integradas localmente. PHPMailer e JetBrains Mono permanecem sem uso na aplicação.

PHPStan, PHPCS, PHPCBF e PHPUnit têm configurações explícitas. O processador próprio de ativos e o conferidor de cobertura estão implementados. Os testes PHP e JavaScript e seus contratos estão em [Testes do jogo](Testes/Integracao/Jogo/TestesJogo.md).

`Configuracoes/Docker/Exemplo.env` e `Configuracoes/Docker/compose.yaml` permanecem vazios; a construção Docker local não é requisito da implantação. A aplicação não exige Composer, Node, Docker ou processo permanente próprio na hospedagem.

Uma configuração privada local é preparada em banco próprio deste trabalho, sem substituir instalação existente. Ela é ignorada pelo Git. O acesso administrativo permanece desabilitado até o operador configurar seu hash privado.

### Configuração do fluxo

O desenvolvimento utiliza `Desenvolvimento_AllanMax`; o envio desta implementação destina-se à branch de mesmo nome no remoto. A incorporação em `Validacao` permanece sujeita a pull request e à revisão do conjunto. Sua preparação inclui MariaDB 10.6.28, cobertura PHP e JavaScript e conferência dos ativos. A execução do workflow no GitHub não é declarada verificada localmente.

O gancho está ativo nesta cópia por `core.hooksPath=.githooks`, com autorização expressa do responsável em 16/11/2026. Os arquivos de `.githooks`, a variável de autorização dos documentos numerados e os documentos numerados permanecem inalterados.

### Limites da automação

Os cenários da automação usam executores controlados para conferir decisões e comandos. A instalação das imagens, a preparação Linux, o TLS e a execução do job completo no GitHub ainda dependem de uma execução real do fluxo. A disponibilidade da hospedagem compartilhada não é comprovada pelas verificações locais.

## Verificações da senha de acesso MCP

Conferência local de 16/11/2026 após a retirada da troca obrigatória:

- PHPUnit: 136 testes e 1204 asserções, sem falhas, avisos ou cenários ignorados. O conferidor integral confirma 1431/1431 linhas e 1355/1355 ramos de produção. PHPStan nível 5 e PHPCS passam.
- Três cenários novos reproduzem, antes da mudança, o bloqueio da senha inicial, o campo de nova senha e a alteração da senha durante o login. Depois da mudança, os testes confirmam autorização, emissão, renovação e aceitação de tokens com a senha inicial e preservação do hash, inclusive quando o cliente envia o campo antigo de troca.
- O catálogo contém 19 ferramentas e recusa a ferramenta de troca retirada sem alterar senha ou token. Usuários sem administração continuam impedidos de gerenciar identidades.
- As migrações e a estrutura do banco permanecem inalteradas. A manutenção de senha autorizada é realizada por operação privada transacional com revogação dos códigos e tokens anteriores; seus valores e os resultados individuais ficam somente nos logs privados ignorados pelo Git.
- A conferência HTTPS da versão hospedada confirma descoberta OAuth com HTTP 200 e desafio de autenticação no MCP com HTTP 401. Após a manutenção privada, o login com a senha definida e sem envio de nova senha produz HTTP 303 e a emissão de token retorna HTTP 200. O cliente criado exclusivamente para essa conferência é removido junto de seus códigos e tokens. A verificação usa TLS com certificados de confiança do Windows e não exibe credenciais.

A atualização da interface de login e do catálogo na hospedagem depende da publicação dos arquivos deste conjunto. A manutenção privada da senha não publica código nem modifica o `.env`.

## Verificações do MCP e do pavio

Conferência local de 16/11/2026, com PHP 8.5.12, Xdebug 3.5.3, MariaDB 10.6.28 e Node.js 22.23.5:

- PHPUnit: 133 testes e 1174 asserções, sem falhas, avisos ou cenários ignorados. O conferidor integral confirma 1444/1444 linhas e 1378/1378 ramos de produção, incluindo funções, closures, visões e entrada.
- PHPStan no nível 5 e PHPCS no padrão declarado passam. Os documentos numerados permanecem idênticos ao registro, e `git diff --check` passa.
- As ferramentas do MCP exercitam perguntas, categorias, consultas e usuários em bancos descartáveis. OAuth cobre código de uso único, PKCE, vínculo do recurso e do cliente, consentimento, troca da senha inicial, expiração, renovação e revogação por reuso.
- Apache 2.4.65 em processo isolado recebe 19 requisições e confere descoberta, registro, login, autorização, token, Bearer, catálogo com 20 ferramentas, consulta, revogação, origem recusada e bloqueio de arquivos privados e originais. O relatório fica em `Dados/Arquivos/Cache/ApacheMCP/Resultado.json`, sem credenciais nem tokens.
- A correção da [issue do pavio](https://github.com/4llanmax/QuizIFMT/issues/1) sincroniza a largura da corda e a posição da faísca com o tempo restante. O teste novo reproduz a posição ausente antes da correção. Original e minificado passam em 38 verificações e 64/64 intervalos V8 cada, incluindo início, meio, fim e retomada parcial da pergunta.
- O processador confirma correspondência entre originais e arquivos minificados. Depois da correção do pavio, os cenários PHP de ativos e visões também passam: quatro testes e 126 asserções.

As duas migrações MCP são aplicadas ao banco de testes autorizado com os registros existentes preservados. A identidade inicial e os parâmetros são preparados por operação privada; seus valores não integram o repositório. Os resumos dos scripts e os resultados dessa operação ficam nos logs privados ignorados pelo Git. É PROIBIDO reaplicar as migrações já concluídas nesse banco.

A execução HTTP local não comprova a implantação em `https://maxmelo.com/mcp`, a autorização de um cliente externo ou a aparência e a animação em navegador real. O operador envia os arquivos pelo fluxo do provedor e acrescenta os parâmetros privados ao `.env`, conforme [Atualização do MCP](Implantacao/Hospedagem.md#atualização-do-mcp). A disponibilidade pública permanece sujeita a essa implantação e à conferência no endereço real.

## Verificações da implementação do jogo

Conferência local de 16/11/2026, com PHP 8.5.12, Xdebug 3.5.3 e MariaDB 10.6.28:

- PHPUnit: 86 testes e 663 asserções, sem falhas, avisos ou cenários ignorados.
- Relatório de produção: 100% das linhas e dos ramos das classes; o conferidor ampliado confirma 756/756 linhas e 628/628 blocos de ramos, incluindo funções, visões e entrada.
- Os relatórios ficam em `Dados/Arquivos/Cache/CoberturaPHP` e não são versionados. O percentual de combinações de caminhos permanece separado do percentual de ramos de decisão.
- A análise PHPStan no nível 5 e a conferência PHPCS no padrão declarado passam. O processador confirma correspondência entre originais e arquivos minificados.
- Os cenários JavaScript exercitam original e minificado, com 52 verificações de comportamento e 63/63 intervalos V8 em cada versão, usando relógio e dependências controlados. A coleta detalhada V8 e seu resultado final ficam em `Dados/Arquivos/Cache/CoberturaJavaScript/Relatorio.json`.
- Apache 2.4.65 em processo isolado entrega páginas, CSS e JavaScript processados, ícone e fontes com sucesso; bloqueia originais, metadados e listagem. Rota inexistente e tentativa de acessar configuração privada retornam erro. O manifesto recebe declaração explícita de tipo de conteúdo.
- Os documentos numerados permanecem idênticos ao registro de conferência.

A conferência HTTP registra 24 respostas em duas entradas locais, com seus tipos, cookie e bloqueios, em `Dados/Arquivos/Cache/ConferenciaHTTP.json`. A correspondência PSR-4 compara a grafia de 29 declarações com os caminhos físicos e não encontra divergências.

O controle do navegador não se conecta ao ambiente local; não há conferência visual de tela, áudio físico, instalação no dispositivo ou área de transferência real. O destino de produção, suas permissões e seus procedimentos administrativos permanecem sujeitos à [conferência no provedor](Implantacao/Hospedagem.md#conferência-no-provedor).

## Histórico

### Implementação do jogo em 16/11/2026

A preparação parte de aplicação sem funcionalidades. A suíte anterior passa com 42 testes e 271 asserções antes da implementação. Os cenários novos são escritos antes dos componentes correspondentes e registram a ausência esperada do comportamento. A implementação separa persistência, regras, validação, controles, rotas e visões e conserva as definições dos documentos numerados.

A persistência substitui o armazenamento demonstrativo no navegador. Respostas e tempo passam à conferência do servidor. A administração exige senha privada. As fontes e os ícones usados não dependem de CDN. A instalação por arquivos e SQL está em [Hospedagem compartilhada](Implantacao/Hospedagem.md).

### Retirada do material de referência em 16/11/2026

A conferência compara as 48 perguntas do material fornecido com a carga SQL, incluindo temas, alternativas e índices corretos, e confirma correspondência integral. O código da aplicação e os testes não dependem da pasta de referência. O layout, o ícone e o manifesto estão nos destinos próprios; o armazenamento demonstrativo e o cache offline não integram a aplicação. A pasta `00ModeloJogo` está removida por solicitação do responsável.
