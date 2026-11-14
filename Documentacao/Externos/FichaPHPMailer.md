# Ficha do PHPMailer

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 12/11/2026 |

## Objetivo

Identificar a cópia local do PHPMailer, seus requisitos e sua forma de carregamento sem Composer. As regras gerais de organização e integração de bibliotecas e recursos de terceiros estão em [Externos](../05Externos.md).

## Identificação

Os caminhos internos `VERSION`, `src/Exception.php`, `src/PHPMailer.php` e `src/SMTP.php` identificam arquivos existentes, relativos a `Externos/PHPMailer-7.1.1`. Os links desta ficha são relativos ao próprio documento.

| Campo | Conteúdo |
| --- | --- |
| Nome | PHPMailer |
| Versão | 7.1.1, identificada em `VERSION` |
| Origem | Repositório oficial, [versão v7.1.1](https://github.com/PHPMailer/PHPMailer/tree/v7.1.1); a referência distinta do changelog está em [Conferência da cópia](#conferência-da-cópia) |
| Licença | LGPL 2.1, identificada como `LGPL-2.1-only` nos metadados do fornecedor; termos em [LICENSE](../../Externos/PHPMailer-7.1.1/LICENSE) e compromisso complementar em [COMMITMENT](../../Externos/PHPMailer-7.1.1/COMMITMENT). Condições em [Distribuição](#distribuição) |
| Justificativa | Pendente: registrar a justificativa de uso no projeto |
| Finalidade | Biblioteca para criação e envio de mensagens de e-mail |
| Integração | Não implementada: a biblioteca está em `Externos/PHPMailer-7.1.1`, mas a aplicação ainda não carrega suas classes |
| Requisitos | PHP >=5.5.0 e extensões `ctype`, `filter` e `hash`; OpenSSL para SMTP com TLS. O mínimo de PHP do projeto prevalece, conforme [Requisitos de execução](../03Configuracoes.md#requisitos-de-execução) |
| Inclusão | Pendente: registrar a inclusão no projeto |
| Alterações locais | Nenhuma nos arquivos do fornecedor em relação às referências conferidas abaixo |
| Verificação | Comparar os arquivos com as referências oficiais identificadas em [Conferência da cópia](#conferência-da-cópia) e testar a integração quando implementada |

## Carregamento sem Composer

O projeto não usa Composer. O `composer.json` de `Externos/PHPMailer-7.1.1` é um arquivo original do PHPMailer com metadados do fornecedor; sua presença não instala dependências nem carrega classes.

Para SMTP, o [carregamento manual documentado pelo fornecedor](https://github.com/PHPMailer/PHPMailer/blob/v7.1.1/README.md#installation--loading) inclui os arquivos existentes `src/Exception.php`, `src/PHPMailer.php` e `src/SMTP.php`. Suas declarações usam o espaço de nomes `PHPMailer\PHPMailer`. O carregamento DEVE ser explícito ou registrado no mecanismo próprio da aplicação; esse mecanismo ainda não está implementado.

O código próprio que utiliza a biblioteca DEVE seguir [Integração com a aplicação](../05Externos.md#integração-com-a-aplicação). Parâmetros de conexão e credenciais pertencem à configuração; a integração DEVE ser testada quanto ao envio e às falhas.

## Distribuição

Ao distribuir o conjunto, cumprir o [LICENSE](../../Externos/PHPMailer-7.1.1/LICENSE), o [COMMITMENT](../../Externos/PHPMailer-7.1.1/COMMITMENT) e as condições aplicáveis da seção 6 da LGPL 2.1:

- Preservar os fontes, os avisos de autoria e os termos da biblioteca, identificando o uso do PHPMailer sob LGPL 2.1.
- Manter a possibilidade de substituição da biblioteca e as permissões exigidas de modificação para uso próprio e engenharia reversa para depuração.
- Conferir os termos de distribuição do conjunto; a seção 4 da [licença do projeto](../../LICENSE) preserva as licenças de terceiros, mas não explicita essas permissões.
- Manter o código próprio separado. Alterações na biblioteca ou no empacotamento exigem nova conferência das obrigações aplicáveis.

## Conferência da cópia

Os arquivos de código, traduções e metadados têm como referência a versão `v7.1.1`. O `changelog.md` tem como referência o [commit de master](https://github.com/PHPMailer/PHPMailer/blob/55e2a9434f041db55d047cbc52c3000ae597a97f/changelog.md), que inclui alterações posteriores; seu conteúdo não indica atualização do código instalado.

O README original descreve recursos e exemplos do fornecedor, sem comprovar funcionalidades da aplicação. Requisitos adicionais dependem do recurso efetivamente utilizado e constam nos metadados e na documentação da biblioteca.

Atualizações seguem [Atualização de ferramentas e dependências](../00Regras.md#atualização-de-ferramentas-e-dependências).
