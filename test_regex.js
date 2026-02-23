const text = `
Misalkan Ω⊂Rn adalah domain terbuka dengan batas kelas C∞. Pertimbangkan operator elliptik linier orde dua berikut

Lu=−i,j=1∑n∂i(aij(x)∂ju)+i=1∑nbi(x)∂iu+c(x)u,

dengan koefisien aij,bi,c∈C∞(Ω) dan matriks (aij(x)) bersifat positif‑definit pada Ω.

Berikan bukti eksistensi dan keunikan solusi lemah u∈H01(Ω) untuk masalah nilai batas Dirichlet

\begin{cases}
\mathcal{L}u = f & \text{di } \Omega,\\[4pt]
u = 0 & \text{di } \partial\Omega,
\end{cases}
`;

function preprocessMath(text) {
  // Catch naked block elements
  text = text.replace(/^(?!\s*\$)(?:\s*)(Lu\s*=|\\begin\{[a-z*]+\}|\\sum|\\int|\\mathcal|\\lim|\w+\s*=\s*(?:\\sum|\\int|\\frac|-?[\w\d]*\s*[\+\-]\s*))([\s\S]*?)(?:\n\n|$)/gm, (match) => {
    if (match.includes('$$')) return match;
    return '\n$$\n' + match.trim() + '\n$$\n\n';
  });
  return text;
}

console.log(preprocessMath(text));
