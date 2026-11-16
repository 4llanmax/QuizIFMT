# Testes do jogo

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Identificar contratos, isolamento, comandos e evidências dos cenários do jogo. O comportamento atendido está em [Jogo](../../../Aplicacao/Site/Jogo.md).

## Ambiente e isolamento

Usar PHP 8.5.12, MariaDB 10.6.28 e as extensões de execução da aplicação, além das exigidas pelo [PHPUnit](../../../Ferramentas/PHPUnit.md). Linhas e ramos PHP exigem Xdebug em modo `coverage`. O relatório de ramos da versão instalada requer também `pathCoverage=true`; o percentual de combinações de caminhos não representa o percentual exigido de ramos de decisão.

`BancoIsolado` cria um nome aleatório próprio, aplica esquema, carga e as migrações de categorias e OAuth e remove somente esse banco ao final da classe. Não recebe nome de banco de produção. O executor precisa de autorização para criar e remover bancos nesse serviço exclusivo de teste. Uma falha de preparação ou limpeza permanece visível.

| Variável exclusiva dos testes | Tipo e padrão | Uso |
| --- | --- | --- |
| `TESTE_BANCO_HOST` | Texto; `127.0.0.1` | Endereço do serviço de testes |
| `TESTE_BANCO_PORTA` | Porta; `3306` | Conexão dos cenários |
| `TESTE_BANCO_USUARIO` | Texto; `root` | Identidade do serviço local de testes |
| `TESTE_BANCO_SENHA` | Texto; vazio | Segredo somente do serviço isolado |

Os padrões correspondem ao Wamp de desenvolvimento. Em outro ambiente, definir as variáveis do processo com identidade exclusiva de teste; não usar credenciais de produção. As variáveis não são parâmetros reconhecidos da aplicação.

Sessões nativas usam pastas temporárias próprias, removidas após os cenários. Estado de formulário e requisição é preparado pelo teste. Os testes de lógica usam contratos substituídos, sem banco. Integração usa o MariaDB real. A conferência local no Apache usa processo e configuração próprios, sem alterar a instalação ativa do Wamp.

## Inventário de contratos e cenários

| Contrato | Cenários verificados | Responsável pelos testes |
| --- | --- | --- |
| Configuração | Arquivo ausente, aspas, comentários, precedência, parâmetros inválidos, descrições adicionais | `ParametrosTeste` |
| Autocarregamento e seleção | Prefixos, nomes desconhecidos, área ausente, caminhos e ativos autorizados | `EncaminhamentoTeste`, `EntregaTeste` |
| Pontuação e prazo | Acerto, erro, vitória, abandono, prazo exato, tempo de confirmação, repetição e índice incorreto | `PartidaTeste`, `BancoJogoTeste` |
| Sorteio e persistência | Distribuição, quantidade, ausência de repetição, acervo insuficiente, gravação idempotente | `ServicosTeste`, `BancoJogoTeste` |
| Contas | Cadastro, entrada correta e incorreta, apelido reservado, renomeação, vínculo do convidado, ausência de transferência entre contas | `EntradaTeste`, `ServicosTeste`, `BancoJogoTeste` |
| Acesso | CSRF, autenticação, limite compartilhado, renovação de sessão, saída, conta excluída em outra sessão, falha de sessão nativa | `BancoJogoTeste`, `ContagemTeste` |
| Ranking e histórico | Recorde independente do Top 100, ordenação da consulta, histórico e exclusão em cascata | `ServicosTeste`, `BancoJogoTeste`, `VisoesTeste` |
| Administração | Senha e autorização, limites, soma, capacidade do acervo, restauração sem apagar resultados | `BancoJogoTeste`, `VisoesTeste` |
| Apresentação | Todas as páginas, conta e convidado, ranking vazio e preenchido, histórico vazio e preenchido, escape e HTML minificado | `VisoesTeste` |
| Ativos | Manifesto, tipo MIME declarado, ícone, fontes locais e correspondência dos arquivos processados | `AtivosTeste` |
| Medição | Aprovação integral, recusa de linha ou ramo ausente, relatório volumoso e LF/CRLF | `CoberturaTeste` |
| Navegador substituído | Cronômetro, progressão sincronizada da corda e da faísca, retomada parcial, expiração, teclas, envio repetido, preferências, som, instalação, cópia e falhas das dependências | `PrincipalTeste.mjs` |
| Automação | Suíte própria, serviço MariaDB, etapas de cobertura e ativos, gatilho preservado | `Automacao/JogoTeste` e cenários anteriores da automação |
| Gestão MCP | Perguntas em lote, edição parcial, arquivamento, exclusão confirmada, categorias, paginação e consultas sem credenciais | `GestaoMCPTeste`, `PerguntaTeste` |
| Equilíbrio das categorias | Total exato, diferença máxima de uma pergunta, categorias vazias, capacidade insuficiente e sorteio dos excedentes | `DistribuicaoTeste`, `BancoJogoTeste`, `CategoriasVisoesTeste` |
| OAuth | Registro público, código de uso único, PKCE, vínculo de cliente e recurso, expiração, renovação, reuso, revogação, consentimento e senha inicial aceita sem alteração | `OAuthTeste`, `IntegracaoMCPTeste` |
| Transporte MCP | JSON-RPC, negociação, autenticação obrigatória, formatos, limites, notificações sem efeitos e mensagens de erro | `MCPTeste` |
| Integração HTTP | Contexto e corpo preservados, CSRF dos formulários, descoberta pela entrada única e configuração canônica sem confiar em Host | `EncaminhamentoHTTPTeste`, `EntradaMCPTeste`, `IntegracaoMCPTeste` |
| Usuários MCP | Permissões administrativas, confirmação de desativação, criação com senha inicial, ausência de troca pública e manutenção privada da senha | `FerramentasUsuariosTeste`, `OAuthTeste`, `IntegracaoMCPTeste` |

## Execução PHP

Executar a suíte completa da raiz com Xdebug habilitado para cobertura:

```text
php Ferramentas/PHPUnit/PHPUnit.phar --configuration Ferramentas/PHPUnit/PHPUnit.xml
php Ferramentas/PHPUnit/ConferenciaCobertura.php
php Ferramentas/Ativos/Ativos.php --conferir
```

Em PowerShell, definir `$env:XDEBUG_MODE='coverage'` no processo antes da suíte. Se o comando `php` selecionar outra versão do Wamp, usar explicitamente o executável da versão 8.5.12. O limite de memória da configuração é 1 GiB para a coleta de caminhos do Xdebug; esse valor é somente de desenvolvimento.

As suítes são `Automacao`, `Integracao` e `Unitario`. A suíte de automação também pode ser executada isoladamente com `--testsuite Automacao --no-coverage`, sem preparar banco da aplicação. A aprovação completa exige todas as suítes e cobertura, sem ignorados, avisos ou falhas.

## Medição PHP

O filtro inclui toda a árvore `Aplicacao` e `Publico/index.php`, inclusive arquivos não exercitados. Os relatórios ficam em `Dados/Arquivos/Cache/CoberturaPHP`: `Resumo.txt`, `Clover.xml` e `Cobertura.php`.

O conferidor lê a serialização do PHPUnit sem instanciar classes. Confere linhas e blocos de ramos das classes, funções, closures, visões e entrada. Um bloco principal único de declaração não contém decisão selecionável; suas linhas executáveis continuam medidas. Não há exclusão de código próprio de produção. Ausência de métrica ou arquivo de relatório impede aprovação.

O código dos testes, apoios de desenvolvimento, ferramentas e distribuições externas não integra o percentual de produção. SQL inicial contém declarações e dados, sem procedimentos ou regras executáveis; sua conferência é a importação real e a consulta dos registros no MariaDB. CSS, SVG e manifesto recebem verificação declarativa. JavaScript recebe medição própria.

## JavaScript e prévia

O apoio usa [Node.js](../../../Ambiente/NodeJS.md) e módulos nativos, somente no desenvolvimento:

```text
node Testes/Apoio/ConferenciaJavaScript.mjs
```

Os mesmos cenários verificam o original e o minificado. DOM, relógio, áudio, armazenamento, instalação e área de transferência são controlados pelos cenários. A coleta V8 usa cobertura detalhada, contextos preservados até a leitura e identificação nova por cenário para evitar reaproveitamento de compilação sem instrumentação de ramos. Intervalos não exercitados reprovam o apoio. O relatório fica em `Dados/Arquivos/Cache/CoberturaJavaScript/Relatorio.json`.

Para prévia local, preparar a configuração privada e uma pasta privada de sessões com permissão de escrita, e usar o servidor embutido com `Testes/Apoio/Servidor.php` como roteador e `Publico` como raiz. A extensão e a diretiva `session.save_path` são do ambiente PHP; a pasta da sessão não fica pública.

## Evidência e limites

A última execução e as conferências HTTP ficam no [Memorial](../../../Memorial.md#verificações-da-implementação-do-jogo). Os relatórios são gerados e não versionados.

Testes com dependências do navegador substituídas não comprovam desenho visual, áudio físico, instalação no dispositivo ou permissões de cópia. A conferência local não comprova a conta da hospedagem nem a execução do workflow no GitHub. A validação nesses ambientes permanece separada, conforme [Hospedagem compartilhada](../../../Implantacao/Hospedagem.md).
