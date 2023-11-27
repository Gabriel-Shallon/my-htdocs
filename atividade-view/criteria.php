<?php

require_once 'api/Criteria.php';

$criteria = new Criteria;
$criteria->add('descricao','=','Suco de Laranja');
$criteria->add('estoque','>','50','OR');
print $criteria->dump() . "<br />";

$criteria = new Criteria;
$criteria->add('idade','IN', array(36,37,38));
$criteria->add('idade','NOT IN', array(10,11,12));
print $criteria->dump() . "<br />";

$criteria = new Criteria;
$criteria->add('nome', 'like', 'Carlos%');
$criteria->add('nome', 'like', 'Elen%', 'or');
$criteria->add('nome', 'like', 'Elen%', 'or');
$criteria->add('nome', 'like', 'Elen%', 'or');
print $criteria->dump() . "<br />";

$criteria = new Criteria;$criteria->add('telefone', 'IS NOT', NULL);
$criteria->add('sexo', '=', 'M');
print $criteria->dump() . "<br />";

$criteria = new Criteria;
$criteria->add('UF', 'IN', array('MT', 'MS', 'GO'));
$criteria->add('UF', 'NOT IN', array('DF', 'SP'));
print $criteria->dump() . "<br />";