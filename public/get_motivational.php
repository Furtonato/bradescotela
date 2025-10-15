<?php
header('Content-Type: application/json; charset=utf-8');

// Lista de frases motivacionais em português
$quotes = [
  "Não desista até se orgulhar.",
  "O sucesso é a soma de pequenos esforços repetidos dia após dia.",
  "Acredite em você e todo o resto virá naturalmente.",
  "A única limitação é aquela que você impõe a si mesmo.",
  "Grandes conquistas começam com a decisão de tentar.",
  "Cada dia é uma nova oportunidade para recomeçar.",
  "Você é capaz de coisas incríveis — basta acreditar.",
  "Transforme seus sonhos em metas e suas metas em realidade.",
  "Não espere por oportunidades, crie-as.",
  "O maior risco é não arriscar nada."
];

// Escolhe aleatoriamente
$phrase = $quotes[array_rand($quotes)];

// Retorna em JSON
echo json_encode(['phrase' => $phrase]);
