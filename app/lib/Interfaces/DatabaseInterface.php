<?php

namespace app\lib\Interfaces;

interface DatabaseInterface{

    public function __construct(array $conig); 

    public function getCon();

    public function delete(string $tablename,string $fieldvalue,string $field='id');

    public function find(string $tablename,string $fielvalue,string $field='id');

    public function insert(string $tablename,array $fields);

    public function update(string $tablename,array $fields,$id,$field='id');

    public function orderBy(string $field,$mode='asc') : object;

    public function getAll(string $tablename,$limit='');
}