# Configurações

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 11/11/2026     |

## Objetivo

Este documento define a organização dos parâmetros de execução e os requisitos de configuração do projeto. Os contratos e parâmetros concretos pertencem à documentação do componente que os utiliza; os procedimentos do ambiente ficam no detalhamento correspondente, conforme [Outros documentos](02Estrutura.md#outros-documentos).

## Em resumo

- O requisito mínimo definido para a aplicação é PHP 8.5.12. Veja [Requisitos de execução](#requisitos-de-execução).
- O modelo de parâmetros DEVE excluir segredos e identificar somente chaves reconhecidas pela implementação. Veja [Parâmetros da aplicação](#parâmetros-da-aplicação).
- Um ambiente é preparado sem substituir uma instalação existente. Veja [Preparação de um ambiente](#preparação-de-um-ambiente).
- O ambiente de validação DEVE reproduzir as condições de produção relevantes aos testes. Veja [Ambiente de validação](#ambiente-de-validação).
- O servidor DEVE entregar exclusivamente a partir de `Publico`, com conferência no ambiente configurado. Veja [Entrega web](#entrega-web).
- Banco, cache, logs e uploads têm destinos e permissões próprios. Veja [Banco e armazenamento](#banco-e-armazenamento).

## Requisitos de execução

A execução da aplicação DEVE usar PHP 8.5.12 ou superior. Esse mínimo é uma definição de compatibilidade. Cada componente DEVE identificar suas extensões e serviços necessários. Cada ferramenta DEVE registrar seus próprios requisitos em seu documento de uso; eles não comprovam requisitos adicionais da aplicação.

## Organização

Os destinos dos arquivos de configuração estão em [Configuracoes](02Estrutura.md#configuracoes); os das ferramentas, em [Ferramentas e Externos](02Estrutura.md#ferramentas-e-externos). Configurações acrescentadas por ferramentas de ambiente, como contêineres e gerenciadores de banco, seguem [Ferramentas de ambiente](02Estrutura.md#ferramentas-de-ambiente).

Este documento concentra as definições gerais de configuração. Detalhamentos apresentam somente os contratos e procedimentos específicos, conforme [Onde documentar cada assunto](00Regras.md#onde-documentar-cada-assunto).

As seções seguintes definem requisitos obrigatórios de configuração e conferência, sem declarar um ambiente implantado. A situação física dos arquivos está em [Estado atual](Memorial.md#estado-atual).

## Parâmetros da aplicação

O arquivo fixo `Configuracoes/Aplicacao/Exemplo.env` é o modelo versionado dos parâmetros. Seu conteúdo DEVE descrever exclusivamente chaves reconhecidas pela implementação, sem segredos reais. Este documento não define chaves nem precedência de carregamento. É PERMITIDO armazenar a configuração privada em `Configuracoes/Aplicacao/.env`; esse arquivo nunca é versionado.

O formato de arquivo, sua gramática e a precedência entre arquivo, variáveis do processo e valores padrão DEVEM pertencer ao contrato do carregador. O modelo DEVE usar os nomes externos de variáveis de ambiente; a representação interna segue [Formato por elemento](01Padroes.md#formato-por-elemento). Um arquivo chamado `.env` não é carregado automaticamente apenas por existir. O carregamento da configuração é documentado no detalhamento do componente responsável.

Cada parâmetro adotado recebe esta descrição na documentação de seu consumidor:

| Campo | Informação |
| --- | --- |
| Nome | Chave exata lida pela implementação |
| Finalidade | Comportamento configurado |
| Tipo e valores | Texto, número, booleano, lista ou outro formato aceito |
| Presença | `OBRIGATÓRIO` ou `OPCIONAL`, com a condição de uso |
| Padrão | Valor utilizado na ausência, ou indicação de que não há padrão |
| Validação | Valores recusados e tratamento da falha |
| Origem | Arquivo, variável do processo ou outro mecanismo |
| Sigilo | Público, interno ou secreto |
| Exemplo | Valor fictício e instruções de substituição |

Endereços, credenciais, caminhos de armazenamento e limites de integração pertencem à configuração. Os parâmetros de todas as áreas, do Núcleo e das integrações RESIDEM EXCLUSIVAMENTE EM `Configuracoes/Aplicacao`; arquivos de integração em formato próprio seguem [Configuracoes](02Estrutura.md#configuracoes). A lógica que usa esses parâmetros permanece em `Aplicacao`, na área responsável, conforme [Nucleo](02Estrutura.md#nucleo).

A classificação `Público` de um parâmetro indica ausência de sigilo no valor documentado; ela não autoriza entregar arquivos de configuração pelo servidor web. A localização privada e as restrições de [Entrega web](#entrega-web) permanecem obrigatórias para todas as classificações.

### Preparação de um ambiente

1. Identificar os componentes habilitados e seus requisitos documentados.
2. Conferir se o modelo descreve as chaves reconhecidas pelo carregador.
3. Criar a configuração privada sem substituir uma instalação existente.
4. Preencher valores do ambiente e disponibilizar segredos pelo mecanismo adotado.
5. Preparar diretórios de armazenamento e permissões do processo.
6. Executar a inicialização e verificar parâmetros ausentes, inválidos e válidos.

Caminhos relativos DEVEM ter um diretório de referência explícito e igual para HTTP, terminal e agendador. Caminhos absolutos são valores locais, não caminhos fixados nos documentos gerais.

### Referências da operação

O detalhamento técnico de um ambiente adotado DEVE identificar os procedimentos operacionais mantidos por sua administração, quando existentes. É PERMITIDO referenciar documentação de infraestrutura, um repositório de operação ou uma fonte de acesso restrito. Identificar o ambiente e a fonte correspondente sem versionar credenciais ou informações protegidas. É PROIBIDO apresentar procedimentos sem ambiente identificado como configuração efetivamente implantada.

Os requisitos técnicos do projeto, incluindo compatibilidade e recuperação das atualizações de banco, ficam na documentação do componente e seguem [Cuidados de manutenção](00Regras.md#cuidados-de-manutenção). Os comandos e as decisões concretas de implantação, contenção e recuperação pertencem à documentação operacional do ambiente referenciado.

### Ambiente de validação

O ambiente destinado aos testes reais de `Validacao` DEVE cumprir [Branches do projeto](Ambiente/GitHub.md#branches-do-projeto). Sua configuração DEVE identificar versões de PHP, banco, servidor e serviços, parâmetros de execução e estrutura de dados correspondentes à produção.

O acesso ao ambiente de teste é PERMITIDO somente aos perfis autorizados. Operações administrativas, como alterar configurações, revogar credenciais ou administrar logs, DEVEM seguir as permissões do serviço correspondente. A permissão para testar e as permissões administrativas DEVEM estar identificadas na configuração dos acessos.

Replicar as condições de produção que afetam os fluxos testados, mantendo endereços, credenciais e dados próprios do ambiente de teste. Registrar as diferenças entre os ambientes e conferir seus efeitos nos cenários de validação. Dados de teste e armazenamento permanecem isolados da produção.

## Entrega web

O servidor DEVE entregar arquivos exclusivamente a partir de `Publico`, encaminhar operações dinâmicas para `Publico/index.php` e entregar somente os ativos autorizados conforme [Publico](02Estrutura.md#publico).

A configuração concreta DEVE identificar servidor, encaminhamento, endereço público, proteção TLS aplicável e tratamento de procuradores reversos, cumprindo [Desenvolvimento seguro](00Regras.md#desenvolvimento-seguro). Um procurador reverso recebe a requisição antes do servidor da aplicação; os cabeçalhos confiáveis dependem dessa topologia.

A configuração de entrega aplica aos caminhos e bloqueios do servidor as condições definidas em [Arquivos entregues ao usuário](00Regras.md#arquivos-entregues-ao-usuário).

O detalhamento do ambiente DEVE identificar os caminhos autorizados para ativos processados e os bloqueios dos originais e metadados preservados no disco. A conferência DEVE examinar o conteúdo recebido: ativos correspondem aos arquivos estáticos processados e HTML dinâmico é minificado na composição da resposta, conforme [Arquivos entregues ao usuário](00Regras.md#arquivos-entregues-ao-usuário). Habilitar mapas de origem exige cumprir os limites de [Ativos](02Estrutura.md#ativos); a extensão do arquivo ou o ambiente de desenvolvimento não dispensam esses limites.

### Conferência da entrega

No ambiente configurado, verificar:

1. Uma rota implementada chega à área e ao controle corretos.
2. Uma rota inexistente produz a resposta de erro prevista.
3. Um ativo autorizado é entregue com tipo de conteúdo correto.
4. Configuração privada, código interno e dados não são acessíveis pelo navegador.
5. Listagem de diretórios, execução de código em Ativos e entrega de metadados não públicos estão bloqueadas.
6. Encaminhamento, endereço e conexão segura correspondem ao ambiente descrito.
7. Os arquivos entregues e os bloqueios configurados atendem a [Arquivos entregues ao usuário](00Regras.md#arquivos-entregues-ao-usuário).
8. Os endereços dos originais preservados não entregam conteúdo fonte sem processamento; mapas habilitados não expõem esses arquivos fonte, comentários internos ou dados protegidos.

Estas verificações dependem de servidor e aplicação preparados.

## Banco e armazenamento

A configuração do banco DEVE identificar o sistema adotado, o driver, o endereço, as credenciais quando exigidas e as opções de conexão. Banco em arquivo DEVE residir em `Dados/Arquivos/BancoDados`; armazenamento externo é PERMITIDO somente com destino explicitamente configurado.

Cache, logs e uploads têm destinos e permissões próprios. O processo recebe escrita apenas onde sua operação exige. A preparação dos testes utiliza armazenamentos isolados, conforme [Categorias e isolamento dos cenários](00Regras.md#categorias-e-isolamento-dos-cenários).

Os logs técnicos de execução, incluindo aplicação de scripts de banco e incidentes, ficam em `Dados/Arquivos/Logs` ou em um armazenamento externo explicitamente configurado. Identificar na configuração do ambiente o destino, os perfis autorizados a consultar e administrar os logs e os prazos de conservação. Logs de incidentes e evidências que contenham informações sensíveis têm acesso restrito e ficam fora do versionamento e da entrega pública. Erros de código são acompanhados também por issues no repositório remoto, conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências), sem reproduzir conteúdo sensível nessas issues.

Versionamento, atualização dos modelos e tratamento de segredos seguem [Manutenção de código e dados](00Regras.md#manutenção-de-código-e-dados).
