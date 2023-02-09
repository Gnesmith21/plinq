<?php
namespace pLinq;

interface dbconninf{
    public function query(string $query);
    public function escape(string $inputs);
    public function returnArray();
}