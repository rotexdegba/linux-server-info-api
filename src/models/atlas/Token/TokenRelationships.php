<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\Token;

use Atlas\Mapper\MapperRelationships;
use Lsia\Atlas\Models\TokenUsage\TokenUsage;

class TokenRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->oneToMany('usages', TokenUsage::class, ['id' => 'token_id'])
             ->onDeleteCascade();
    }
}
