import { marked } from 'marked';
import markedKatex from 'marked-katex-extension';

marked.use(markedKatex({
  throwOnError: false,
  output: 'html'
}));

const text = `
(Tx)(t)=\\int_{0}^{1}k(t,s)x(s)\\,ds,\\qquad t\\in[0,1], $$ dengan kernel $k(t,s)=\\min\\{t,s\\}$. **(a)** Tunjukkan bahwa $T$ adalah operator simetris (self-adjoint). **(b)** Tentukan spektrum $
\\sigma(T) $. #### Jawaban **(a)** Untuk $x,y\\in H$
`;

console.log(marked.parse(text));
