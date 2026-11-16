/**
 * Executa os cenários JavaScript e recusa intervalos de cobertura não exercitados.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';
import { medir } from '../Unitario/Navegador/PrincipalTeste.mjs';

const raiz = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const resultado = await medir(raiz);
for (const teste of resultado.resultados) console.log(teste.arquivo + ': ' + teste.verificacoes + ' verificações.');
for (const resumo of resultado.resumos) {
    console.log(resumo.arquivo + ': ' + resumo.cobertos + '/' + resumo.intervalos + ' intervalos V8.');
}
if (resultado.resumos.some(item => item.intervalos === 0 || item.cobertos !== item.intervalos || !item.instrumentacaoCompleta)) {
    throw Error('A cobertura JavaScript está incompleta.');
}
