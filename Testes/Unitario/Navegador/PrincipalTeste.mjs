/**
 * Confere cronômetro, atalhos, som, envio, compartilhamento e instalação com dependências controladas.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */
import assert from 'node:assert/strict';
import { readFile, mkdir, writeFile } from 'node:fs/promises';
import vm from 'node:vm';
import { Session } from 'node:inspector';
import { promisify } from 'node:util';

export async function executar(raiz, arquivo = 'Principal.js', contextos = []) {
    const fonte = await readFile(raiz + '/Publico/Ativos/JavaScript/' + arquivo, 'utf8');
    let verificacoes = 0;
    const conferir = (atual, esperado) => { assert.deepEqual(atual, esperado); verificacoes++; };
    function preparar(opcoes = {}) {
        const eventos = {};
        const eventosJanela = {};
        const estado = { agora: 0, envios: 0, cliques: 0, sons: 0, atributos: {}, classes: new Set(), quadro: null };
        const contador = { dataset: { restante: '45000', alerta: '10', segundos: '45' } };
        const mensagem = { textContent: '', classList: opcoes.classeAusente ? undefined : { add: nome => estado.classes.add(nome) } };
        const botaoSom = { setAttribute: (nome, valor) => estado.atributos[nome] = valor };
        const elementos = {
            '[data-restante]': opcoes.jogo === false ? null : contador,
            '[data-digitos]': { textContent: '' },
            '[data-pavio]': { style: {} },
            '[data-expiracao]': { requestSubmit: () => estado.envios++ },
            '[data-mensagem]': opcoes.mensagemAusente ? null : mensagem,
            '[data-som]': opcoes.botaoSom ? botaoSom : null,
            '[data-alternativa="0"]': { click: () => estado.cliques++ },
            '[data-alternativa="1"]': { click: () => estado.cliques++ }
        };
        const armazenamento = {
            getItem() { if (opcoes.leituraFalha) throw Error('Armazenamento indisponível.'); return opcoes.som ?? 'nao'; },
            setItem() { if (opcoes.escritaFalha) throw Error('Armazenamento indisponível.'); }
        };
        class ContextoAudio {
            currentTime = 0;
            constructor() { if (opcoes.audioFalha) throw Error('Áudio indisponível.'); }
            createOscillator() { return { frequency: {}, connect() {}, start: () => estado.sons++, stop() {} }; }
            createGain() { return { gain: { setValueAtTime() {}, exponentialRampToValueAtTime() {} }, connect() {} }; }
        }
        const janela = { location: { reload: () => estado.recarregamentos = (estado.recarregamentos ?? 0) + 1 }, addEventListener: (nome, acao) => eventosJanela[nome] = acao };
        if (opcoes.audio) janela.AudioContext = ContextoAudio;
        if (opcoes.audioAlternativo) janela.webkitAudioContext = ContextoAudio;
        const navegador = {};
        if (opcoes.copiar) navegador.clipboard = { writeText: opcoes.copiar };
        const documento = {
            body: { dataset: { prefixo: '/' }, classList: { toggle: (nome, ativo) => ativo ? estado.classes.add(nome) : estado.classes.delete(nome) } },
            querySelector: seletor => elementos[seletor] ?? null,
            addEventListener: (nome, acao) => eventos[nome] = acao
        };
        const contexto = vm.createContext({ document: documento, window: janela, navigator: navegador, performance: { now: () => estado.agora }, localStorage: armazenamento, requestAnimationFrame: acao => estado.quadro = acao });
        contextos.push(contexto);
        vm.runInContext(fonte, contexto, { filename: arquivo + '?cenario=' + contextos.length + '-' + Math.random() });
        return { eventos, eventosJanela, estado, elementos, mensagem, botaoSom };
    }
    const tecla = (pessoa, key, extras = {}) => pessoa.eventos.keydown({ key, target: { tagName: 'BODY' }, preventDefault() {}, ...extras });
    const clicar = async (pessoa, tipo, resultado = 'Resultado fictício') => {
        const elemento = { dataset: { resultado }, hasAttribute: nome => nome === tipo, setAttribute: (nome, valor) => pessoa.estado.atributos[nome] = valor };
        await pessoa.eventos.click({ target: { closest: () => elemento } });
    };
    const pessoa = preparar({ botaoSom: true });
    pessoa.eventosJanela.pageshow({ persisted: false });
    conferir(pessoa.estado.recarregamentos ?? 0, 0);
    pessoa.eventosJanela.pageshow({ persisted: true });
    conferir(pessoa.estado.recarregamentos, 1);
    conferir(pessoa.elementos['[data-digitos]'].textContent, '45');
    pessoa.estado.agora = 36000; pessoa.estado.quadro();
    conferir(pessoa.elementos['[data-digitos]'].textContent, '09');
    conferir(pessoa.estado.classes.has('perigo'), true);
    pessoa.estado.agora = 43000; pessoa.estado.quadro();
    conferir(pessoa.estado.classes.has('tremor'), true);
    pessoa.estado.agora = 45000; pessoa.estado.quadro();
    conferir(pessoa.estado.envios, 1);
    tecla(pessoa, 'A'); tecla(pessoa, '2');
    conferir(pessoa.estado.cliques, 2);
    for (const extras of [{ctrlKey:true}, {metaKey:true}, {altKey:true}, {repeat:true}, {target:{tagName:'INPUT'}}, {target:{tagName:'TEXTAREA'}}]) tecla(pessoa, 'A', extras);
    for (const valor of ['Escape', '', 'e', '9']) tecla(pessoa, valor);
    conferir(pessoa.estado.cliques, 2);
    const formulario = { dataset: {} };
    let recusas = 0;
    pessoa.eventos.submit({ target: formulario, preventDefault: () => recusas++ });
    pessoa.eventos.submit({ target: formulario, preventDefault: () => recusas++ });
    conferir(recusas, 1);
    await pessoa.eventos.click({ target: { closest: () => null } });
    const pagina = preparar({ jogo: false, leituraFalha: true });
    tecla(pagina, 'A');
    await clicar(pagina, 'data-instalar');
    conferir(pagina.mensagem.textContent.includes('Adicionar'), true);
    let pedidos = 0;
    pagina.eventosJanela.beforeinstallprompt({ preventDefault() {}, prompt: async () => pedidos++, userChoice: Promise.resolve({outcome:'accepted'}) });
    await clicar(pagina, 'data-instalar');
    conferir(pedidos, 1);
    pagina.eventosJanela.beforeinstallprompt({ preventDefault() {}, prompt: async () => { throw Error('Recusa simulada.'); } });
    await clicar(pagina, 'data-instalar');
    conferir(pagina.mensagem.textContent.includes('Não foi possível'), true);
    await clicar(pagina, 'data-resultado');
    conferir(pagina.mensagem.textContent, 'Resultado fictício');
    const copiar = preparar({ jogo: false, copiar: async texto => conferir(texto, 'Resultado fictício'), classeAusente: true });
    await clicar(copiar, 'data-resultado');
    conferir(copiar.mensagem.textContent, 'Resultado copiado.');
    const recusaCopia = preparar({ jogo: false, copiar: async () => { throw Error('Recusa simulada.'); } });
    await clicar(recusaCopia, 'data-resultado');
    conferir(recusaCopia.mensagem.textContent, 'Resultado fictício');
    const semMensagem = preparar({ jogo: false, mensagemAusente: true });
    await clicar(semMensagem, 'data-instalar');
    const audio = preparar({ audio: true });
    await clicar(audio, 'data-som');
    conferir(audio.estado.sons, 1);
    audio.estado.agora = 36000; audio.estado.quadro();
    conferir(audio.estado.sons, 2);
    audio.estado.quadro();
    conferir(audio.estado.sons, 2);
    await clicar(audio, 'data-som');
    conferir(audio.estado.atributos['aria-pressed'], 'false');
    await clicar(preparar({ audioAlternativo: true }), 'data-som');
    const semAudio = preparar({ som: 'sim', botaoSom: true });
    semAudio.estado.agora = 36000; semAudio.estado.quadro();
    await clicar(semAudio, 'data-som');
    await clicar(semAudio, 'data-som');
    conferir(semAudio.estado.sons, 0);
    const audioPersistente = preparar({ audio: true, som: 'sim' });
    audioPersistente.estado.agora = 36000; audioPersistente.estado.quadro();
    conferir(audioPersistente.estado.sons, 1);
    const falhaInicial = preparar({ audio: true, som: 'sim', audioFalha: true });
    conferir(falhaInicial.mensagem.textContent.includes('som'), true);
    const falhaAudio = preparar({ audio: true, audioFalha: true });
    await clicar(falhaAudio, 'data-som');
    conferir(falhaAudio.mensagem.textContent.includes('som'), true);
    const falhaGravacao = preparar({ escritaFalha: true });
    await clicar(falhaGravacao, 'data-som');
    conferir(falhaGravacao.mensagem.textContent.includes('som'), true);
    return { arquivo, verificacoes };
}

export async function medir(raiz) {
    const sessao = new Session();
    sessao.connect();
    const enviar = promisify(sessao.post.bind(sessao));
    await enviar('Profiler.enable');
    await enviar('Profiler.startPreciseCoverage', {callCount:true,detailed:true});
    const resultados = [];
    const contextos = [];
    for (const arquivo of ['Principal.js','Principal.min.js']) resultados.push(await executar(raiz, arquivo, contextos));
    const { result } = await enviar('Profiler.takePreciseCoverage');
    contextos.length = 0;
    await enviar('Profiler.stopPreciseCoverage');
    sessao.disconnect();
    const scripts = result.filter(item => ['Principal.js','Principal.min.js'].includes(item.url.split('?')[0]));
    const resumos = [];
    for (const arquivo of ['Principal.js','Principal.min.js']) {
        const grupo = scripts.filter(item => item.url.split('?')[0] === arquivo);
        const funcoes = new Map();
        for (const script of grupo) for (const funcao of script.functions) {
            const chave = funcao.ranges[0].startOffset + ':' + funcao.ranges[0].endOffset;
            if (!funcoes.has(chave)) funcoes.set(chave, []);
            funcoes.get(chave).push(funcao);
        }
        let total = 0;
        let cobertos = 0;
        const ausentes = [];
        for (const [chave, versoes] of funcoes) {
            const intervalos = new Map();
            for (const versao of versoes) for (const intervalo of versao.ranges) intervalos.set(intervalo.startOffset+':'+intervalo.endOffset, intervalo);
            for (const intervalo of intervalos.values()) {
                let execucoes = 0;
                for (const versao of versoes) {
                    const correspondentes = versao.ranges.filter(item => item.startOffset <= intervalo.startOffset && item.endOffset >= intervalo.endOffset).sort((primeiro,segundo) => (primeiro.endOffset-primeiro.startOffset)-(segundo.endOffset-segundo.startOffset));
                    execucoes += correspondentes[0]?.count ?? 0;
                }
                total++;
                if (execucoes > 0) cobertos++;
                else ausentes.push({funcao:chave,inicio:intervalo.startOffset,fim:intervalo.endOffset});
            }
        }
        const instrumentacaoCompleta = [...funcoes.values()].every(versoes => versoes.some(versao => versao.isBlockCoverage && versao.ranges[0].count > 0));
        resumos.push({arquivo,intervalos:total,cobertos,ausentes,instrumentacaoCompleta});
    }
    await mkdir(raiz+'/Dados/Arquivos/Cache/CoberturaJavaScript',{recursive:true});
    await writeFile(raiz+'/Dados/Arquivos/Cache/CoberturaJavaScript/Relatorio.json',JSON.stringify({resultados,resumos,scripts},null,2));
    return {resultados,resumos};
}
