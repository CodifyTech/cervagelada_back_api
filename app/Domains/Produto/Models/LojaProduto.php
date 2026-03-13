<?php
 
 namespace App\Domains\Produto\Models;
 
 use App\Domains\Loja\Models\Loja;
 use App\Domains\Shared\Traits\TenantScope;
 use Illuminate\Database\Eloquent\Concerns\HasUlids;
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Relations\BelongsTo;
 use Illuminate\Database\Eloquent\Relations\Pivot;
 
 class LojaProduto extends Pivot
 {
     use HasFactory, HasUlids, TenantScope;
 
     protected $table = 'loja_produtos';
 
     protected $fillable = [
         'loja_id',
         'produto_id',
         'preco',
         'preco_promocional',
         'estoque',
         'destaque',
         'ativo',
     ];
 
     protected $casts = [
         'preco' => 'decimal:2',
         'preco_promocional' => 'decimal:2',
         'estoque' => 'decimal:3',
         'destaque' => 'boolean',
         'ativo' => 'boolean',
     ];
 
     public function loja(): BelongsTo
     {
         return $this->belongsTo(Loja::class);
     }
 
     public function produto(): BelongsTo
     {
         return $this->belongsTo(Produto::class);
     }
 }
