<?php
namespace App\Command\Util;

interface ImporterInterface{
    public function import(\Iterator $records);
}