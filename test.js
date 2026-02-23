import { marked } from 'marked';
import DOMPurify from 'isomorphic-dompurify';
import { JSDOM } from 'jsdom';

marked.setOptions({ breaks: true, gfm: true });
const md = (s) => DOMPurify.sanitize(marked.parse(s || ''));

const input = "Berikut kode HTML:\n```html\n<!DOCTYPE html>\n<html>\n<head>\n    <meta charset=\"UTF-8\">\n</head>\n<body>\n    Test 123\n</body>\n</html>\n```";

const htmlOutput = md(input);
console.log("MARKDOWN OUTPUT:");
console.log(htmlOutput);

const dom = new JSDOM('<!DOCTYPE html><body><article>' + htmlOutput + '</article></body>');
const doc = dom.window.document;

const codeEl = doc.querySelector('code');
console.log("\ncode.innerHTML:");
console.log(codeEl.innerHTML);

console.log("\ncode.textContent:");
console.log(codeEl.textContent);

console.log("\ncode.innerText (simulated via textContent):");
console.log(codeEl.textContent);
