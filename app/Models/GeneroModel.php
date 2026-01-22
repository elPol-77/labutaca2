<?php namespace App\Models;

use CodeIgniter\Model;

class GeneroModel extends Model
{
    protected $table = 'generos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre'];
    protected $returnType = 'array';
}