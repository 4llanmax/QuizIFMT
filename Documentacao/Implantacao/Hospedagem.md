# Hospedagem compartilhada

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Descrever a instalação do jogo por envio de arquivos e importação SQL, com versões controladas pelo provedor. Os parâmetros reconhecidos estão em [Inicialização](../Aplicacao/Nucleo/Inicializacao.md).

## Condições de execução

A aplicação segue o mínimo de PHP 8.5.12 definido em [Configurações](../03Configuracoes.md#requisitos-de-execução) e usa MariaDB 10.6.28, correspondente ao Wamp local. Extensões de execução: PDO, `pdo_mysql`, `mbstring`, `intl`, `filter`, `session`, `json` e suporte a senhas e aleatoriedade do PHP. As extensões de testes e análise não são requisitos da hospedagem.

A implantação não exige Composer, Node, Docker, acesso SSH, cron ou processos permanentes. Ferramentas de desenvolvimento processam os ativos antes do envio; os arquivos processados são versionados.

A administração DEVE conferir as versões e extensões no painel do provedor. Caso a conta não forneça os requisitos, a compatibilidade DEVE ser resolvida antes da implantação; o envio dos arquivos não altera as versões travadas.

## Disposição dos arquivos

A pasta pública da conta recebe o conteúdo de `Publico`, incluindo `.htaccess` e o manifesto. `Aplicacao`, `Configuracoes` e `Dados` ocupam a área privada da conta, como pastas irmãs da pasta pública. A entrada resolve a aplicação pela pasta imediatamente acima dela.

A fórmula `[PastaPublica]` designa a pasta fixada pelo provedor (exemplo hipotético de escrita: `public_html`). Esse nome externo não exige renomear os componentes internos nem cria uma nova pasta no repositório.

É PROIBIDO enviar a raiz inteira do repositório para exposição pública. `.git`, documentação, ferramentas, testes e parâmetros privados permanecem fora da entrega web. Se o provedor impedir a disposição privada exigida, essa condição DEVE ser resolvida sem expor o código interno nem contornar a [Entrega web](../03Configuracoes.md#entrega-web).

## Preparação e envio

1. Conferir a [verificação local](../Testes/Integracao/Jogo/TestesJogo.md) e gerar os ativos com `php Ferramentas/Ativos/Ativos.php`.
2. Criar banco vazio e identidade de conexão no painel do provedor, sem substituir banco existente.
3. Importar `Dados/Esquemas/MariaDB/I.2026.11.15.0001.sql` e depois `Dados/Cargas/MariaDB/I.2026.11.15.0001.sql`, pelo gerenciador disponibilizado na conta.
4. Copiar `Configuracoes/Aplicacao/Exemplo.env` para a configuração privada `.env` somente se ela não existir. Preencher host, porta, banco, usuário e senha reais nessa cópia não versionada.
5. Conferir `URL_PREFIXO`: `/` para a raiz do endereço; em instalação sob outro caminho, preencher o caminho iniciado e terminado por barra.
6. Para habilitar a administração, gerar localmente um hash PHP e preencher `ADMIN_SENHA_HASH`, conforme [Programação](../Aplicacao/Painel/Programacao.md#parâmetro-administrativo). Sem o valor, a entrada administrativa permanece desabilitada.
7. Enviar pastas privadas e conteúdo público preservando maiúsculas, minúsculas e arquivos ocultos.
8. Conferir sessões, HTTPS, encaminhamento, tipos e bloqueios no endereço real antes de abrir o jogo ao público.

Os scripts iniciais não são executados automaticamente pela aplicação e não devem ser repetidos em instalações em uso. Não há instalação administrativa pública nem credencial padrão para o Painel.

## Conferência no provedor

Verificar início, identificação de convidado, resposta, expiração, resultado, ranking, cadastro, entrada, histórico, alteração de apelido, saída e administração. Usar dados fictícios em ambiente de validação separado da produção.

Conferir por HTTP uma rota inexistente, um ativo processado, o manifesto, uma fonte WOFF2 e os bloqueios de originais, metadados, pastas e configuração privada. Inspecionar cookie seguro e cabeçalhos dinâmicos. Conferir que recarregar a pergunta não reinicia o prazo e que o cliente não recebe gabaritos futuros.

A verificação local não comprova permissões, TLS, versões, encaminhamento ou operação da conta do provedor. O responsável pela implantação DEVE registrar o resultado desse ambiente no fluxo de validação do projeto.

## Operação

A administração do ambiente DEVE identificar armazenamento, acesso e retenção dos logs PHP e das sessões, permissões de leitura dos parâmetros e procedimento de backup e restauração. Falhas técnicas são registradas sem valores de conexão ou dados do formulário.

A abertura pública também exige definir os procedimentos de tratamento descritos em [Dados e responsabilidades](../Aplicacao/Site/Jogo.md#dados-e-responsabilidades). O código implementa consulta, correção do apelido e exclusão da conta; não define responsáveis administrativos ou políticas do provedor.
