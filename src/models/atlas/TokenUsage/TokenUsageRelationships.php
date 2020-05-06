<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\TokenUsage;
use Lsia\Atlas\Models\Token\Token;

use Atlas\Mapper\MapperRelationships;

class TokenUsageRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('token', Token::class, ['token_id' => 'id']);
    }
}
