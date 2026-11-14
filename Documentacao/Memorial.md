# Memorial

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | ---------| -------------- |
| Allan Max | 11/11/2026      | Revisado | 12/11/2026     |

## Objetivo

Este documento registra o estado atual e as pendências de implementação e configuração do projeto, conforme [Memorial do projeto](00Regras.md#memorial-do-projeto).

## Estado atual

A aplicação ainda não está implementada. Não há funcionalidades, banco de dados, conexão, scripts de esquema ou de carga, nem testes da aplicação.

Os arquivos abaixo existem e estão vazios:

| Arquivo |
| --- |
| `Publico/index.php` |
| `Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php` |
| `Configuracoes/Aplicacao/Exemplo.env` |
| `Configuracoes/Docker/Exemplo.env` |
| `Configuracoes/Docker/compose.yaml` |
| `Publico/Ativos/CSS/Principal.css` |
| `Publico/Ativos/JavaScript/Principal.js` |

As áreas de `Aplicacao`, exceto `Nucleo`, e suas pastas correspondentes de `Documentacao/Aplicacao` contêm somente seus READMEs. Os destinos de esquemas, cargas, adaptações, testes unitários e documentação dos ativos próprios também contêm somente READMEs. Os destinos e suas responsabilidades estão em [Mapa geral](02Estrutura.md#mapa-geral).

As cópias de PHPStan, PHPCS, PHPCBF e PHPUnit estão presentes. Suas versões, requisitos e comandos estão nos documentos de `Documentacao/Ferramentas`. As configurações `Ferramentas/PHPStan/PHPStan.neon` e `Ferramentas/PHPCS/PHPCS.xml` ainda não existem.

PHPMailer, Font Awesome e as fontes JetBrains Mono, Manrope e Orbitron estão presentes, sem integração com a aplicação. Sua identificação e seus arquivos estão nas fichas de `Documentacao/Externos` e `Documentacao/Publico/Externos`. `Publico/Ativos/Imagens/Desenvolvedor` contém logotipos da empresa desenvolvedora.

`Ferramentas/PHPUnit/PHPUnit.xml` e `Testes/Apoio/Preparacao.php` preparam a execução dos testes PHP existentes. Os cenários de `Testes/Integracao/Automacao` verificam os apoios da automação e do Git, conforme [Testes de integração da automação](Testes/Integracao/TestesIntegracaoAutomacao.md).

### Configuração do fluxo

O workflow `.github/workflows/Validacao.yml` está configurado somente para pull requests destinados a `Validacao`. O apoio `.github/Verificacoes/Alteracao.php` reconhece a fórmula `Desenvolvimento_[Responsavel]` e não reconhece `Desenvolvimento` como branch de desenvolvimento.

`Validacao` e `Principal` têm proteção que exige pull request, inclusive para administradores. Somente `Validacao` exige o sucesso de `conferirResultado`; `Principal` não exige uma nova execução de testes. A configuração permite trabalho individual, sem exigir uma segunda pessoa para aprovar.

`.github/workflows/Reprovacao.yml` e `.github/Verificacoes/Reprovacao.php` registram a issue antes de encerrar o pull request reprovado. O workflow de registro usa o código da branch padrão e não executa os testes da entrega com permissões de escrita. Os cenários PHP substituem a API do GitHub por dados fictícios.

### Limites da automação

A automação não coleta nem exige cobertura integral, não contém verificações específicas dos ativos e scripts de banco e não configura o bloqueio dos originais e metadados de ativos no Apache. Não há ambiente da aplicação configurado; os arquivos de Docker estão vazios e `Configuracoes/Docker/Construcao` contém somente seu README.

Pendente: preparar a aplicação, as configurações de verificação, a medição de cobertura e a entrega web. Os testes da automação não comprovam execução, cobertura ou validação da aplicação.
