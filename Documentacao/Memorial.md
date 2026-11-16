# Memorial

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 11/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Registrar estado atual, pendências e evidências da implementação. As definições permanecem em [Regras](00Regras.md#memorial-do-projeto) e nos documentos responsáveis.

## Estado atual

O jogo BombQUIZ está implementado nas áreas Site e Painel, com inicialização técnica no Núcleo, sessão nativa PHP e persistência no MariaDB. O conteúdo necessário do material fornecido está nos componentes da aplicação, nos ativos públicos e na carga SQL. A pasta `00ModeloJogo` está removida após a conferência da transferência e a autorização do responsável.

Site atende identificação de convidados, contas, rodada, respostas, expiração, resultado, ranking e histórico. Painel autentica a administração e altera a programação das novas rodadas. O [detalhamento do jogo](Aplicacao/Site/Jogo.md) e a [programação](Aplicacao/Painel/Programacao.md) apresentam os contratos.

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
