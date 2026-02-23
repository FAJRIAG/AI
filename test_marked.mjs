import { marked } from 'marked';
import markedKatex from 'marked-katex-extension';

marked.use(markedKatex({
  throwOnError: false,
  output: 'html'
}));

const text = `
Misalkan H adalah ruang Hilbert real. Diberikan operator linear
T : H -> H yang didefinisikan oleh
\\[
(Tx)(t)=\\int_{0}^{1}k(t,s)x(s)\\,ds,\\qquad t\\in[0,1],
\\]
dengan kernel \\(k(t,s)=\\min\\{t,s\\}\\). **(a)** Tunjukkan bahwa \\(T\\) adalah operator simetris (self-adjoint).
`;

console.log(marked.parse(text));
