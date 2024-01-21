const firstVar = 5;
let secVar = 10;
var thirdVar = 15;


const nome = window.prompt("Nome: ");

const nomeAll = "Boa noite "+nome;
//ou
const nomeAll2 = `Boa noite ${nome}`;


const a = parseInt(window.prompt("Primeiro valor "));
// ou (precisa dizer que é int)
const b = +window.prompt("Segundo valor ");

const soma = a+b;

window.alert(`Soma = ${soma}`)

const objeto = {
    name: "John",
    age: 30
}